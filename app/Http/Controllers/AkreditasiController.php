<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Akreditasi;
use App\Support\BerkasSk;
use App\Support\StatusInternasional;

class AkreditasiController extends Controller
{
        // Sumber data Ajax untuk tabel akreditasi nasional di beranda (DataTables)
        public function dataNasional()
        {
                $nas = DB::select('SELECT
                a.id_riwayat_akreditasi, a.id_prodi, b.nama_prodi, d.nama_jenjang, c.nm_fakultas, f.akreditasi, a.akreditasi AS kode_akreditasi, a.no_sk_akreditasi, a.lembaga, a.start_date, a.end_date, a.url, a.file
                FROM `riwayat_akreditasi` AS a
                LEFT JOIN prodi as b on a.id_prodi = b.id_prodi
                LEFT JOIN fakultas as c on b.id_fakultas = c.id_fakultas
                LEFT JOIN jenjang as d on b.id_jenjang = d.id_jenjang
                LEFT JOIN peringkat as f on a.akreditasi = f.kode
                WHERE a.status = ? and b.status = ?

                UNION ALL

                -- prodi aktif yang belum punya riwayat akreditasi aktif: tetap ditampilkan, kolom akreditasi kosong (-)
                SELECT NULL, b.id_prodi, b.nama_prodi, d.nama_jenjang, c.nm_fakultas, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL
                FROM prodi as b
                LEFT JOIN fakultas as c on b.id_fakultas = c.id_fakultas
                LEFT JOIN jenjang as d on b.id_jenjang = d.id_jenjang
                WHERE b.status = ?
                  AND NOT EXISTS (SELECT 1 FROM riwayat_akreditasi r WHERE r.id_prodi = b.id_prodi AND r.status = ?)', [1, 1, 1, 1]);

                // Riwayat dokumen seluruh prodi diambil sekali, lalu dikelompokkan per prodi
                $dokumen1 = DB::table('riwayat_akreditasi_dokumen as Z')
                        ->select(
                                DB::raw('"d" as kode'), 'id', 'id_prodi', 'file as url', DB::raw('NULL as file'),
                                DB::raw('date_format(tgl_awal,"%Y") as tgl_awal'),
                                DB::raw('date_format(tgl_akhir,"%Y") as tgl_akhir'),
                        );

                $riwayat = DB::table('riwayat_akreditasi as A')
                        ->select(
                                DB::raw('"n" as kode'), 'id_riwayat_akreditasi as id', 'id_prodi', 'url', 'file',
                                DB::raw('date_format(start_date,"%Y") as tgl_awal'),
                                DB::raw('date_format(end_date,"%Y") as tgl_akhir'),
                        )
                        ->where('A.status', '=', '0')
                        ->unionAll($dokumen1)
                        ->orderBy('tgl_akhir', 'desc')
                        ->get()
                        ->groupBy('id_prodi');

                // url/file asli tidak dikirim ke browser; diganti link terenkripsi ke halaman captcha
                $link = fn ($kode, $id, $url, $file) => ($file || $url) ? route('berkas.form', BerkasSk::token($kode, (int) $id)) : null;

                $data = array_map(function ($row) use ($riwayat, $link) {
                        $row->riwayat = $riwayat->get($row->id_prodi, collect())
                                ->map(fn ($d) => ['url' => $link($d->kode, $d->id, $d->url, $d->file), 'tgl_awal' => $d->tgl_awal, 'tgl_akhir' => $d->tgl_akhir])
                                ->filter(fn ($d) => $d['url'])
                                ->values();
                        $row->url = $link('n', $row->id_riwayat_akreditasi, $row->url, $row->file);
                        unset($row->file, $row->id_riwayat_akreditasi);
                        return $row;
                }, $nas);

                return response()->json(['data' => $data]);
        }

        public function index()
        {

                // $data =
                //         DB::table('prodi')
                //         ->select(
                //                 'prodi.id_prodi',
                //                 'prodi.nama_prodi',
                //                 'fakultas.nm_fakultas',
                //                 'jenjang.nama_jenjang',
                //                 'peringkat.akreditasi',
                //                 'riwayat_akreditasi.url',
                //                 'riwayat_akreditasi.end_date',
                //                 'riwayat_akreditasi.start_date',
                //                 'riwayat_akreditasi.lembaga',
                //                 'prodi.status',
                //                 'riwayat_akreditasi_internasional_series.url as urlinter',
                //                 'riwayat_akreditasi_internasional_series.id_riwayat',
                //                 'riwayat_akreditasi_internasional_series.tgl_berakhir',
                //                 'nama_lembaga_internasional.nama_lembaga',
                //                 DB::raw('(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = 3 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "1"
                //         WHEN riwayat_akreditasi_internasional_series.lembaga = 4 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "1"
                //         WHEN riwayat_akreditasi_internasional_series.lembaga = 6 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "1"
                //           END) AS tgl_akhir'),
                //                 DB::raw('(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = 3 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "ASIIN"
                //         WHEN riwayat_akreditasi_internasional_series.lembaga = 4 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "FIBAA"
                //         WHEN riwayat_akreditasi_internasional_series.lembaga = 6 AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN "RSC" END) AS internasional')
                //         )
                //         ->join('riwayat_akreditasi', 'prodi.id_prodi', '=', 'riwayat_akreditasi.id_prodi')
                //         ->leftJoin('peringkat', 'riwayat_akreditasi.akreditasi', '=', 'peringkat.kode')
                //         ->leftJoin('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
                //         ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id_fakultas')
                //         ->leftJoin('riwayat_akreditasi_international', 'prodi.id_prodi', '=', 'riwayat_akreditasi_international.id_prodi')
                //         ->leftJoin('riwayat_akreditasi_internasional_series', 'prodi.id_prodi', '=', 'riwayat_akreditasi_internasional_series.id_prodi')
                //         ->leftJoin('nama_lembaga_internasional', 'riwayat_akreditasi_internasional_series.lembaga', '=', 'nama_lembaga_internasional.id_akre')

                //         ->where('prodi.status', '=', '1')
                //         ->orderBy('fakultas.nm_fakultas', 'ASC')
                //         ->orderBy('riwayat_akreditasi_internasional_series.lembaga')
                //         ->groupBy('prodi.id_prodi')
                //         ->get();
                // dd($prodi); 

                // Peta sebaran nasional dihitung di browser dari data tabel (JSON dataNasional),
                // sehingga mengikuti filter yang sedang aktif; di sini hanya urutan baris jenjang.
                $urutan_jenjang = DB::table('jenjang')->orderBy('id_jenjang')->pluck('nama_jenjang');

                // Kolom peta = semua peringkat di tabel referensi: urutan tetap seperti tampilan lama,
                // peringkat lain menyusul, Persiapan (O) & Proses Reakreditasi (OO) selalu di akhir.
                $awal = ['TU', 'U', 'A', 'BS', 'BA', 'B', 'C', 'TM', 'S', 'T', 'TP', 'TS'];
                $akhir = ['OO', 'O'];
                $kolom_peta = DB::table('peringkat')->orderBy('urutan')->get(['kode', 'akreditasi'])
                        ->sortBy(fn ($p) => in_array($p->kode, $akhir)
                                ? 1000 + array_search($p->kode, $akhir)
                                : (in_array($p->kode, $awal) ? array_search($p->kode, $awal) : 500))
                        ->map(fn ($p) => ['kode' => $p->kode, 'label' => ucwords(strtolower($p->akreditasi))])
                        ->values();

                // dd($total_nas);
                return view(
                        'beranda',
                        ['judul' => 'Akreditasi Program Studi'],
                        compact('urutan_jenjang', 'kolom_peta')
                );
        }


        // Halaman publik akreditasi internasional (tampilan sama dengan halaman akreditasi prodi)
        public function internasional()
        {
                StatusInternasional::perbaruiHarian();

                return view(
                        'internasional',
                        [
                                'judul' => 'Akreditasi Internasional',
                                'urutan_jenjang' => DB::table('jenjang')->orderBy('id_jenjang')->pluck('nama_jenjang'),
                                'kolom_peta' => DB::table('nama_lembaga_internasional')->orderBy('id_akre')->pluck('nama_lembaga'),
                        ]
                );
        }

        // Sumber data Ajax tabel akreditasi internasional: hanya keterangan "Aktif" dari prodi aktif
        public function dataInternasional()
        {
                StatusInternasional::perbaruiHarian();

                $data = DB::table('riwayat_akreditasi_internasional_series as a')
                        ->select('a.id_riwayat', 'a.url', 'b.nama_prodi', 'c.nama_jenjang', 'd.nm_fakultas', 'e.nama_lembaga',
                                'a.tgl_berlaku as start_date', 'a.tgl_berakhir as end_date')
                        ->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
                        ->leftJoin('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
                        ->leftJoin('fakultas as d', 'b.id_fakultas', '=', 'd.id_fakultas')
                        ->leftJoin('nama_lembaga_internasional as e', 'a.lembaga', '=', 'e.id_akre')
                        ->where('a.keterangan', '=', StatusInternasional::AKTIF)
                        ->where('b.status', '=', 1)
                        ->get()
                        ->map(function ($row) {
                                // url asli tidak dikirim ke browser; diganti link terenkripsi ke halaman captcha
                                $row->url = $row->url ? route('berkas.form', BerkasSk::token('i', (int) $row->id_riwayat)) : null;
                                unset($row->id_riwayat);
                                return $row;
                        });

                return response()->json(['data' => $data]);
        }

        public function detail($id_prodi)
        {
                $detail = DB::table('riwayat_akreditasi as A')
                        ->select('B.id_prodi', 'B.nama_prodi', 'D.nm_fakultas', 'C.nama_jenjang', 'I.akreditasi', 'A.start_date AS tgl_awal', 'A.end_date as tgl_akhir', 'A.url')
                        ->join('prodi as B', 'A.id_prodi', '=', 'B.id_prodi')
                        ->leftJoin('jenjang as C', 'B.id_jenjang', '=', 'C.id_jenjang')
                        ->leftJoin('fakultas as D', 'B.id_fakultas', '=', 'D.id_fakultas')
                        ->leftJoin('peringkat as I', 'A.akreditasi', '=', 'I.kode')
                        ->where('B.status', '=', '1')
                        ->where('A.id_prodi', '=', $id_prodi);

                $dokumen = DB::table('riwayat_akreditasi_dokumen as E')
                        ->select('F.id_prodi', 'F.nama_prodi', 'H.nm_fakultas', 'G.nama_jenjang', 'J.akreditasi', 'E.tgl_awal', 'E.tgl_akhir', 'E.file')
                        ->join('prodi as F', 'E.id_prodi', '=', 'F.id_prodi')
                        ->leftJoin('jenjang as G', 'F.id_jenjang', '=', 'G.id_jenjang')
                        ->leftJoin('fakultas as H', 'F.id_fakultas', '=', 'H.id_fakultas')
                        ->leftJoin('peringkat as J', 'E.akreditasi', '=', 'J.kode')
                        ->where('F.status', '=', '1')
                        ->where('E.id_prodi', '=', $id_prodi)
                        ->union($detail)
                        ->orderBy('tgl_akhir', 'desc')
                        ->get();
                // $tgl = $dokumen->tgl_akhir;
                // dd($dokumen);
                return view(
                        'detailprodi',
                        ['judul' => 'Link Download Sertifikat'],
                        compact('dokumen')
                );
        }

        public function universitas()
        {
                $univ = DB::table('riwayat_akreditasi_universitas as a')
                        ->select('a.*', 'b.akreditasi')
                        ->leftJoin('peringkat as b', 'a.id_peringkat', '=', 'b.kode')
                        ->get();

                return view(
                        'akreuniv',
                        ['judul' => 'Akreditasi Universitas'],
                        compact('univ')
                );
        }
}
