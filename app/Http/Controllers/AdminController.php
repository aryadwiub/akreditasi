<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view(
            'backend/dashboard',
            ['judul' => 'Akreditasi Universitas']
        );
    }

    public function fakultas()
    {
        $fakultas = DB::table('fakultas')
            ->get();

        return view(
            'backend/fakultas',
            ['judul' => 'Akreditasi Universitas'],
            compact('fakultas')
        );
    }

    public function prodi($id_fakultas)
    {
        $prodi = DB::table('prodi as a')
            ->select('a.id_prodi', 'c.nama_jenjang', 'a.nama_prodi', 'nm_fakultas', 'a.status')
            ->join('fakultas as b', 'a.id_fakultas', '=', 'b.id_fakultas')
            ->join('jenjang as c', 'a.id_jenjang', '=', 'c.id_jenjang')
            ->where('a.id_fakultas', '=', $id_fakultas)
            ->orderBy('c.nama_jenjang', 'asc')
            ->get();

        return view(
            'backend/prodi',
            ['judul' => 'Akreditasi Universitas'],
            compact('prodi')
        );
    }

    public function prodidetail($id_prodi)
    {
        $detail = DB::table('prodi')
            ->select(
                'prodi.id_prodi',
                'prodi.nama_prodi',
                'fakultas.nm_fakultas',
                'jenjang.nama_jenjang',
                'peringkat.akreditasi',
                'riwayat_akreditasi.url',
                'riwayat_akreditasi.end_date',
                'riwayat_akreditasi.lembaga',
                'prodi.status',
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
            ->where('prodi.id_prodi', '=', $id_prodi)
            ->orderBy('fakultas.nm_fakultas', 'ASC')
            ->orderBy('riwayat_akreditasi_internasional_series.lembaga')
            ->groupBy('prodi.id_prodi')
            ->first();

        $riwayat_nasional = DB::table('riwayat_akreditasi')
            ->where('id_prodi', '=', $id_prodi)
            ->get();
        // dd($riwayat_nasional);

        $akreditasi = DB::table('jenis_akreditasi')
            ->get();

        $peringkat = DB::table('peringkat')
            ->orderBy('urutan', 'asc')
            ->get();

        $riwayat_internasional = DB::table('riwayat_akreditasi_internasional_series as a')
            ->join('nama_lembaga_internasional as b', 'a.lembaga', '=', 'b.id_akre')
            ->where('a.id_prodi', '=', $id_prodi)
            ->get();

        $lembaga_internasional = DB::table('nama_lembaga_internasional')
            ->get();

        return view(
            'backend/prodidetail',
            ['judul' => 'Akreditasi Universitas'],
            compact('detail', 'riwayat_nasional', 'akreditasi', 'peringkat', 'riwayat_internasional', 'lembaga_internasional')
        );
    }

    public function simpandata(Request $request)
    {

        //    return $request->file('file')->store('post-files');
        $id_prodi = $request->prodi;
        //     // dd($url);
        $form = DB::table('riwayat_akreditasi')
            ->insert([
                'id_prodi' => $request->prodi,
                'akreditasi' => $request->akreditasi,
                'no_sk_akreditasi' => $request->no_sk,
                'start_date' => $request->tgl_berlaku,
                'end_date' => $request->tgl_berakhir,
                'update_date' => \Carbon\Carbon::now(),
                'url' => $request->file,
                'lembaga' => $request->lembaga,
                'status' => $request->status
            ]);
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    public function ubah($id_riwayat_akreditasi)
    {
        // return $id_riwayat_akreditasi;
        $ambil = DB::table('riwayat_akreditasi as a')
            ->select(
                'a.id_riwayat_akreditasi',
                'a.id_prodi',
                'a.akreditasi',
                'c.kode',
                'c.akreditasi as akre',
                'a.no_sk_akreditasi',
                'a.start_date',
                'a.end_date',
                'a.url',
                'a.lembaga',
                'a.status'
            )
            ->join('jenis_akreditasi as b', 'a.lembaga', '=', 'b.nm_akreditasi')
            ->join('peringkat as c', 'a.akreditasi', '=', 'c.kode')
            ->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)
            ->first();
        // dd($ambil);
        $lembaga = DB::table('jenis_akreditasi')
            ->get();

        $peringkat = DB::table('peringkat')
            ->orderBy('urutan', 'asc')
            ->get();

        return view(
            'backend/ubahakre',
            ['judul' => 'Edit Data Akreditasi Nasional'],
            compact('ambil', 'lembaga', 'peringkat')
        );
    }

    public function ubahsimpan(Request $request, $id_riwayat_akreditasi)
    {
        // dd($id_riwayat_akreditasi);
        $id_prodi = $request->prodi;
        $form = DB::table('riwayat_akreditasi')
            ->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)
            ->update([
                'id_prodi' => $request->prodi,
                'akreditasi' => $request->akreditasi,
                'no_sk_akreditasi' => $request->no_sk,
                'start_date' => $request->tgl_berlaku,
                'end_date' => $request->tgl_berakhir,
                'update_date' => \Carbon\Carbon::now(),
                'url' => $request->file,
                'lembaga' => $request->lembaga,
                'status' => $request->status
            ]);
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi))->with('succes', 'Data sampun dirubah');
        } else {
            return "gagal simpan";
        }
    }

    public function destroy(Request $request, $id_riwayat_akreditasi)
    {
        // return($id_riwayat_akreditasi);
        $id_prodi = $request->prodi;
        $form = DB::table('riwayat_akreditasi')
            ->where('id_riwayat_akreditasi', '=', $id_riwayat_akreditasi)
            ->delete();

        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    public function simpandatainter(Request $request)
    {
        $id_prodi = $request->prodi;
        // return($request);
        $form = DB::table('riwayat_akreditasi_internasional_series')
            ->insert([
                'id_prodi' => $request->prodi,
                'lembaga' => $request->lembaga,
                'tgl_berlaku' => $request->tgl_berlaku,
                'tgl_berakhir' => $request->tgl_berakhir,
                'url' => $request->file
            ]);
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }

    public function ubahinter($id_riwayat)
    {
        // return $id_riwayat_akreditasi;
        $ambil = DB::table('riwayat_akreditasi_internasional_series as a')
            ->join('nama_lembaga_internasional as b', 'a.lembaga', '=', 'b.id_akre')
            ->where('a.id_riwayat', '=', $id_riwayat)
            ->first();
        // dd($ambil);
        $lembaga = DB::table('nama_lembaga_internasional')
            ->get();

        return view(
            'backend/ubahinter',
            ['judul' => 'Edit Data Akreditasi Internasional'],
            compact('ambil', 'lembaga')
        );
    }

    public function ubahsimpaninter(Request $request, $id_riwayat)
    {
        // dd($id_riwayat_akreditasi);
        $id_prodi = $request->prodi;
        $form = DB::table('riwayat_akreditasi_internasional_series')
            ->where('id_riwayat', '=', $id_riwayat)
            ->update([
                'id_prodi' => $request->prodi,
                'lembaga' => $request->lembaga,
                'tgl_berlaku' => $request->tgl_berlaku,
                'tgl_berakhir' => $request->tgl_berakhir,
                'url' => $request->file
            ]);
        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi))->with('succes', 'Data sampun dirubah');
        } else {
            return "gagal simpan";
        }
    }

    public function destroyinter(Request $request, $id_riwayat)
    {
        // return($id_riwayat_akreditasi);
        $id_prodi = $request->prodi;
        $form = DB::table('riwayat_akreditasi_internasional_series')
            ->where('id_riwayat', '=', $id_riwayat)
            ->delete();

        if ($form) {
            return redirect(url('prodidetail/' . $id_prodi));
        } else {
            return "gagal simpan";
        }
    }
}
