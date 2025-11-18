<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReferensiController extends Controller
{
    public function fakultas()
    {
        $fakultas = DB::table('fakultas')
            ->get();

        return view(
            'backend/ref_fakultas',
            ['judul' => 'Akreditasi Universitas'],
            compact('fakultas')
        );
    }

    public function simpanfakultas(Request $request)
    {
        $form = DB::table('fakultas')
            ->insert([
                'nm_fakultas' => $request->nama,
                'nm_singkat' => $request->singkat
            ]);
        if ($form) {
            return redirect(url('referensifakultas'));
        } else {
            return "gagal simpan";
        }
    }

    public function ubahfakultas($id_fakultas)
    {
        $ambil = DB::table('fakultas')
            ->where('id_fakultas', '=', $id_fakultas)
            ->first();

        return view(
            'backend/ref_fakultas_ubah',
            ['judul' => 'Ubah Data Fakultas'],
            compact('ambil')
        );
    }

    public function ubahsimpanfakultas(Request $request, $id_fakultas)
    {
        $form = DB::table('fakultas')
            ->where('id_fakultas', '=', $id_fakultas)
            ->update([
                'nm_fakultas' => $request->nama,
                'nm_singkat' => $request->singkat
            ]);
        if ($form) {
            return redirect(url('referensifakultas'));
        } else {
            return "gagal simpan";
        }
    }

    public function destroyfakultas(Request $request, $id_fakultas)
    {
        $form = DB::table('fakultas')
            ->where('id_fakultas', '=', $id_fakultas)
            ->delete();

        if ($form) {
            return redirect(url('referensifakultas'));
        } else {
            return "gagal simpan";
        }
    }

    public function prodi()
    {
        $prodi = DB::table('prodi as a')
            ->join('jenjang as b', 'a.id_jenjang', '=', 'b.id_jenjang')
            ->join('fakultas as c', 'a.id_fakultas', '=', 'c.id_fakultas')
            ->get();

        $fakultas = DB::table('fakultas')
            ->get();

        $jenjang = DB::table('jenjang')
            ->get();

        return view(
            'backend/ref_prodi',
            ['judul' => 'Akreditasi Universitas'],
            compact('prodi', 'fakultas', 'jenjang')
        );
    }

    public function simpanprodi(Request $request)
    {
        $form = DB::table('prodi')
            ->insert([
                'id_fakultas' => $request->fakultas,
                'nama_prodi' => $request->prodi,
                'id_jenjang' => $request->jenjang,
                'id_cyber' => $request->id_cyber,
                'status' => $request->status,
            ]);
        if ($form) {
            return redirect(url('referensiprodi'));
        } else {
            return "gagal simpan";
        }
    }

    public function ubahprodi($id_prodi)
    {
        $ambil = DB::table('prodi as a')
            ->join('jenjang as b', 'a.id_jenjang', '=', 'b.id_jenjang')
            ->join('fakultas as c', 'a.id_fakultas', '=', 'c.id_fakultas')
            ->where('a.id_prodi', '=', $id_prodi)
            ->first();

        $fakultas = DB::table('fakultas')
            ->get();

        $jenjang = DB::table('jenjang')
            ->get();

        return view(
            'backend/ref_prodi_ubah',
            ['judul' => 'Ubah Data Program Studi'],
            compact('ambil', 'fakultas', 'jenjang')
        );
    }

    public function ubahsimpanprodi(Request $request, $id_prodi)
    {
        $form = DB::table('prodi')
            ->where('id_prodi', '=', $id_prodi)
            ->update([
                'id_fakultas' => $request->fakultas,
                'nama_prodi' => $request->prodi,
                'id_jenjang' => $request->jenjang,
                'id_cyber' => $request->id_cyber,
                'status' => $request->status
            ]);
        if ($form) {
            return redirect(url('referensiprodi'));
        } else {
            return "gagal simpan";
        }
    }

    public function destroyprodi(Request $request, $id_prodi)
    {
        // return($id_riwayat_akreditasi);
        // $id_prodi = $request->prodi;
        $form = DB::table('prodi')
            ->where('id_prodi', '=', $id_prodi)
            ->delete();

        if ($form) {
            return redirect(url('referensiprodi'));
        } else {
            return "gagal simpan";
        }
    }

    public function peringkat()
    {
        $peringkat = DB::table('peringkat')
            ->get();

        return view(
            'backend/ref_peringkat',
            ['judul' => ''],
            compact('peringkat')
        );
    }

    public function simpanperingkat(Request $request)
    {
        $form = DB::table('peringkat')
            ->insert([
                'kode' => $request->kode,
                'akreditasi' => $request->nama
            ]);
        if ($form) {
            return redirect(url('referensiperingkat'));
        } else {
            return "gagal simpan";
        }
    }

    public function ubahperingkat($kode)
    {
        $ambil = DB::table('peringkat')
            ->where('kode', '=', $kode)
            ->first();

        return view(
            'backend/ref_peringkat_ubah',
            ['judul' => 'Ubah Data Peringkat Nasional'],
            compact('ambil')
        );
    }

    public function ubahsimpanperingkat(Request $request, $kode)
    {
        $form = DB::table('peringkat')
            ->where('kode', '=', $kode)
            ->update([
                'kode' => $request->kode,
                'akreditasi' => $request->nama
            ]);
        if ($form) {
            return redirect(url('referensiperingkat'));
        } else {
            return "gagal simpan";
        }
    }

    public function destroyperingkat(Request $request, $kode)
    {
        $form = DB::table('peringkat')
            ->where('kode', '=', $kode)
            ->delete();

        if ($form) {
            return redirect(url('referensiperingkat'));
        } else {
            return "gagal simpan";
        }
    }

    public function lembaganasional()
    {
        $lembaga = DB::table('jenis_akreditasi')
            ->get();

        return view(
            'backend/ref_lembaganasional',
            ['judul' => ''],
            compact('lembaga')
        );
    }

    public function simpanlembaganasional(Request $request)
    {
        $form = DB::table('jenis_akreditasi')
            ->insert([
                'nm_akreditasi' => $request->nama
            ]);
        if ($form) {
            return redirect(url('referensilembaganasional'));
        } else {
            return "gagal simpan";
        }
    }

    public function ubahlembaganasional($id_jenis_akreditasi)
    {
        $ambil = DB::table('jenis_akreditasi')
            ->where('id_jenis_akreditasi', '=', $id_jenis_akreditasi)
            ->first();

        return view(
            'backend/ref_lembaganasional_ubah',
            ['judul' => 'Ubah Data Lembaga Nasional'],
            compact('ambil')
        );
    }

    public function ubahsimpanlembaganasional(Request $request, $id_jenis_akreditasi)
    {
        $form = DB::table('jenis_akreditasi')
            ->where('id_jenis_akreditasi', '=', $id_jenis_akreditasi)
            ->update([
                'nm_akreditasi' => $request->nama
            ]);
        if ($form) {
            return redirect(url('referensilembaganasional'));
        } else {
            return "gagal simpan";
        }
    }

        public function destroylembaganasional(Request $request, $id_jenis_akreditasi)
    {
        $form = DB::table('jenis_akreditasi')
            ->where('id_jenis_akreditasi', '=', $id_jenis_akreditasi)
            ->delete();

        if ($form) {
            return redirect(url('referensilembaganasional'));
        } else {
            return "gagal simpan";
        }
    }

        public function lembagainter()
    {
        $lembaga = DB::table('nama_lembaga_internasional')
            ->get();

        return view(
            'backend/ref_lembagainter',
            ['judul' => ''],
            compact('lembaga')
        );
    }

        public function simpanlembagainter(Request $request)
    {
        $form = DB::table('nama_lembaga_internasional')
            ->insert([
                'nama_lembaga' => $request->nama
            ]);
        if ($form) {
            return redirect(url('referensilembagainter'));
        } else {
            return "gagal simpan";
        }
    }

        public function ubahlembagainter($id_akre)
    {
        $ambil = DB::table('nama_lembaga_internasional')
            ->where('id_akre', '=', $id_akre)
            ->first();

        return view(
            'backend/ref_lembagainter_ubah',
            ['judul' => 'Ubah Data Lembaga Internasional'],
            compact('ambil')
        );
    }

        public function ubahsimpanlembagainter(Request $request, $id_akre)
    {
        $form = DB::table('nama_lembaga_internasional')
            ->where('id_akre', '=', $id_akre)
            ->update([
                'nama_lembaga' => $request->nama
            ]);
        if ($form) {
            return redirect(url('referensilembagainter'));
        } else {
            return "gagal simpan";
        }
    }

            public function destroylembagainter(Request $request, $id_akre)
    {
        $form = DB::table('nama_lembaga_internasional')
            ->where('id_akre', '=', $id_akre)
            ->delete();

        if ($form) {
            return redirect(url('referensilembagainter'));
        } else {
            return "gagal simpan";
        }
    }
}
