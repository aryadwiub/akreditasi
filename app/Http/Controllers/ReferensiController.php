<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

// Referensi: Fakultas, Prodi, Peringkat, Lembaga Nasional, Lembaga Internasional.
// Kelima halaman memakai pola yang sama (lihat jenis()), view: backend/ref_daftar & backend/ref_ubah.
class ReferensiController extends Controller
{
    private const PESAN = [
        'required' => ':Attribute wajib diisi.',
        'max' => ':Attribute maksimal :max karakter.',
        'unique' => ':Attribute ":input" sudah ada.',
        'exists' => ':Attribute tidak valid.',
        'in' => ':Attribute tidak valid.',
        'integer' => ':Attribute harus berupa angka.',
        'min' => ':Attribute minimal :min.',
        'regex' => ':Attribute hanya boleh berisi huruf, angka' . ' atau tanda hubung, tanpa spasi.',
    ];

    public function fakultas() { return $this->daftar('fakultas'); }
    public function simpanfakultas(Request $r) { return $this->simpan($r, 'fakultas'); }
    public function ubahfakultas($id) { return $this->ubah('fakultas', $id); }
    public function ubahsimpanfakultas(Request $r, $id) { return $this->ubahSimpan($r, 'fakultas', $id); }
    public function destroyfakultas($id) { return $this->hapus('fakultas', $id); }

    public function prodi() { return $this->daftar('prodi'); }
    public function simpanprodi(Request $r) { return $this->simpan($r, 'prodi'); }
    public function ubahprodi($id) { return $this->ubah('prodi', $id); }
    public function ubahsimpanprodi(Request $r, $id) { return $this->ubahSimpan($r, 'prodi', $id); }
    public function destroyprodi($id) { return $this->hapus('prodi', $id); }

    public function peringkat() { return $this->daftar('peringkat'); }
    public function simpanperingkat(Request $r) { return $this->simpan($r, 'peringkat'); }
    public function ubahperingkat($id) { return $this->ubah('peringkat', $id); }
    public function ubahsimpanperingkat(Request $r, $id) { return $this->ubahSimpan($r, 'peringkat', $id); }
    public function destroyperingkat($id) { return $this->hapus('peringkat', $id); }

    public function lembaganasional() { return $this->daftar('lembaganasional'); }
    public function simpanlembaganasional(Request $r) { return $this->simpan($r, 'lembaganasional'); }
    public function ubahlembaganasional($id) { return $this->ubah('lembaganasional', $id); }
    public function ubahsimpanlembaganasional(Request $r, $id) { return $this->ubahSimpan($r, 'lembaganasional', $id); }
    public function destroylembaganasional($id) { return $this->hapus('lembaganasional', $id); }

    public function lembagainter() { return $this->daftar('lembagainter'); }
    public function simpanlembagainter(Request $r) { return $this->simpan($r, 'lembagainter'); }
    public function ubahlembagainter($id) { return $this->ubah('lembagainter', $id); }
    public function ubahsimpanlembagainter(Request $r, $id) { return $this->ubahSimpan($r, 'lembagainter', $id); }
    public function destroylembagainter($id) { return $this->hapus('lembagainter', $id); }

    // ---------------------------------------------------------------------------------------------

    private function daftar(string $jenis)
    {
        $j = $this->jenis($jenis);
        $pakai = ($j['pakai'])();
        $baris = ($j['query'])()->get()->each(function ($r) use ($j, $pakai) {
            $r->_id = $r->{$j['pk']};
            $r->_pakai = $pakai[mb_strtolower((string) $r->_id)] ?? 0;
        });

        return view('backend/ref_daftar', ['j' => $j, 'baris' => $baris, 'opsi' => $this->opsi($j)]);
    }

    private function ubah(string $jenis, $id)
    {
        $j = $this->jenis($jenis);
        $data = DB::table($j['tabel'])->where($j['pk'], $id)->first() ?? abort(404);
        $pakai = ($j['pakai'])()[mb_strtolower((string) $id)] ?? 0;

        return view('backend/ref_ubah', ['j' => $j, 'data' => $data, 'id' => $id, 'pakai' => $pakai, 'opsi' => $this->opsi($j)]);
    }

