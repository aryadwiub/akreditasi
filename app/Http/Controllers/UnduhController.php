<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use function Laravel\Prompts\table;

class UnduhController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id_riwayat)
    {
        $data  = DB::table('riwayat_akreditasi_internasional_series as a')
                ->select('b.id_prodi','c.nama_jenjang', 'b.nama_prodi', 'a.url')
                ->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
                ->leftjoin('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
                ->where('a.id_riwayat', '=', $id_riwayat)                
                ->first();
        // dd($data);
        return view('unduh',
            ['judul' => 'Unduh File'],
            compact('data'));
    }

    public function history($id_prodi)
    {
        $data  = DB::table('riwayat_akreditasi_dokumen as a')
                ->select('b.id_prodi','c.nama_jenjang', 'b.nama_prodi', 'a.file')
                ->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
                ->leftjoin('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
                ->where('a.id_prodi', '=', $id_prodi)                
                ->first();
        // dd($data);
        return view('unduhhis',
            ['judul' => 'Unduh File'],
            compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
 
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    //    return $request->prodi;
        $url = $request->url;
        // dd($url);
       $form = DB::table('form_permintaan')
            ->insert([
                'nim' => $request->nim,
                'nama' => $request->nama,
                'keperluan' => $request->keperluan,
                'email' => $request->email,
                'no_hp' => $request->nohp,
                'id_prodi' => $request->prodi,
                'created_at' => \Carbon\Carbon::now(),
                'bidang' => $request->bidang
            ]);
        if($form){
            return redirect( $url );
        } else {
            return "gagal simpan";
        }
    }

    public function storehistory(Request $request)
    {
    //    return $request;
        $url = $request->url;
        // dd($url);
       $form = DB::table('form_permintaan')
            ->insert([
                'nim' => $request->nim,
                'nama' => $request->nama,
                'keperluan' => $request->keperluan,
                'email' => $request->email,
                'no_hp' => $request->nohp,
                'id_prodi' => $request->prodi,
                'created_at' => \Carbon\Carbon::now(),
                'bidang' => $request->bidang
            ]);
        if($form){
            return redirect( $url );
        } else {
            return "gagal simpan";
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return "berhasil";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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

    public function link($prodi)
    {
        return $prodi;
    }
}
