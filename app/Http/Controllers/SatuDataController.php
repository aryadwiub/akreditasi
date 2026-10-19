<?php

namespace App\Http\Controllers;

use App\Support\EksporTabel;
use App\Support\SatuData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Referensi -> Prodi SatuData: data program studi dari API SatuData (AUCC) dibandingkan dengan tabel prodi lokal
class SatuDataController extends Controller
{
    // status hasil pembandingan (prodi.id_cyber = id_program_studi SatuData)
    public const STATUS = [
        'sama' => ['Sesuai', 'bg-green-100 text-green-800'],
        'beda_nama' => ['Nama berbeda', 'bg-amber-100 text-amber-800'],
        'nonaktif_lokal' => ['Nonaktif di lokal', 'bg-gray-200 text-gray-700'],
        'tidak_di_lokal' => ['Tidak ada di lokal', 'bg-red-100 text-red-700'],
        'tidak_di_satudata' => ['Tidak ada di SatuData', 'bg-red-100 text-red-700'],
    ];

    public function prodi()
    {
        try {
            $hasil = SatuData::prodi();
        } catch (\Throwable $e) {
            return view('backend/satudata_prodi', ['gagal' => $e->getMessage(), 'baris' => collect(), 'diambil' => null, 'peringatan' => null]);
        }

        return view('backend/satudata_prodi', [
            'gagal' => null,
            'baris' => $this->bandingkan($hasil['data']),
            'diambil' => $hasil['diambil'],
            'peringatan' => $hasil['peringatan'],
        ]);
    }

    public function muatUlang()
    {
        try {
            $hasil = SatuData::prodi(true);
            $pesan = $hasil['peringatan'] ?? 'Data berhasil diperbarui dari SatuData (' . count($hasil['data']) . ' program studi).';
        } catch (\Throwable $e) {
            $pesan = $e->getMessage();
        }
        return redirect('referensisatudata')->with('info', $pesan);
    }

    // unduh Excel / PDF sesuai filter yang sedang dipakai di halaman
    public function unduh(Request $request)
    {
        $format = $request->query('format');
        abort_unless(in_array($format, ['xlsx', 'pdf'], true), 404);

        try {
            $hasil = SatuData::prodi();
        } catch (\Throwable $e) {
            return redirect('referensisatudata')->with('info', $e->getMessage());
        }

        $teks = fn ($k) => is_string($request->query($k)) ? mb_substr(trim($request->query($k)), 0, 100) : '';
        $f = ['cari' => $teks('cari'), 'fakultas' => $teks('fakultas'), 'jenjang' => $teks('jenjang'), 'status' => $teks('status')];
        $kecil = fn ($v) => mb_strtolower((string) $v);

        // aturan sama dengan filter JavaScript di halaman
        $baris = $this->bandingkan($hasil['data'])->filter(fn ($r) =>
            str_contains($kecil($r->prodi . ' ' . $r->lokal_nama . ' ' . $r->singkatan), $kecil($f['cari']))
            && ($f['fakultas'] === '' || $r->fakultas === $f['fakultas'])
            && ($f['jenjang'] === '' || $r->jenjang === $f['jenjang'])
            && ($f['status'] === '' || $r->status === $f['status'])
        )->values();

        $keterangan = array_filter([
            $f['cari'] !== '' ? 'Pencarian: "' . $f['cari'] . '"' : null,
            $f['fakultas'] !== '' ? 'Fakultas: ' . $f['fakultas'] : null,
            $f['jenjang'] !== '' ? 'Jenjang: ' . $f['jenjang'] : null,
            isset(self::STATUS[$f['status']]) ? 'Status: ' . self::STATUS[$f['status']][0] : null,
        ]);
        $tabel = [
            'judul' => 'Program Studi SatuData UNAIR',
            'info' => [
                'Badan Penjaminan Mutu Universitas Airlangga  |  Dicetak ' . now(LogAksesController::ZONA)->format('d-m-Y H:i') . ' WIB',
                'Sumber: API SatuData (aucc-program-studi), diambil ' . \Carbon\Carbon::createFromTimestamp($hasil['diambil'], LogAksesController::ZONA)->format('d-m-Y H:i') . ' WIB',
                'Filter: ' . ($keterangan ? implode('  |  ', $keterangan) : 'tidak ada') . '  |  ' . $baris->count() . ' data',
            ],
            'kolom' => [['No', 6, true], ['ID AUCC', 10, true], ['Program Studi (SatuData)', 42], ['Singkatan', 11, true], ['Fakultas', 30],
                ['Masa Studi Maks (smt)', 12, true], ['Subject THE WUR', 22], ['ID Prodi Lokal', 10, true], ['Nama Prodi Lokal', 40], ['Status', 18]],
            'baris' => $baris->map(fn ($r, $i) => [$i + 1, $r->id_aucc ?? '-', $r->prodi ?: '-', $r->singkatan ?: '-', $r->fakultas ?: '-',
                $r->masa_studi ?? '-', $r->subject ?: '-', $r->lokal_id ?? '-', $r->lokal_nama ?: '-', self::STATUS[$r->status][0]])->all(),
        ];
        $namaFile = 'prodi-satudata-' . now(LogAksesController::ZONA)->format('Ymd') . '.' . $format;

        return $format === 'xlsx' ? EksporTabel::excel($tabel, $namaFile) : EksporTabel::pdf($tabel, $namaFile);
    }

