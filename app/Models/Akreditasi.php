<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Akreditasi extends Model
{
    function read(){
             $data = DB::table('prodi')
                ->select('prodi.id_prodi', 'prodi.nama_prodi', 'fakultas.nm_fakultas', 'jenjang.nama_jenjang', 
                'peringkat.akreditasi', 'riwayat_akreditasi.url', 'riwayat_akreditasi.end_date',
                'riwayat_akreditasi.lembaga', 'prodi.status', 
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
	}
}