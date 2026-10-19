<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// Rekap -> Lembar Kerja -> Prodi yang Akan Akreditasi
class LembarKerjaController extends Controller
{
    // nilai proses minimal yang dihitung "telah submit"
    public const NILAI_SUBMIT = 3;

    public function index()
    {
        $tahunIni = (int) now(LogAksesController::ZONA)->year;
        $periode = DB::table('periode_akreditasi as p')
            ->leftJoin('periode_akreditasi_prodi as pp', 'pp.id_periode', '=', 'p.id')
            ->leftJoin('proses_akreditasi as r', 'r.id', '=', 'pp.id_proses')
            ->select('p.id', 'p.tahun',
                DB::raw('count(pp.id) as jumlah'),
                DB::raw('sum(case when r.nilai >= ' . self::NILAI_SUBMIT . ' then 1 else 0 end) as submit'))
            ->groupBy('p.id', 'p.tahun')
            ->orderByDesc('p.tahun')
            ->get();

        return view('backend/lembarkerja', [
            'periode' => $periode,
            'tahunIni' => $tahunIni,
            'pilihanTahun' => $this->pilihanTahun(),
            'tahunTerpakai' => $periode->pluck('tahun')->map(fn ($t) => (int) $t)->all(),
            'prodi' => $this->prodiPilihan(),
        ]);
    }

    public function simpan(Request $request)
    {
        [$data, $prodi] = $this->validasiPeriode($request);

        DB::transaction(function () use ($data, $prodi, $request) {
            $waktu = now();
            $idPeriode = DB::table('periode_akreditasi')->insertGetId([
                'tahun' => $data['tahun'], 'dibuat_oleh' => auth()->id(), 'created_at' => $waktu, 'updated_at' => $waktu,
            ]);
            $this->tambahProdi($idPeriode, $prodi->keys(), $waktu);

            $this->catat($request, [
                'aksi' => 'tambah_periode', 'id_periode' => $idPeriode, 'tahun' => $data['tahun'],
                'keterangan' => $this->ringkas($prodi->count() . ' prodi: ' . $prodi->map(fn ($p) => $this->namaProdi($p))->implode('; ')),
            ]);
        });

        return redirect('lembarkerja')->with('sukses', 'Periode ' . $data['tahun'] . ' berhasil disimpan dengan ' . $prodi->count() . ' program studi.');
    }

    // ubah tahun dan/atau daftar prodi sebuah periode; prodi yang dihapus ikut kehilangan prosesnya
    public function ubahPeriode(Request $request, int $id)
    {
        $periode = DB::table('periode_akreditasi')->where('id', $id)->first() ?? abort(404);
        [$data, $prodi] = $this->validasiPeriode($request, $periode);

        $lama = $this->barisPeriode($id)->keyBy('id_prodi');
        $tambah = $prodi->keys()->diff($lama->keys())->values();
        $hapus = $lama->keys()->diff($prodi->keys())->values();
        $gantiTahun = (int) $data['tahun'] !== (int) $periode->tahun;

        if (!$gantiTahun && $tambah->isEmpty() && $hapus->isEmpty()) {
            return redirect('lembarkerja/' . $id)->with('sukses', 'Tidak ada perubahan periode.');
        }

        DB::transaction(function () use ($id, $periode, $data, $prodi, $lama, $tambah, $hapus, $gantiTahun, $request) {
            $waktu = now();
            DB::table('periode_akreditasi')->where('id', $id)->update(['tahun' => $data['tahun'], 'updated_at' => $waktu]);
            $this->tambahProdi($id, $tambah, $waktu);
            if ($hapus->isNotEmpty()) {
                DB::table('periode_akreditasi_prodi')->where('id_periode', $id)->whereIn('id_prodi', $hapus->all())->delete();
            }

            $keterangan = array_filter([
                $gantiTahun ? 'Tahun ' . $periode->tahun . ' → ' . $data['tahun'] : null,
                $tambah->isNotEmpty() ? 'Ditambah ' . $tambah->count() . ' prodi: ' . $tambah->map(fn ($p) => $this->namaProdi($prodi[$p]))->implode('; ') : null,
                $hapus->isNotEmpty() ? 'Dihapus ' . $hapus->count() . ' prodi: ' . $hapus->map(fn ($p) => $this->namaProdi($lama[$p])
                    . ($lama[$p]->nama_proses ? ' (proses: ' . $lama[$p]->nama_proses . ')' : ''))->implode('; ') : null,
            ]);
            $this->catat($request, [
                'aksi' => 'ubah_periode', 'id_periode' => $id, 'tahun' => $data['tahun'],
                'keterangan' => $this->ringkas(implode(' | ', $keterangan)),
            ]);
        });

        return redirect('lembarkerja/' . $id)->with('sukses', 'Periode ' . $data['tahun'] . ' berhasil diubah'
            . ($tambah->isNotEmpty() ? ', ' . $tambah->count() . ' prodi ditambah' : '')
            . ($hapus->isNotEmpty() ? ', ' . $hapus->count() . ' prodi dihapus' : '') . '.');
    }