    // gabungkan data SatuData dengan tabel prodi lokal; prodi lokal aktif yang tidak ada di SatuData ikut ditampilkan
    private function bandingkan(array $data)
    {
        $lokal = DB::table('prodi as a')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('fakultas as e', 'a.id_fakultas', '=', 'e.id_fakultas')
            ->select('a.id_prodi', 'a.id_cyber', 'a.nama_prodi', 'a.status', 'd.nama_jenjang', 'e.nm_fakultas')
            ->get();
        $perCyber = $lokal->filter(fn ($p) => $p->id_cyber)->groupBy('id_cyber');
        $normal = fn ($v) => preg_replace('/\s+/', ' ', mb_strtolower(trim((string) $v)));

        $baris = collect($data)->map(function ($a) use ($perCyber, $normal) {
            // bila satu id_cyber dipakai beberapa prodi lokal, utamakan yang aktif
            $p = $perCyber->get($a['id_program_studi'] ?? null)?->sortByDesc('status')->first();
            $namaLokal = $p ? trim($p->nama_jenjang . ' ' . $p->nama_prodi) : null;
            $status = match (true) {
                !$p => 'tidak_di_lokal',
                (int) $p->status !== 1 => 'nonaktif_lokal',
                $normal($namaLokal) !== $normal($a['prodi'] ?? '') => 'beda_nama',
                default => 'sama',
            };
            return (object) [
                'id_aucc' => $a['id_program_studi'] ?? null,
                'prodi' => trim(preg_replace('/\s+/', ' ', (string) ($a['prodi'] ?? ''))),
                'singkatan' => $a['nm_singkat_prodi'] ?? null,
                'jenjang' => $a['nm_jenjang'] ?? '',
                'fakultas' => $a['nm_fakultas'] ?? '',
                'singkatan_fakultas' => $a['singkatan_fakultas'] ?? null,
                'masa_studi' => $a['masa_studi_semester_maks'] ?? null,
                'subject' => $a['subject_the_wur'] ?? null,
                'lokal_id' => $p?->id_prodi,
                'lokal_nama' => $namaLokal,
                'status' => $status,
            ];
        });

        $adaDiApi = $baris->pluck('id_aucc')->filter()->map(fn ($v) => (string) $v)->all();
        $hilang = $lokal->filter(fn ($p) => (int) $p->status === 1 && !in_array((string) $p->id_cyber, $adaDiApi, true))
            ->map(fn ($p) => (object) [
                'id_aucc' => null, 'prodi' => '', 'singkatan' => null, 'jenjang' => $p->nama_jenjang ?? '', 'fakultas' => $p->nm_fakultas ?? '',
                'singkatan_fakultas' => null, 'masa_studi' => null, 'subject' => null,
                'lokal_id' => $p->id_prodi, 'lokal_nama' => trim($p->nama_jenjang . ' ' . $p->nama_prodi), 'status' => 'tidak_di_satudata',
            ]);

        return $baris->concat($hilang)
            ->sortBy(fn ($r) => $r->fakultas . '|' . $r->jenjang . '|' . ($r->prodi ?: $r->lokal_nama))
            ->values();
    }
}
