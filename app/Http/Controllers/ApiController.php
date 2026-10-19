<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

	public function nasional()
    {
        $nas = DB::table('riwayat_akreditasi as a')
            ->select([
                'a.id_prodi',
                'b.id_cyber',
                'b.nama_prodi',
                'd.nama_jenjang',
                'c.nm_fakultas',
                'a.akreditasi',
                'f.akreditasi as nama_akreditasi',
                'a.no_sk_akreditasi',
                'a.start_date',
                'a.end_date',
                'a.lembaga'
            ])
            ->leftJoin('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
            ->leftJoin('fakultas as c', 'b.id_fakultas', '=', 'c.id_fakultas')
            ->leftJoin('jenjang as d', 'b.id_jenjang', '=', 'd.id_jenjang')
            ->leftJoin('jenis_akreditasi as e', 'a.jenis_akreditasi', '=', 'e.id_jenis_akreditasi')
            ->leftJoin('peringkat as f', 'a.akreditasi', '=', 'f.kode')
            ->where('a.status', 1)
            ->where('b.status', 1)
            ->get();

        if ($nas->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data akreditasi nasional tidak ditemukan',
                'data'    => []
            ], 404); // Mengembalikan 404 jika kosong (opsional, tergantung kebutuhan)
        }

        return response()->json([
            'success' => true,
            'data'    => $nas
        ], 200);
    }

	public function internasional()
    {
        $inter = DB::table('riwayat_akreditasi_internasional_series as a')
    	->select([
        	'a.id_prodi', 
        	'b.id_cyber', 
        	'b.nama_prodi', 
        	'c.nama_jenjang', 
        	'd.nm_fakultas', 
        	'a.tgl_berlaku', 
        	'a.tgl_berakhir', 
        	'e.nama_lembaga'
    	])
    	->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
    	->join('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
    	->join('fakultas as d', 'b.id_fakultas', '=', 'd.id_fakultas')
    	->join('nama_lembaga_internasional as e', 'a.lembaga', '=', 'e.id_akre')
    	->where('a.tgl_berakhir', '>', now())
    	->get();

        if ($inter->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data akreditasi internasional tidak ditemukan',
                'data'    => []
            ], 404); // Mengembalikan 404 jika kosong (opsional, tergantung kebutuhan)
        }

        return response()->json([
            'success' => true,
            'data'    => $inter
        ], 200);
    }
}