    public function hapus(Request $request, int $id)
    {
        $periode = DB::table('periode_akreditasi')->where('id', $id)->first() ?? abort(404);
        $baris = $this->barisPeriode($id);

        DB::transaction(function () use ($id, $periode, $baris, $request) {
            DB::table('periode_akreditasi_prodi')->where('id_periode', $id)->delete();
            DB::table('periode_akreditasi')->where('id', $id)->delete();

            $berproses = $baris->filter(fn ($r) => $r->nama_proses);
            $this->catat($request, [
                'aksi' => 'hapus_periode', 'id_periode' => $id, 'tahun' => $periode->tahun,
                'keterangan' => $this->ringkas($baris->count() . ' prodi, ' . $berproses->count() . ' sudah berproses'
                    . ($berproses->isNotEmpty() ? ': ' . $berproses->map(fn ($r) => $this->namaProdi($r) . ' (' . $r->nama_proses . ')')->implode('; ') : '')),
            ]);
        });

        return redirect('lembarkerja')->with('sukses', 'Periode ' . $periode->tahun . ' beserta ' . $baris->count() . ' prodi di dalamnya berhasil dihapus.');
    }

    public function lihat(int $id)
    {
        $periode = DB::table('periode_akreditasi')->where('id', $id)->first() ?? abort(404);

        $baris = DB::table('periode_akreditasi_prodi as pp')
            ->join('prodi as a', 'a.id_prodi', '=', 'pp.id_prodi')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('fakultas as e', 'a.id_fakultas', '=', 'e.id_fakultas')
            ->leftJoin('proses_akreditasi as r', 'r.id', '=', 'pp.id_proses')
            ->leftJoin('users as u', 'u.id', '=', 'pp.diubah_oleh')
            ->select('pp.id', 'pp.id_prodi', 'pp.id_proses', 'pp.updated_at', 'a.nama_prodi', 'd.nama_jenjang', 'e.nm_fakultas',
                'r.nilai', DB::raw('coalesce(u.nama, u.username) as diubah_oleh'))
            ->where('pp.id_periode', $id)
            ->orderBy('e.nm_fakultas')->orderBy('a.id_jenjang')->orderBy('a.nama_prodi')
            ->get();

        // akreditasi nasional terakhir tiap prodi: SK aktif (status 1) diutamakan, lalu end_date terbaru
        $akreditasi = DB::table('riwayat_akreditasi as b')
            ->leftJoin('peringkat as c', 'b.akreditasi', '=', 'c.kode')
            ->select('b.id_prodi', 'c.akreditasi', 'b.lembaga', 'b.no_sk_akreditasi', 'b.end_date')
            ->whereIn('b.id_prodi', $baris->pluck('id_prodi')->all())
            ->orderByDesc('b.status')->orderByDesc('b.end_date')
            ->get()
            ->unique('id_prodi')
            ->keyBy('id_prodi');
        $baris->each(function ($r) use ($akreditasi) {
            $a = $akreditasi->get($r->id_prodi);
            $r->akreditasi = $a?->akreditasi;
            $r->lembaga = $a?->lembaga;
            $r->no_sk = $a?->no_sk_akreditasi;
            $r->end_date = $a?->end_date;
        });

        $nama = DB::table('proses_akreditasi')->pluck('nama_proses', 'id');

        return view('backend/lembarkerja_detail', [
            'periode' => $periode,
            'baris' => $baris,
            'proses' => DB::table('proses_akreditasi')->orderBy('nilai')->orderBy('id')->get(),
            'submit' => $baris->filter(fn ($r) => $r->nilai !== null && $r->nilai >= self::NILAI_SUBMIT)->count(),
            // untuk form Ubah Periode
            'prodi' => $this->prodiPilihan($id),
            'pilihanTahun' => collect($this->pilihanTahun())->push((int) $periode->tahun)->unique()->sort()->values()->all(),
            'tahunTerpakai' => DB::table('periode_akreditasi')->where('id', '!=', $id)->pluck('tahun')->map(fn ($t) => (int) $t)->all(),
            'prosesProdi' => $baris->filter(fn ($r) => $r->id_proses)->mapWithKeys(fn ($r) => [$r->id_prodi => $nama[$r->id_proses] ?? ''])->all(),
        ]);
    }