    private function simpan(Request $request, string $jenis)
    {
        $j = $this->jenis($jenis);
        $data = $request->validateWithBag('tambah', $this->aturan($j), self::PESAN, $this->label($j));

        DB::transaction(function () use ($j, &$data) {
            if (!empty($j['idOtomatis'])) {
                // kolom kunci bukan auto increment: pakai nomor terbesar + 1
                $data[$j['pk']] = (int) DB::table($j['tabel'])->lockForUpdate()->max($j['pk']) + 1;
            }
            DB::table($j['tabel'])->insert($data + ($j['bawaan'] ?? []));
        });

        return redirect($j['url'])->with('sukses', $j['judul'] . ' "' . $data[$j['namaKolom']] . '" berhasil ditambahkan.');
    }

    private function ubahSimpan(Request $request, string $jenis, $id)
    {
        $j = $this->jenis($jenis);
        $lama = DB::table($j['tabel'])->where($j['pk'], $id)->first() ?? abort(404);
        $data = $request->validate($this->aturan($j, $id), self::PESAN, $this->label($j));

        DB::transaction(function () use ($j, $id, $lama, $data) {
            DB::table($j['tabel'])->where($j['pk'], $id)->update($data);
            if (isset($j['setelahUbah'])) {
                ($j['setelahUbah'])($lama, $data);
            }
        });

        // update tanpa perubahan bukan kegagalan
        return redirect($j['url'])->with('sukses', $j['judul'] . ' "' . $data[$j['namaKolom']] . '" berhasil disimpan.');
    }

    private function hapus(string $jenis, $id)
    {
        $j = $this->jenis($jenis);
        $data = DB::table($j['tabel'])->where($j['pk'], $id)->first() ?? abort(404);
        $pakai = ($j['pakai'])()[mb_strtolower((string) $id)] ?? 0;
        if ($pakai > 0) {
            return redirect($j['url'])->with('gagal', $j['judul'] . ' "' . $data->{$j['namaKolom']} . '" tidak dapat dihapus karena masih dipakai oleh ' . $pakai . ' ' . $j['pakaiLabel'] . '.');
        }

        DB::table($j['tabel'])->where($j['pk'], $id)->delete();
        return redirect($j['url'])->with('sukses', $j['judul'] . ' "' . $data->{$j['namaKolom']} . '" berhasil dihapus.');
    }

    // aturan validasi dari definisi field; $id terisi saat ubah (kolom terkunci tidak ikut diubah)
    private function aturan(array $j, $id = null): array
    {
        $aturan = [];
        foreach ($j['field'] as $nama => $f) {
            if ($id !== null && !empty($f['kunci'])) {
                continue;
            }
            $aturan[$nama] = array_map(
                fn ($a) => $a === 'unik' ? Rule::unique($j['tabel'], $nama)->ignore($id, $j['pk']) : $a,
                $f['aturan']
            );
        }
        return $aturan;
    }

    private function label(array $j): array
    {
        return array_map(fn ($f) => mb_strtolower($f['label']), $j['field']);
    }

    // pilihan untuk field select
    private function opsi(array $j): array
    {
        $opsi = [];
        foreach ($j['field'] as $nama => $f) {
            if (isset($f['pilihan'])) {
                $opsi[$nama] = is_callable($f['pilihan']) ? ($f['pilihan'])() : $f['pilihan'];
            }
        }
        return $opsi;
    }

