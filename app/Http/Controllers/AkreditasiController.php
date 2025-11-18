<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Akreditasi;

class AkreditasiController extends Controller
{
    public function index()
    {
        $data = 
        DB::table('prodi')
                ->select('prodi.id_prodi', 'prodi.nama_prodi', 'fakultas.nm_fakultas', 'jenjang.nama_jenjang', 
                'peringkat.akreditasi', 'riwayat_akreditasi.url', 'riwayat_akreditasi.end_date',
                'riwayat_akreditasi.lembaga', 'prodi.status', 
                'riwayat_akreditasi_internasional_series.url as urlinter',
                'riwayat_akreditasi_internasional_series.id_riwayat',
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
                ->join('riwayat_akreditasi', 'prodi.id_prodi', '=', 'riwayat_akreditasi.id_prodi')
                ->leftJoin('peringkat', 'riwayat_akreditasi.akreditasi', '=', 'peringkat.kode')
                ->leftJoin('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
                ->leftJoin('fakultas', 'prodi.id_fakultas', '=', 'fakultas.id_fakultas')
                ->leftJoin('riwayat_akreditasi_international', 'prodi.id_prodi', '=', 'riwayat_akreditasi_international.id_prodi')
                ->leftJoin('riwayat_akreditasi_internasional_series', 'prodi.id_prodi', '=', 'riwayat_akreditasi_internasional_series.id_prodi')
                ->leftJoin('nama_lembaga_internasional', 'riwayat_akreditasi_internasional_series.lembaga', '=', 'nama_lembaga_internasional.id_akre')

                ->where('prodi.status', '=', '1')
                ->orderBy('fakultas.nm_fakultas', 'ASC')
                ->orderBy('riwayat_akreditasi_internasional_series.lembaga')
                ->groupBy('prodi.id_prodi')
                ->get(); 
                // dd($prodi); 

        $nasional = DB::table('prodi')
                    ->select('jenjang.nama_jenjang',
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "TU" THEN 1 END) AS Terakre_unggul'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "U" THEN 1 END) AS Unggul'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "A" THEN 1 END) AS A'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "BS" THEN 1 END) AS Baik_sekali'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "BA" THEN 1 END) AS Baik'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "B" THEN 1 END) AS B'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "C" THEN 1 END) AS C'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "TM" THEN 1 END) AS Minimum'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "S" THEN 1 END) AS Sementara'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "OO" THEN 1 END) AS Reakreditasi'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "O" THEN 1 END) AS Proses'))

                    ->join('riwayat_akreditasi', 'prodi.id_prodi', '=', 'riwayat_akreditasi.id_prodi')
                    ->join('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
                    ->where('prodi.status', '=', '1')
                    ->groupBy('jenjang.id_jenjang')
                    ->get();
        
        $total_nas = DB::table('prodi')
                    ->select(
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "TU" THEN 1 END) AS Terakre_unggul'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "U" THEN 1 END) AS Unggul'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "A" THEN 1 END) AS A'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "BS" THEN 1 END) AS Baik_sekali'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "BA" THEN 1 END) AS Baik'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "B" THEN 1 END) AS B'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "C" THEN 1 END) AS C'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "TM" THEN 1 END) AS Minimum'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "S" THEN 1 END) AS Sementara'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "OO" THEN 1 END) AS Reakreditasi'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi.akreditasi = "O" THEN 1 END) AS Proses'))

                    ->join('riwayat_akreditasi', 'prodi.id_prodi', '=', 'riwayat_akreditasi.id_prodi')
                    ->join('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
                    ->where('prodi.status', '=', '1')
                    ->get();

        $internasional = DB::table('riwayat_akreditasi_internasional_series')
                    ->select('jenjang.nama_jenjang',
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "1" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS AUN'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "2" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS ABEST'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "3" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS ASIIN'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "4" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS FIBAA'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "5" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS APHEA'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "6" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS RSC'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "7" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS ASIC'))

                    ->join('prodi', 'riwayat_akreditasi_internasional_series.id_prodi', '=', 'prodi.id_prodi')
                    ->rightjoin('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
                    ->where('prodi.status', '=', '1')
                    ->groupBy('jenjang.id_jenjang')
                    ->orderBy('jenjang.id_jenjang')
                    ->get();

        $total_inter = DB::table('riwayat_akreditasi_internasional_series')
                    ->select(
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "1" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS AUN'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "2" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS ABEST'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "3" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS ASIIN'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "4" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS FIBAA'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "5" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS APHEA'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "6" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS RSC'),
                    DB::raw('COUNT(CASE WHEN riwayat_akreditasi_internasional_series.lembaga = "7" AND riwayat_akreditasi_internasional_series.tgl_berakhir > now() THEN 1 END) AS ASIC'))

                    ->join('prodi', 'riwayat_akreditasi_internasional_series.id_prodi', '=', 'prodi.id_prodi')
                    ->rightjoin('jenjang', 'prodi.id_jenjang', '=', 'jenjang.id_jenjang')
                    ->where('prodi.status', '=', '1')
                    ->get();
// dd($total_nas);
        return view('beranda',
                ['judul' => 'Akreditasi Program Studi'],
                compact('data', 'nasional', 'total_nas', 'internasional', 'total_inter'));
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
        return view('detailprodi',
                ['judul' => 'Link Download Sertifikat'],
                compact('dokumen'));
    }

    public function universitas()
    {
        $univ = DB::table('riwayat_akreditasi_universitas as a')
                ->select('a.*', 'b.akreditasi')
                ->leftJoin('peringkat as b', 'a.id_peringkat', '=', 'b.kode')
                ->get();

        return view('akreuniv',
                ['judul' => 'Akreditasi Universitas'],
                compact('univ'));
    }
}
 