    public function simpanProses(Request $request, int $id)
    {
        $periode = DB::table('periode_akreditasi')->where('id', $id)->first() ?? abort(404);
        $data = $request->validate([
            'proses' => ['required', 'array'],
            'proses.*' => ['nullable', 'integer', Rule::exists('proses_akreditasi', 'id')],
        ]);

        $proses = DB::table('proses_akreditasi')->get()->keyBy('id');
        $label = fn ($idProses) => $idProses && isset($proses[$idProses]) ? $proses[$idProses]->nama_proses . ' (' . $proses[$idProses]->nilai . ')' : null;

        // hanya baris milik periode ini yang nilainya benar-benar berubah
        $lama = DB::table('periode_akreditasi_prodi as pp')
            ->join('prodi as a', 'a.id_prodi', '=', 'pp.id_prodi')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->select('pp.id', 'pp.id_prodi', 'pp.id_proses', 'a.nama_prodi', 'd.nama_jenjang')
            ->where('pp.id_periode', $id)
            ->whereIn('pp.id', array_map('intval', array_keys($data['proses'])))
            ->get();
        $berubah = $lama->filter(fn ($r) => (int) $r->id_proses !== (int) ($data['proses'][$r->id] ?? 0));

        DB::transaction(function () use ($berubah, $data, $periode, $request, $label) {
            foreach ($berubah as $r) {
                $baru = $data['proses'][$r->id] ? (int) $data['proses'][$r->id] : null;
                DB::table('periode_akreditasi_prodi')->where('id', $r->id)
                    ->update(['id_proses' => $baru, 'diubah_oleh' => auth()->id(), 'updated_at' => now()]);
                $this->catat($request, [
                    'aksi' => 'ubah_proses', 'id_periode' => $periode->id, 'tahun' => $periode->tahun,
                    'id_prodi' => $r->id_prodi, 'nama_prodi' => $this->namaProdi($r),
                    'proses_lama' => $label($r->id_proses), 'proses_baru' => $label($baru),
                ]);
            }
        });

        return redirect('lembarkerja/' . $id)->with('sukses', $berubah->isEmpty()
            ? 'Tidak ada perubahan proses.'
            : $berubah->count() . ' proses program studi berhasil diperbarui.');
    }

    public function log(Request $request)
    {
        $tanggal = fn ($v) => is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) ? $v : null;
        $teks = fn ($k) => is_string($request->query($k)) ? mb_substr(trim($request->query($k)), 0, 100) : null;
        $filter = [
            'dari' => $tanggal($request->query('dari')),
            'sampai' => $tanggal($request->query('sampai')),
            'tahun' => ctype_digit((string) $request->query('tahun')) ? (int) $request->query('tahun') : null,
            'prodi' => $teks('prodi'),
        ];
        $utc = fn ($v) => Carbon::parse($v, LogAksesController::ZONA)->utc()->toDateTimeString();

        $query = DB::table('log_lembar_kerja')
            ->when($filter['dari'], fn ($q, $v) => $q->where('dibuat_pada', '>=', $utc($v . ' 00:00:00')))
            ->when($filter['sampai'], fn ($q, $v) => $q->where('dibuat_pada', '<=', $utc($v . ' 23:59:59')))
            ->when($filter['tahun'], fn ($q, $v) => $q->where('tahun', $v))
            ->when($filter['prodi'], fn ($q, $v) => $q->where('nama_prodi', 'like', '%' . addcslashes($v, '%_\\') . '%'));

        $total = (clone $query)->count();
        $log = $query->orderByDesc('dibuat_pada')->orderByDesc('id')->paginate(50)->withQueryString();
        $daftarTahun = DB::table('log_lembar_kerja')->whereNotNull('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('backend/loglembarkerja', compact('log', 'total', 'filter', 'daftarTahun'));
    }

    // tahun kalender yang bisa dipilih: tahun lalu s.d. 5 tahun ke depan
    private function pilihanTahun(): array
    {
        $tahun = (int) now(LogAksesController::ZONA)->year;
        return range($tahun - 1, $tahun + 5);
    }