    private function jenis(string $jenis): array
    {
        $hitung = fn ($tabel, $kolom) => DB::table($tabel)->select($kolom, DB::raw('count(*) as n'))->whereNotNull($kolom)->groupBy($kolom)->get()
            ->mapWithKeys(fn ($r) => [mb_strtolower((string) $r->{$kolom}) => (int) $r->n]);
        $fakultas = fn () => DB::table('fakultas')->orderBy('nm_fakultas')->pluck('nm_fakultas', 'id_fakultas')->all();
        $jenjang = fn () => DB::table('jenjang')->orderBy('order')->orderBy('id_jenjang')->pluck('nama_jenjang', 'id_jenjang')->all();

        return match ($jenis) {
            'fakultas' => [
                'judul' => 'Fakultas',
                'deskripsi' => 'Daftar fakultas yang menjadi induk program studi.',
                'tabel' => 'fakultas', 'pk' => 'id_fakultas', 'namaKolom' => 'nm_fakultas', 'idOtomatis' => true,
                'url' => 'referensifakultas', 'aksi' => 'fakultas',
                'query' => fn () => DB::table('fakultas')->orderBy('id_fakultas'),
                'kolom' => [['ID', 'id_fakultas', 'tengah'], ['Nama Fakultas', 'nm_fakultas', 'utama'], ['Singkatan', 'nm_singkat', 'tengah']],
                'field' => [
                    'nm_fakultas' => ['label' => 'Nama Fakultas', 'aturan' => ['required', 'string', 'max:255', 'unik']],
                    'nm_singkat' => ['label' => 'Singkatan', 'aturan' => ['nullable', 'string', 'max:20'], 'contoh' => 'mis. FK'],
                ],
                'pakai' => fn () => $hitung('prodi', 'id_fakultas'), 'pakaiLabel' => 'program studi',
            ],
            'prodi' => [
                'judul' => 'Program Studi',
                'deskripsi' => 'Referensi program studi beserta fakultas, jenjang, ID Cyber (AUCC/SatuData) dan statusnya.',
                'tabel' => 'prodi', 'pk' => 'id_prodi', 'namaKolom' => 'nama_prodi',
                'url' => 'referensiprodi', 'aksi' => 'prodi',
                'query' => fn () => DB::table('prodi as a')
                    ->leftJoin('jenjang as b', 'a.id_jenjang', '=', 'b.id_jenjang')
                    ->leftJoin('fakultas as c', 'a.id_fakultas', '=', 'c.id_fakultas')
                    ->select('a.*', 'b.nama_jenjang', 'c.nm_fakultas')
                    ->orderBy('c.nm_fakultas')->orderBy('b.order')->orderBy('a.nama_prodi'),
                'kolom' => [['ID', 'id_prodi', 'tengah'], ['Jenjang', 'nama_jenjang', 'tengah'], ['Program Studi', 'nama_prodi', 'utama'],
                    ['Fakultas', 'nm_fakultas', 'md'], ['ID Cyber', 'id_cyber', 'tengah'], ['Status', 'status', 'status']],
                'filter' => [['nm_fakultas', 'Fakultas'], ['nama_jenjang', 'Jenjang'], ['status', 'Status']],
                'field' => [
                    'id_prodi' => ['label' => 'ID Prodi', 'aturan' => ['required', 'string', 'max:5', 'regex:/^[0-9A-Za-z]+$/', 'unik'], 'kunci' => true,
                        'contoh' => 'maks. 5 karakter, unik', 'bantuan' => 'ID tidak dapat diubah setelah disimpan karena dipakai riwayat akreditasi.'],
                    'id_fakultas' => ['label' => 'Fakultas', 'aturan' => ['required', 'integer', 'exists:fakultas,id_fakultas'], 'pilihan' => $fakultas],
                    'id_jenjang' => ['label' => 'Jenjang', 'aturan' => ['required', 'exists:jenjang,id_jenjang'], 'pilihan' => $jenjang],
                    'nama_prodi' => ['label' => 'Nama Prodi', 'aturan' => ['required', 'string', 'max:255'], 'contoh' => 'tanpa jenjang, mis. KEDOKTERAN'],
                    'id_cyber' => ['label' => 'ID Cyber', 'aturan' => ['nullable', 'integer', 'min:1'], 'tipe' => 'number',
                        'bantuan' => 'ID program studi di AUCC / SatuData (lihat Referensi → Prodi SatuData).'],
                    'status' => ['label' => 'Status', 'aturan' => ['required', 'in:1,2'], 'pilihan' => [1 => 'Aktif', 2 => 'Tidak Aktif']],
                ],
                'pakai' => function () use ($hitung) {
                    $total = [];
                    foreach ([['riwayat_akreditasi', 'id_prodi'], ['riwayat_akreditasi_internasional_series', 'id_prodi'], ['periode_akreditasi_prodi', 'id_prodi']] as [$t, $k]) {
                        foreach ($hitung($t, $k) as $id => $n) {
                            $total[$id] = ($total[$id] ?? 0) + $n;
                        }
                    }
                    return $total;
                },
                'pakaiLabel' => 'data riwayat akreditasi / lembar kerja',
            ],
            'peringkat' => [
                'judul' => 'Peringkat Akreditasi Nasional',
                'deskripsi' => 'Kode dan nama peringkat akreditasi nasional; urutan dipakai untuk kolom peta sebaran.',
                'tabel' => 'peringkat', 'pk' => 'kode', 'namaKolom' => 'akreditasi',
                'url' => 'referensiperingkat', 'aksi' => 'peringkat',
                'query' => fn () => DB::table('peringkat')->orderByRaw('urutan is null')->orderBy('urutan')->orderBy('kode'),
                'kolom' => [['Urutan', 'urutan', 'tengah'], ['Kode', 'kode', 'kode'], ['Peringkat', 'akreditasi', 'utama']],
                'field' => [
                    'kode' => ['label' => 'Kode', 'aturan' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9-]+$/', 'unik'], 'kunci' => true,
                        'contoh' => 'mis. BS', 'bantuan' => 'Kode tidak dapat diubah setelah disimpan karena dipakai riwayat akreditasi.'],
                    'akreditasi' => ['label' => 'Nama Peringkat', 'aturan' => ['required', 'string', 'max:255'], 'contoh' => 'mis. BAIK SEKALI'],
                    'urutan' => ['label' => 'Urutan', 'aturan' => ['nullable', 'integer', 'min:0', 'max:999'], 'tipe' => 'number'],
                ],
                'pakai' => fn () => $hitung('riwayat_akreditasi', 'akreditasi'), 'pakaiLabel' => 'riwayat akreditasi',
            ],
            'lembaganasional' => [
                'judul' => 'Lembaga Akreditasi Nasional',
                'deskripsi' => 'Lembaga akreditasi nasional (BAN-PT dan LAM).',
                'tabel' => 'jenis_akreditasi', 'pk' => 'id_jenis_akreditasi', 'namaKolom' => 'nm_akreditasi',
                'url' => 'referensilembaganasional', 'aksi' => 'lembaganasional',
                'query' => fn () => DB::table('jenis_akreditasi')->orderBy('id_jenis_akreditasi'),
                'kolom' => [['ID', 'id_jenis_akreditasi', 'tengah'], ['Nama Lembaga', 'nm_akreditasi', 'utama']],
                'field' => [
                    'nm_akreditasi' => ['label' => 'Nama Lembaga', 'aturan' => ['required', 'string', 'max:255', 'unik'], 'contoh' => 'mis. LAM-PTKes',
                        'bantuan' => 'Mengganti nama juga memperbarui nama lembaga pada riwayat akreditasi yang memakainya.'],
                ],
                // riwayat_akreditasi.lembaga menyimpan NAMA lembaga, bukan ID
                'pakai' => function () use ($hitung) {
                    $perNama = $hitung('riwayat_akreditasi', 'lembaga');
                    return DB::table('jenis_akreditasi')->pluck('nm_akreditasi', 'id_jenis_akreditasi')
                        ->mapWithKeys(fn ($nama, $id) => [(string) $id => $perNama[mb_strtolower((string) $nama)] ?? 0])->all();
                },
                'setelahUbah' => function ($lama, $baru) {
                    if ($lama->nm_akreditasi !== $baru['nm_akreditasi']) {
                        DB::table('riwayat_akreditasi')->where('lembaga', $lama->nm_akreditasi)->update(['lembaga' => $baru['nm_akreditasi']]);
                    }
                },
                'pakaiLabel' => 'riwayat akreditasi',
            ],
            'lembagainter' => [
                'judul' => 'Lembaga Akreditasi Internasional',
                'deskripsi' => 'Lembaga akreditasi internasional (AUN, ASIIN, FIBAA, dll.).',
                'tabel' => 'nama_lembaga_internasional', 'pk' => 'id_akre', 'namaKolom' => 'nama_lembaga', 'bawaan' => ['status' => ''],
                'url' => 'referensilembagainter', 'aksi' => 'lembagainter',
                'query' => fn () => DB::table('nama_lembaga_internasional')->orderBy('id_akre'),
                'kolom' => [['ID', 'id_akre', 'tengah'], ['Nama Lembaga', 'nama_lembaga', 'utama']],
                'field' => [
                    'nama_lembaga' => ['label' => 'Nama Lembaga', 'aturan' => ['required', 'string', 'max:255', 'unik'], 'contoh' => 'mis. ASIIN'],
                ],
                'pakai' => fn () => $hitung('riwayat_akreditasi_internasional_series', 'lembaga'), 'pakaiLabel' => 'riwayat akreditasi internasional',
            ],
            default => abort(404),
        };
    }
}
