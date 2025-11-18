<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function rekapakreditasi()
    {
        $now = Carbon::now();
        $akreditasi = DB::table('prodi as A')
            ->select(
                'A.id_prodi',
                'A.nama_prodi',
                'D.nm_fakultas',
                'C.nama_jenjang',
                'G.akreditasi',
                'B.lembaga',
                'F.nama_lembaga',
                'E.tgl_berakhir',
                'A.status',
                DB::raw('(CASE WHEN E.lembaga = 3 AND E.tgl_berakhir > now() THEN "ASIIN"
                        WHEN E.lembaga = 4 AND E.tgl_berakhir > now() THEN "FIBAA"
                        WHEN E.lembaga = 6 AND E.tgl_berakhir > now() THEN "RSC" END) AS internasional')
            )
            ->join('riwayat_akreditasi as B', 'A.id_prodi', '=', 'B.id_prodi')
            ->leftJoin('jenjang as C', 'A.id_jenjang', '=', 'C.id_jenjang')
            ->leftJoin('fakultas as D', 'A.id_fakultas', '=', 'D.id_fakultas')
            ->leftJoin('riwayat_akreditasi_internasional_series as E', 'E.id_prodi', '=', 'A.id_prodi')
            ->leftJoin('nama_lembaga_internasional as F', 'E.lembaga', '=', 'F.id_akre')
            ->leftJoin('peringkat as G', 'B.akreditasi', '=', 'G.kode')
            ->where('A.status', '=', '1')
            ->Where('B.status', '=', '1')
            // ->orWhere('E.keterangan', '!=', '2')
            ->groupBy('A.id_prodi')
            ->orderBy('C.nama_jenjang', 'ASC')
            ->get();

        // return $akreditasi;
        return view(
            'backend/rekap_akreditasi',
            ['judul' => 'Akreditasi Universitas'],
            compact('akreditasi')
        );
    }

    public function kadal(Request $request)
    {
        // return $request;
        $kadal = 
        DB::table('prodi as a')
            ->select('a.id_prodi', 'a.nama_prodi', 'd.nama_jenjang', 'e.nm_fakultas', 'c.akreditasi', 'b.end_date', 'b.lembaga')
            ->join('riwayat_akreditasi as b', 'a.id_prodi', '=', 'b.id_prodi')
            ->leftJoin('peringkat as c', 'b.akreditasi', '=', 'c.kode')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('fakultas as e', 'a.id_fakultas', '=', 'e.id_fakultas')
            ->where('b.end_date', '<', $request->tgl)
            ->where('a.status', '=', '1')
            ->get();
        // dd($kadal);
         $tanggal = $request->tgl;
        return view(
            'backend/rekap_tanggal',
            ['judul' => 'Akreditasi Nasional'],
            compact('kadal', 'tanggal')
        );
    }

    public function tanggal()
    {
        return view(
            'backend/rekap_tanggal_awal',
            ['judul' => 'Rekap Akreditasi Nasional Berdasarkan Tanggal']
        );
    }

    public function tanggal_inter()
    {
        return view(
            'backend/rekap_tanggal_akhir_inter',
            ['judul' => 'Rekap Akreditasi Nasional Berdasarkan Tanggal']
        );
    }

       public function kadal_internasional(Request $request)
    {
        // return $request;
        $kadal = 
        DB::table('prodi as a')
            ->select('a.id_prodi', 'a.nama_prodi', 'd.nama_jenjang', 'e.nm_fakultas','g.nama_lembaga', 'f.tgl_berakhir')
            ->leftJoin('jenjang as d', 'a.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('fakultas as e', 'a.id_fakultas', '=', 'e.id_fakultas')
            ->leftJoin('riwayat_akreditasi_internasional_series as f', 'a.id_prodi', '=', 'f.id_prodi')
            ->leftJoin('nama_lembaga_internasional as g', 'f.lembaga', '=', 'g.id_akre')
            ->where('f.tgl_berakhir', '<', $request->tgl)
            ->where('a.status', '=', '1')
            ->where('f.keterangan', '=', '1')
            ->orderBy('e.nm_fakultas', 'asc')
            ->get();
        // dd($kadal);
         $tanggal = $request->tgl;
        return view(
            'backend/rekap_tanggal_internasional',
            ['judul' => 'Akreditasi Nasional'],
            compact('kadal', 'tanggal')
        );
    }
}