    // validasi form periode (tambah/ubah); hasil: [data, prodi terpilih keyBy id_prodi]
    private function validasiPeriode(Request $request, ?object $periode = null): array
    {
        // tahun periode lama tetap boleh walau sudah di luar rentang pilihan
        $tahunBoleh = array_unique(array_merge($this->pilihanTahun(), $periode ? [(int) $periode->tahun] : []));
        $data = $request->validateWithBag('periode', [
            'tahun' => ['required', 'integer', Rule::in($tahunBoleh), Rule::unique('periode_akreditasi', 'tahun')->ignore($periode?->id)],
            'prodi' => ['required', 'array', 'min:1'],
            'prodi.*' => ['string', 'max:5', 'distinct'],
        ], [
            'tahun.required' => 'Pilih tahun periode.',
            'tahun.unique' => 'Periode tahun :input sudah ada. Buka tombol View pada tabel untuk melihatnya.',
            'tahun.in' => 'Tahun tidak valid.',
            'tahun.integer' => 'Tahun tidak valid.',
            'prodi.required' => 'Pilih minimal satu program studi.',
            'prodi.*' => 'Ada program studi yang tidak valid.',
        ]);

        // prodi aktif (atau yang memang sudah ada di periode ini) saja yang boleh dipilih
        $prodi = $this->prodiPilihan($periode?->id)->keyBy('id_prodi')->only($data['prodi']);
        if ($prodi->count() !== count($data['prodi'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['prodi' => 'Ada program studi yang tidak valid.'])->errorBag('periode');
        }

        return [$data, $prodi];
    }

    private function tambahProdi(int $idPeriode, $daftarId, $waktu): void
    {
        if (collect($daftarId)->isEmpty()) {
            return;
        }
        DB::table('periode_akreditasi_prodi')->insert(collect($daftarId)->map(fn ($id) => [
            'id_periode' => $idPeriode, 'id_prodi' => $id, 'diubah_oleh' => auth()->id(), 'created_at' => $waktu, 'updated_at' => $waktu,
        ])->values()->all());
    }

    private function barisPeriode(int $id)
    {
        return DB::table('periode_akreditasi_prodi as pp')
            ->leftJoin('prodi as a', 'a.id_prodi', '=', 'pp.id_prodi')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('proses_akreditasi as r', 'r.id', '=', 'pp.id_proses')
            ->select('pp.id_prodi', 'a.nama_prodi', 'd.nama_jenjang', 'r.nama_proses')
            ->where('pp.id_periode', $id)
            ->get();
    }

    // prodi aktif (+ prodi yang sudah ada di periode tertentu) dengan tanggal berakhir SK nasional aktif
    private function prodiPilihan(?int $idPeriode = null)
    {
        return DB::table('prodi as a')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('fakultas as e', 'a.id_fakultas', '=', 'e.id_fakultas')
            ->leftJoin('riwayat_akreditasi as b', function ($j) {
                $j->on('a.id_prodi', '=', 'b.id_prodi')->where('b.status', '=', '1');
            })
            ->select('a.id_prodi', 'a.nama_prodi', 'd.nama_jenjang', 'e.nm_fakultas', DB::raw('max(b.end_date) as nas_end'))
            ->where(function ($q) use ($idPeriode) {
                $q->where('a.status', '=', 1);
                if ($idPeriode) {
                    $q->orWhereIn('a.id_prodi', DB::table('periode_akreditasi_prodi')->select('id_prodi')->where('id_periode', $idPeriode));
                }
            })
            ->groupBy('a.id_prodi', 'a.nama_prodi', 'd.nama_jenjang', 'e.nm_fakultas', 'a.id_jenjang')
            ->orderBy('e.nm_fakultas')->orderBy('a.id_jenjang')->orderBy('a.nama_prodi')
            ->get();
    }

    private function ringkas(string $teks): string
    {
        return mb_strlen($teks) > 500 ? mb_substr($teks, 0, 497) . '...' : $teks;
    }

    private function namaProdi($r): string
    {
        return trim($r->nama_jenjang . ' ' . $r->nama_prodi);
    }

    private function catat(Request $request, array $isi): void
    {
        $user = auth()->user();
        DB::table('log_lembar_kerja')->insert($isi + [
            'id_user' => $user?->id,
            'nama_user' => $user?->nama ?: $user?->username,
            'ip_address' => $request->ip(),
            'dibuat_pada' => now(),
        ]);
    }
}
