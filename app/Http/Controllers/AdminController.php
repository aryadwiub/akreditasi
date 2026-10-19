<?php

namespace App\Http\Controllers;

use App\Support\BerkasSk;
use App\Support\StatusInternasional;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $fakultas = DB::table('fakultas')->orderBy('nm_fakultas')->get(['id_fakultas', 'nm_fakultas']);

        return view(
            'backend/dashboard',
            ['judul' => 'Akreditasi Universitas', 'fakultas' => $fakultas] + $this->petaSebaran()
        );
    }

    // unduh peta sebaran (Excel / PDF), bisa difilter per fakultas
    public function unduhPeta(Request $request)
    {
        $format = $request->query('format');
        if (!in_array($format, ['xlsx', 'pdf'], true)) {
            abort(404);
        }

        $idFakultas = $request->filled('fakultas') ? (int) $request->query('fakultas') : null;
        $namaFakultas = $idFakultas ? DB::table('fakultas')->where('id_fakultas', $idFakultas)->value('nm_fakultas') : null;
        if ($idFakultas && !$namaFakultas) {
            abort(404);
        }

        // status prodi: '' = semua, 'aktif', 'tidak-aktif'
        $status = in_array($request->query('status'), ['aktif', 'tidak-aktif'], true) ? $request->query('status') : null;

        $data = $this->petaSebaran($idFakultas, $status) + [
            'namaFakultas' => $namaFakultas ?? 'Semua Fakultas',
            'namaStatus' => ['aktif' => 'Prodi Aktif', 'tidak-aktif' => 'Prodi Tidak Aktif'][$status] ?? 'Semua Prodi',
            'dicetak' => now('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB',
        ];
        $namaFile = 'peta-sebaran-akreditasi-' . \Illuminate\Support\Str::slug($namaFakultas ?? 'semua-fakultas')
            . ($status ? '-' . $status : '') . '-' . now('Asia/Jakarta')->format('Ymd') . '.' . $format;

        return $format === 'xlsx'
            ? \App\Support\EksporPeta::excel($data, $namaFile)
            : \App\Support\EksporPeta::pdf($data, $namaFile);
    }

    // peta sebaran: kode peringkat yang berlaku tiap prodi, tahun ini -5 s/d +5
    private function petaSebaran(?int $idFakultas = null, ?string $status = null): array
    {
        $tahunIni = (int) now('Asia/Jakarta')->format('Y');
        $tahun = range($tahunIni - 5, $tahunIni + 5);

        $prodi = DB::table('prodi as a')
            ->select('a.id_prodi', 'a.id_fakultas', 'a.nama_prodi', 'b.nama_jenjang', 'c.nm_fakultas', DB::raw('a.status = 1 as aktif'))
            ->leftJoin('jenjang as b', 'a.id_jenjang', '=', 'b.id_jenjang')
            ->leftJoin('fakultas as c', 'a.id_fakultas', '=', 'c.id_fakultas')
            ->when($status === 'aktif', fn ($q) => $q->where('a.status', '=', 1))
            ->when($status === 'tidak-aktif', fn ($q) => $q->where('a.status', '!=', 1))
            ->when($idFakultas, fn ($q) => $q->where('a.id_fakultas', '=', $idFakultas))
            ->orderBy('c.nm_fakultas')->orderBy('b.id_jenjang')->orderBy('a.nama_prodi')
            ->get();

        $riwayat = DB::table('riwayat_akreditasi')
            ->select('id_prodi', 'akreditasi', 'lembaga', 'start_date', 'end_date')
            ->whereIn('id_prodi', $prodi->pluck('id_prodi'))
            ->whereNotNull('end_date')
            ->get()
            ->map(function ($r) {
                // tanggal berlaku kosong: dihitung 5 tahun sebelum tanggal berakhir (masa berlaku umum akreditasi)
                $r->mulai = $r->start_date ?? \Carbon\Carbon::parse($r->end_date)->subYears(5)->addDay()->toDateString();
                return $r;
            })
            ->groupBy('id_prodi');

        $peta = $prodi->map(function ($p) use ($riwayat, $tahun) {
            $sk = $riwayat->get($p->id_prodi, collect());
            $p->sel = [];
            foreach ($tahun as $t) {
                // SK yang berlaku (sebagian) di tahun $t; bila lebih dari satu, ambil yang terbaru
                $p->sel[$t] = $sk->filter(fn ($r) => $r->mulai <= "$t-12-31" && $r->end_date >= "$t-01-01")
                    ->sortByDesc(fn ($r) => $r->mulai . $r->end_date)
                    ->first();
            }
            return $p;
        });

        $peringkat = DB::table('peringkat')->orderBy('urutan')->pluck('akreditasi', 'kode');
        // warna sel = lembaga akreditasi
        $lembaga = \App\Support\EksporPeta::warnaLembaga(
            DB::table('jenis_akreditasi')->orderBy('id_jenis_akreditasi')->pluck('nm_akreditasi')
        );

        return compact('peta', 'tahun', 'tahunIni', 'peringkat', 'lembaga');
    }

    public function fakultas()
    {
        $fakultas = DB::table('fakultas')
            ->get();

        return view(
            'backend/fakultas',
            ['judul' => 'Akreditasi Universitas'],
            compact('fakultas')
        );
    }

    public function prodi($id_fakultas)
    {
        $prodi = DB::table('prodi as a')
            ->select('a.id_prodi', 'c.nama_jenjang', 'a.nama_prodi', 'nm_fakultas', 'a.status')
            ->join('fakultas as b', 'a.id_fakultas', '=', 'b.id_fakultas')
            ->join('jenjang as c', 'a.id_jenjang', '=', 'c.id_jenjang')
            ->where('a.id_fakultas', '=', $id_fakultas)
            ->orderBy('c.nama_jenjang', 'asc')
            ->get();

        return view(
            'backend/prodi',
            ['judul' => 'Akreditasi Universitas'],
            compact('prodi')
        );
    }

    public function prodidetail($id_prodi)
    {
        $detail = DB::table('prodi')
            ->select(
                'prodi.id_prodi',
                'prodi.id_fakultas',
                'prodi.nama_prodi',
                'fakultas.nm_fakultas',
                'jenjang.nama_jenjang',
                'peringkat.akreditasi',
                'riwayat_akreditasi.url',
                'riwayat_akreditasi.end_date',
                'riwayat_akreditasi.lembaga',
                'prodi.status',
                'riwayat_akreditasi_internasional_series.url as urlinter',
                'riwayat_akreditasi_internasional_series.tgl_berakhir',
                'nama_lembaga_internasional.nama_lembaga',
                DB::raw('(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = 3 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "1"
                        WHEN riwayat_akreditasi_internasional_series.lembaga = 4 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "1"
                        WHEN riwayat_akreditasi_internasional_series.lembaga = 6 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "1"
                          END) AS tgl_akhir'),
                DB::raw('(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = 3 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "ASIIN"
                        WHEN riwayat_akreditasi_internasional_series.lembaga = 4 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "FIBAA"
                        WHEN riwayat_akreditasi_internasional_series.lembaga = 6 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "RSC" END) AS internasional')
            )
            ->leftJoin('riwayat_akreditasi', 'prodi.id_prodi', '=', 'riwayat_akreditasi.id_prodi')
            ->leftJoin('peringkat', 'riwayat_akreditasi.akreditasi', '=', 'peringkat.kode')
            ->leftJoin('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
            ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id_fakultas')
            ->leftJoin('riwayat_akreditasi_international', 'prodi.id_prodi', '=', 'riwayat_akreditasi_international.id_prodi')
            ->leftJoin('riwayat_akreditasi_internasional_series', 'prodi.id_prodi', '=', 'riwayat_akreditasi_internasional_series.id_prodi')
            ->leftJoin('nama_lembaga_internasional', 'riwayat_akreditasi_internasional_series.lembaga', '=', 'nama_lembaga_internasional.id_akre')

            ->where('prodi.id_prodi', '=', $id_prodi)
            ->orderBy('fakultas.nm_fakultas', 'ASC')
            ->orderBy('riwayat_akreditasi_internasional_series.lembaga')
            ->groupBy('prodi.id_prodi')
            ->first();

        if (!$detail) {
            abort(404);
        }

        $riwayat_nasional = DB::table('riwayat_akreditasi')
            ->where('id_prodi', '=', $id_prodi)
            ->get();
        // dd($riwayat_nasional);

        $akreditasi = DB::table('jenis_akreditasi')
            ->get();

        $peringkat = DB::table('peringkat')
            ->orderBy('urutan', 'asc')
            ->get();

        $riwayat_internasional = DB::table('riwayat_akreditasi_internasional_series as a')
            ->join('nama_lembaga_internasional as b', 'a.lembaga', '=', 'b.id_akre')
            ->where('a.id_prodi', '=', $id_prodi)
            ->get();

        $lembaga_internasional = DB::table('nama_lembaga_internasional')
            ->get();

        return view(
            'backend/prodidetail',
            ['judul' => 'Akreditasi Universitas'],
            compact('detail', 'riwayat_nasional', 'akreditasi', 'peringkat', 'riwayat_internasional', 'lembaga_internasional')
        );
    }

    public function simpandata(Request $request)
    {

        $id_prodi = $request->prodi;
        $this->validasiBerkas($request, wajib: true);

        $path = $request->hasFile('berkas') ? BerkasSk::simpan($request->file('berkas')) : null;

        $form = DB::table('riwayat_akreditasi')
            ->insert([
                'id_prodi' => $request->prodi,
                'akreditasi' => $request->akreditasi,
                'no_sk_akreditasi' => $request->no_sk,
                'start_date' => $request->tgl_berlaku,
                'end_date' => $request->tgl_berakhir,
                'update_date' => \Carbon\Carbon::now(),
                'url' => $request->url ?: null,
                'file' => $path,
                'lembaga' => $request->lembaga,
                'status' => $request->status
            ]);
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    public function ubah($id_riwayat_akreditasi)
    {
        // return $id_riwayat_akreditasi;
        $ambil = DB::table('riwayat_akreditasi as a')
            ->select(
                'a.id_riwayat_akreditasi',
                'a.id_prodi',
                'a.akreditasi',
                'c.kode',
                'c.akreditasi as akre',
                'a.no_sk_akreditasi',
                'a.start_date',
                'a.end_date',
                'a.url',
                'a.file',
                'a.lembaga',
                'a.status'
            )
            ->leftJoin('peringkat as c', 'a.akreditasi', '=', 'c.kode')
            ->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)
            ->first();

        if (!$ambil) {
            abort(404);
        }
        // dd($ambil);
        $lembaga = DB::table('jenis_akreditasi')
            ->get();

        $peringkat = DB::table('peringkat')
            ->orderBy('urutan', 'asc')
            ->get();

        return view(
            'backend/ubahakre',
            ['judul' => 'Edit Data Akreditasi Nasional'],
            compact('ambil', 'lembaga', 'peringkat')
        );
    }

    public function ubahsimpan(Request $request, $id_riwayat_akreditasi)
    {
        $id_prodi = $request->prodi;
        $lama = DB::table('riwayat_akreditasi')->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)->first();
        if (!$lama) {
            abort(404);
        }

        $hapusFile = $request->boolean('hapus_berkas');
        $masihAdaFile = $lama->file && !$hapusFile;
        $this->validasiBerkas($request, wajib: !$masihAdaFile);

        $path = $lama->file;
        if ($request->hasFile('berkas')) {
            $path = BerkasSk::simpan($request->file('berkas'));
        } elseif ($hapusFile) {
            $path = null;
        }

        $form = DB::table('riwayat_akreditasi')
            ->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)
            ->update([
                'id_prodi' => $request->prodi,
                'akreditasi' => $request->akreditasi,
                'no_sk_akreditasi' => $request->no_sk,
                'start_date' => $request->tgl_berlaku,
                'end_date' => $request->tgl_berakhir,
                'update_date' => \Carbon\Carbon::now(),
                'url' => $request->url ?: null,
                'file' => $path,
                'lembaga' => $request->lembaga,
                'status' => $request->status
            ]);

        // file lama dibuang setelah data baru tersimpan
        if ($form && $lama->file !== $path) {
            BerkasSk::hapus($lama->file);
        }

        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi))->with('succes', 'Data sampun dirubah');
        } else {
            return "gagal simpan";
        }
    }

    public function destroy(Request $request, $id_riwayat_akreditasi)
    {
        $id_prodi = $request->prodi;
        $lama = DB::table('riwayat_akreditasi')->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)->first();
        $form = DB::table('riwayat_akreditasi')
            ->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)
            ->delete();

        if ($form) {
            BerkasSk::hapus($lama->file);
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    public function simpandatainter(Request $request)
    {
        $id_prodi = $request->prodi;
        // return($request);
        $form = DB::table('riwayat_akreditasi_internasional_series')
            ->insert([
                'id_prodi' => $request->prodi,
                'lembaga' => $request->lembaga,
                'tgl_berlaku' => $request->tgl_berlaku,
                'tgl_berakhir' => $request->tgl_berakhir,
                'url' => $request->file
            ]);
        StatusInternasional::perbarui(); // isi keterangan Aktif/Tidak Aktif
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    public function ubahinter($id_riwayat)
    {
        // return $id_riwayat_akreditasi;
        $ambil = DB::table('riwayat_akreditasi_internasional_series as a')
            ->leftJoin('nama_lembaga_internasional as b', 'a.lembaga', '=', 'b.id_akre')
            ->where('a.id_riwayat', '=', $id_riwayat)
            ->first();

        if (!$ambil) {
            abort(404);
        }
        // dd($ambil);
        $lembaga = DB::table('nama_lembaga_internasional')
            ->get();

        return view(
            'backend/ubahinter',
            ['judul' => 'Edit Data Akreditasi Internasional'],
            compact('ambil', 'lembaga')
        );
    }

    public function ubahsimpaninter(Request $request, $id_riwayat)
    {
        // dd($id_riwayat_akreditasi);
        $id_prodi = $request->prodi;
        $form = DB::table('riwayat_akreditasi_internasional_series')
            ->where('id_riwayat', '=', $id_riwayat)
            ->update([
                'id_prodi' => $request->prodi,
                'lembaga' => $request->lembaga,
                'tgl_berlaku' => $request->tgl_berlaku,
                'tgl_berakhir' => $request->tgl_berakhir,
                'url' => $request->file
            ]);
        StatusInternasional::perbarui(); // keterangan ikut berubah bila tanggal berakhir diubah
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi))->with('succes', 'Data sampun dirubah');
        } else {
            return "gagal simpan";
        }
    }

    public function destroyinter(Request $request, $id_riwayat)
    {
        // return($id_riwayat_akreditasi);
        $id_prodi = $request->prodi;
        $form = DB::table('riwayat_akreditasi_internasional_series')
            ->where('id_riwayat', '=', $id_riwayat)
            ->delete();

        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    // admin melihat file SK hasil upload (tanpa captcha, tidak dicatat di log publik)
    public function lihatfile($id_riwayat_akreditasi)
    {
        $data = DB::table('riwayat_akreditasi')
            ->where('id_riwayat_akreditasi', '=', (int) $id_riwayat_akreditasi)
            ->first();

        if (!$data || !BerkasSk::ada($data->file)) {
            abort(404);
        }

        return response()->file(BerkasSk::pathLengkap($data->file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SK-' . $data->id_riwayat_akreditasi . '.pdf"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    // link atau file upload, minimal salah satu wajib ada
    private function validasiBerkas(Request $request, bool $wajib): void
    {
        $request->validate([
            'url' => [$wajib ? 'required_without:berkas' : 'nullable', 'nullable', 'url:http,https', 'max:2000'],
            'berkas' => [$wajib ? 'required_without:url' : 'nullable', 'nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:10240'],
        ], [
            'url.required_without' => 'Isi link file atau upload file PDF.',
            'berkas.required_without' => 'Upload file PDF atau isi link file.',
            'url.url' => 'Link file harus berupa alamat http/https yang valid.',
            'berkas.mimes' => 'File harus berupa PDF.',
            'berkas.mimetypes' => 'File harus berupa PDF.',
            'berkas.max' => 'Ukuran file maksimal 10 MB.',
            'berkas.uploaded' => 'File gagal diupload (ukuran mungkin melebihi batas server).',
        ]);
    }
}
