<?php

namespace App\Http\Controllers;

use App\Support\EksporTabel;
use App\Support\StatusInternasional;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RekapController extends Controller
{
    private const JENIS = [
        'aktif' => [
            'judul' => 'Rekap Akreditasi Aktif',
            'deskripsi' => 'Akreditasi nasional dan internasional yang sedang berlaku untuk setiap program studi aktif.',
            'url' => 'rekapakreditasi',
        ],
        'nasional' => [
            'judul' => 'Rekap Akreditasi Nasional per Tanggal',
            'deskripsi' => 'SK akreditasi nasional aktif yang berakhir sebelum tanggal yang dipilih.',
            'url' => 'rekap_tanggal_awal',
        ],
        'internasional' => [
            'judul' => 'Rekap Akreditasi Internasional per Tanggal',
            'deskripsi' => 'Akreditasi internasional aktif yang berakhir sebelum tanggal yang dipilih.',
            'url' => 'rekap_tanggal_awal_inter',
        ],
    ];

    public function rekapakreditasi(Request $request)
    {
        return $this->tampil('aktif', $request);
    }

    public function tanggal(Request $request)
    {
        return $this->tampil('nasional', $request);
    }

    public function tanggal_inter(Request $request)
    {
        return $this->tampil('internasional', $request);
    }

    // form lama (POST) dialihkan ke halaman GET agar hasil bisa di-bookmark/di-refresh
    public function kadal(Request $request)
    {
        return redirect('rekap_tanggal_awal?tgl=' . urlencode((string) $request->input('tgl')));
    }

    public function kadal_internasional(Request $request)
    {
        return redirect('rekap_tanggal_awal_inter?tgl=' . urlencode((string) $request->input('tgl')));
    }

    // unduh Excel / PDF sesuai filter yang sedang dipakai di halaman
    public function unduh(Request $request)
    {
        $jenis = $request->query('jenis');
        $format = $request->query('format');
        if (!isset(self::JENIS[$jenis]) || !in_array($format, ['xlsx', 'pdf'], true)) {
            abort(404);
        }

        $tgl = $this->tanggalValid($request->query('tgl'));
        if ($jenis !== 'aktif' && !$tgl) {
            abort(404);
        }

        $filter = $this->filter($request);
        $baris = $this->saring($this->data($jenis, $tgl), $filter);
        $tglTeks = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d-m-Y') : '-';

        $info = ['Badan Penjaminan Mutu Universitas Airlangga  |  Dicetak ' . now('Asia/Jakarta')->format('d-m-Y H:i') . ' WIB'];
        if ($tgl) {
            $info[] = 'Berakhir sebelum tanggal: ' . $tglTeks($tgl);
        }
        $keterangan = array_filter([
            $filter['cari'] !== '' ? 'Pencarian: "' . $filter['cari'] . '"' : null,
            $filter['fakultas'] !== '' ? 'Fakultas: ' . $filter['fakultas'] : null,
            $filter['nasional'] !== '' ? 'Lembaga nasional: ' . ($filter['nasional'] === '-' ? 'belum ada SK' : $filter['nasional']) : null,
            $filter['internasional'] !== '' ? 'Lembaga internasional: ' . ($filter['internasional'] === '-' ? 'tidak ada' : $filter['internasional']) : null,
        ]);
        $info[] = 'Filter: ' . ($keterangan ? implode('  |  ', $keterangan) : 'tidak ada') . '  |  ' . $baris->count() . ' data';

        $prodi = fn ($r) => trim($r->nama_jenjang . ' ' . $r->nama_prodi);
        if ($jenis === 'internasional') {
            $kolom = [['No', 6, true], ['Program Studi', 44], ['Fakultas', 30], ['Lembaga Internasional', 22], ['Berakhir', 13, true], ['Akreditasi Nasional', 20, true], ['Lembaga Nasional', 16]];
            $isi = $baris->values()->map(fn ($r, $i) => [$i + 1, $prodi($r), $r->nm_fakultas, $r->inter[0]['lembaga'] ?? '-', $tglTeks($r->inter_end), $r->nas_akreditasi ?? '-', $r->nas_lembaga ?: '-']);
        } else {
            $kolom = [['No', 6, true], ['Program Studi', 44], ['Fakultas', 30], ['Akreditasi Nasional', 20, true], ['Lembaga Nasional', 16], ['Berlaku s/d', $jenis === 'aktif' ? 26 : 13, true], ['Akreditasi Internasional', 34]];
            $isi = $baris->values()->map(fn ($r, $i) => [$i + 1, $prodi($r), $r->nm_fakultas, $r->nas_akreditasi ?? '-', $r->nas_lembaga ?: '-',
                $tglTeks($r->nas_end) . ($jenis === 'aktif' && ($sisa = self::sisaMasaAktif($r->nas_end)) ? ' (' . $sisa . ')' : ''),
                collect($r->inter)->map(fn ($x) => $x['lembaga'] . ' (s/d ' . $tglTeks($x['tgl_berakhir']) . ')')->implode('; ') ?: '-']);
        }

        $tabel = ['judul' => self::JENIS[$jenis]['judul'], 'info' => $info, 'kolom' => $kolom, 'baris' => $isi->all()];
        $namaFile = Str::slug(self::JENIS[$jenis]['judul']) . ($tgl ? '-' . $tgl : '') . '-' . now('Asia/Jakarta')->format('Ymd') . '.' . $format;

        return $format === 'xlsx' ? EksporTabel::excel($tabel, $namaFile) : EksporTabel::pdf($tabel, $namaFile);
    }

    private function tampil(string $jenis, Request $request)
    {
        $tgl = $this->tanggalValid($request->query('tgl'));

        return view('backend/rekap', self::JENIS[$jenis] + [
            'mode' => $jenis,
            'tanggal' => $tgl,
            'aksiTanggal' => $jenis === 'aktif' ? null : url(self::JENIS[$jenis]['url']),
            'baris' => $jenis === 'aktif' || $tgl ? $this->data($jenis, $tgl) : null,
            'fakultas' => DB::table('fakultas')->orderBy('nm_fakultas')->pluck('nm_fakultas'),
            'lembagaNasional' => DB::table('jenis_akreditasi')->orderBy('id_jenis_akreditasi')->pluck('nm_akreditasi'),
            'lembagaInter' => DB::table('nama_lembaga_internasional')->orderBy('id_akre')->pluck('nama_lembaga'),
        ]);
    }

    private function data(string $jenis, ?string $tgl)
    {
        StatusInternasional::perbaruiHarian();

        if ($jenis === 'aktif') {
            // urut tanggal berakhir: yang sudah expired dulu, lalu yang paling dekat berakhir; prodi tanpa SK di akhir
            return $this->barisProdi()->sortBy(fn ($r) => $r->nas_end ?: '9999-12-31')->values();
        }
        if ($jenis === 'nasional') {
            return $this->barisProdi()->filter(fn ($r) => $r->nas_end && $r->nas_end < $tgl)->values();
        }

        // internasional: satu baris per akreditasi internasional, dilengkapi data prodi & SK nasional
        $prodi = $this->barisProdi()->keyBy('id_prodi');
        return DB::table('riwayat_akreditasi_internasional_series as f')
            ->select('f.id_prodi', 'g.nama_lembaga', 'f.tgl_berakhir')
            ->leftJoin('nama_lembaga_internasional as g', 'f.lembaga', '=', 'g.id_akre')
            ->where('f.keterangan', '=', StatusInternasional::AKTIF)
            ->where('f.tgl_berakhir', '<', $tgl)
            ->get()
            ->filter(fn ($f) => $prodi->has($f->id_prodi))
            ->map(fn ($f) => (object) array_merge((array) $prodi[$f->id_prodi], [
                'inter' => [['lembaga' => $f->nama_lembaga, 'tgl_berakhir' => $f->tgl_berakhir]],
                'inter_end' => $f->tgl_berakhir,
            ]))
            ->sortBy(fn ($r) => $r->nm_fakultas . '|' . str_pad($r->id_jenjang, 3, '0', STR_PAD_LEFT) . '|' . $r->nama_prodi)
            ->values();
    }

    // prodi aktif + SK nasional aktif (bila ada) + daftar akreditasi internasional aktif
    private function barisProdi()
    {
        $inter = DB::table('riwayat_akreditasi_internasional_series as f')
            ->select('f.id_prodi', 'g.nama_lembaga', 'f.tgl_berakhir')
            ->leftJoin('nama_lembaga_internasional as g', 'f.lembaga', '=', 'g.id_akre')
            ->where('f.keterangan', '=', StatusInternasional::AKTIF)
            ->orderBy('f.tgl_berakhir')
            ->get()
            ->groupBy('id_prodi');

        return DB::table('prodi as a')
            ->select('a.id_prodi', 'a.nama_prodi', 'a.id_jenjang', 'd.nama_jenjang', 'a.id_fakultas', 'e.nm_fakultas',
                'c.akreditasi as nas_akreditasi', 'b.lembaga as nas_lembaga', 'b.end_date as nas_end')
            ->leftJoin('riwayat_akreditasi as b', function ($j) {
                $j->on('a.id_prodi', '=', 'b.id_prodi')->where('b.status', '=', '1');
            })
            ->leftJoin('peringkat as c', 'b.akreditasi', '=', 'c.kode')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('fakultas as e', 'a.id_fakultas', '=', 'e.id_fakultas')
            ->where('a.status', '=', 1)
            ->orderBy('e.nm_fakultas')->orderBy('a.id_jenjang')->orderBy('a.nama_prodi')
            ->get()
            ->map(function ($r) use ($inter) {
                $r->inter = $inter->get($r->id_prodi, collect())
                    ->map(fn ($f) => ['lembaga' => $f->nama_lembaga, 'tgl_berakhir' => $f->tgl_berakhir])->values()->all();
                return $r;
            });
    }

    private function filter(Request $request): array
    {
        $teks = fn ($k) => is_string($request->query($k)) ? mb_substr(trim($request->query($k)), 0, 100) : '';
        return ['cari' => $teks('cari'), 'fakultas' => $teks('fakultas'), 'nasional' => $teks('nasional'), 'internasional' => $teks('internasional')];
    }

    // aturan sama dengan filter JavaScript di halaman rekap
    private function saring($baris, array $f)
    {
        $kecil = fn ($v) => mb_strtolower((string) $v);
        return $baris->filter(function ($r) use ($f, $kecil) {
            $inter = collect($r->inter)->pluck('lembaga')->map($kecil);
            return str_contains($kecil($r->nama_jenjang . ' ' . $r->nama_prodi), $kecil($f['cari']))
                && ($f['fakultas'] === '' || $r->nm_fakultas === $f['fakultas'])
                && ($f['nasional'] === '' || ($r->nas_lembaga ? $kecil($r->nas_lembaga) : '-') === $kecil($f['nasional']))
                && ($f['internasional'] === '' || ($f['internasional'] === '-' ? $inter->isEmpty() : $inter->contains($kecil($f['internasional']))));
        })->values();
    }

    // sisa masa berlaku SK: "Expired" bila sudah lewat, "N hari lagi" bila tinggal kurang dari 2 tahun, selain itu null
    public static function sisaMasaAktif(?string $akhir): ?string
    {
        if (!$akhir) {
            return null;
        }
        $hariIni = \Carbon\Carbon::parse(now('Asia/Jakarta')->toDateString());
        $berakhir = \Carbon\Carbon::parse($akhir)->startOfDay();
        if ($berakhir->lt($hariIni)) {
            return 'Expired';
        }
        if ($berakhir->gte($hariIni->copy()->addYears(2))) {
            return null;
        }
        $hari = (int) $hariIni->diffInDays($berakhir);
        return $hari === 0 ? 'Berakhir hari ini' : number_format($hari, 0, ',', '.') . ' hari lagi';
    }

    private function tanggalValid($v): ?string
    {
        return is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) ? $v : null;
    }
}
