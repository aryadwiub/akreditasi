<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BerkasController;
use App\Http\Controllers\LembarKerjaController;
use App\Http\Controllers\LogAksesController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\SatuDataController;
use App\Http\Controllers\UnduhController;
use App\Http\Controllers\ReferensiController;
use App\Http\Controllers\AkreditasiController;

Route::get('/', [AkreditasiController::class, 'index'], function () {
    // dump(request('cari'));
});
//API
Route::get('/api', [ApiController::class, 'index']);
Route::get('/data/akreditasi-nasional', [AkreditasiController::class, 'dataNasional']);
Route::get('/akreditasi-internasional', [AkreditasiController::class, 'internasional']);
Route::get('/data/akreditasi-internasional', [AkreditasiController::class, 'dataInternasional']);
//user
Route::get('/detailprodi/{id_prodi}', [AkreditasiController::class, 'detail']);
Route::get('/akreuniv', [AkreditasiController::class, 'universitas']);
Route::post('/unduh', [UnduhController::class, 'store']);
Route::post('/unduhhis', [UnduhController::class, 'storehistory']);
Route::get('/unduh/{id_prodi}', [UnduhController::class, 'index']);
Route::get('/unduhhis/{prodi}', [UnduhController::class, 'history']);

//file SK publik (link terenkripsi + captcha + log)
Route::get('/berkas-captcha', [BerkasController::class, 'captcha'])->name('berkas.captcha');
Route::get('/berkas/{token}', [BerkasController::class, 'form'])->name('berkas.form');
Route::post('/berkas/{token}', [BerkasController::class, 'verifikasi'])->middleware('throttle:100,1')->name('berkas.verifikasi');
Route::get('/berkas/{token}/unduh', [BerkasController::class, 'unduh'])->middleware('signed')->name('berkas.unduh');

//login
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'auth']);

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    //admin
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/dashboard/unduh', [AdminController::class, 'unduhPeta']);
    Route::get('/fakultas', [AdminController::class, 'fakultas']);
    Route::post('/simpandata', [AdminController::class, 'simpandata']);
    Route::post('/simpandatainter', [AdminController::class, 'simpandatainter']);
    Route::get('/prodi/{id_fakultas}', [AdminController::class, 'prodi']);
    Route::put('/ubahsimpan/{id_riwayat_akreditasi}', [AdminController::class, 'ubahsimpan']);
    Route::put('/ubahsimpaninter/{id_riwayat}', [AdminController::class, 'ubahsimpaninter']);
    Route::delete('/hapus/{id_riwayat_akreditasi}', [AdminController::class, 'destroy']);
    Route::delete('/hapusinter/{id_riwayat}', [AdminController::class, 'destroyinter']);
    Route::get('/prodidetail/{id_prodi}', [AdminController::class, 'prodidetail']);
    Route::get('/ubah/{id_riwayat_akreditasi}', [AdminController::class, 'ubah']);
    Route::get('/ubahinter/{id_riwayat}', [AdminController::class, 'ubahinter']);
    Route::get('/filesk/{id_riwayat_akreditasi}', [AdminController::class, 'lihatfile'])->whereNumber('id_riwayat_akreditasi');
    //admin log akses file publik
    Route::get('/logakses', [LogAksesController::class, 'index']);
    Route::get('/loglembarkerja', [LembarKerjaController::class, 'log']);
    //admin referensi
    Route::get('/referensifakultas', [ReferensiController::class, 'fakultas']);
    Route::post('/simpanfakultas', [ReferensiController::class, 'simpanfakultas']);
    Route::put('/ubahsimpanfakultas/{id_fakultas}', [ReferensiController::class, 'ubahsimpanfakultas']);
    Route::delete('/hapusfakultas/{id_fakultas}', [ReferensiController::class, 'destroyfakultas']);
    Route::get('/ubahfakultas/{id_fakultas}', [ReferensiController::class, 'ubahfakultas']);

    Route::delete('/hapusprodi/{id_prodi}', [ReferensiController::class, 'destroyprodi']);
    Route::post('/simpanprodi', [ReferensiController::class, 'simpanprodi']);
    Route::put('/ubahsimpanprodi/{id_prodi}', [ReferensiController::class, 'ubahsimpanprodi']);
    Route::get('/ubahprodi/{id_prodi}', [ReferensiController::class, 'ubahprodi']);
    Route::get('/referensiprodi', [ReferensiController::class, 'prodi']);

    Route::get('/ubahperingkat/{kode}', [ReferensiController::class, 'ubahperingkat']);
    Route::put('/ubahsimpanperingkat/{kode}', [ReferensiController::class, 'ubahsimpanperingkat']);
    Route::delete('/hapusperingkat/{kode}', [ReferensiController::class, 'destroyperingkat']);
    Route::post('/simpanperingkat', [ReferensiController::class, 'simpanperingkat']);
    Route::get('/referensiperingkat', [ReferensiController::class, 'peringkat']);

    Route::get('/ubahlembaganasional/{id_jenis_akreditasi}', [ReferensiController::class, 'ubahlembaganasional']);
    Route::put('/ubahsimpanlembaganasional/{id_jenis_akreditasi}', [ReferensiController::class, 'ubahsimpanlembaganasional']);
    Route::delete('/hapuslembaganasional/{id_jenis_akreditasi}', [ReferensiController::class, 'destroylembaganasional']);
    Route::post('/simpanlembaganasional', [ReferensiController::class, 'simpanlembaganasional']);
    Route::get('/referensilembaganasional', [ReferensiController::class, 'lembaganasional']);

    Route::post('/simpanlembagainter', [ReferensiController::class, 'simpanlembagainter']);
    Route::put('/ubahsimpanlembagainter/{id_akre}', [ReferensiController::class, 'ubahsimpanlembagainter']);
    Route::delete('/hapuslembagainter/{id_akre}', [ReferensiController::class, 'destroylembagainter']);
    Route::get('/ubahlembagainter/{id_akre}', [ReferensiController::class, 'ubahlembagainter']);
    Route::get('/referensilembagainter', [ReferensiController::class, 'lembagainter']);
    //admin referensi -> prodi dari API SatuData
    Route::get('/referensisatudata', [SatuDataController::class, 'prodi']);
    Route::post('/referensisatudata/muat-ulang', [SatuDataController::class, 'muatUlang'])->middleware('throttle:10,1');
    Route::get('/referensisatudata/unduh', [SatuDataController::class, 'unduh']);
    //admin referensi pengguna
    Route::get('/referensiuser', [PenggunaController::class, 'user']);
    Route::post('/simpanuser', [PenggunaController::class, 'simpanuser']);
    Route::put('/ubahsimpanuser/{id}', [PenggunaController::class, 'ubahsimpanuser'])->whereNumber('id');
    //admin rekap
    Route::get('/rekapakreditasi', [RekapController::class, 'rekapakreditasi']);
    Route::get('/rekap_tanggal_awal', [RekapController::class, 'tanggal']);
    Route::post('/kadal', [RekapController::class, 'kadal']);
    Route::get('/rekap_tanggal_awal_inter', [RekapController::class, 'tanggal_inter']);
    Route::post('/kadal_internasional', [RekapController::class, 'kadal_internasional']);
    Route::get('/rekap/unduh', [RekapController::class, 'unduh']);
    //admin rekap -> lembar kerja: prodi yang akan akreditasi
    Route::get('/lembarkerja', [LembarKerjaController::class, 'index']);
    Route::post('/lembarkerja', [LembarKerjaController::class, 'simpan']);
    Route::get('/lembarkerja/{id}', [LembarKerjaController::class, 'lihat'])->whereNumber('id');
    Route::put('/lembarkerja/{id}', [LembarKerjaController::class, 'simpanProses'])->whereNumber('id');
    Route::put('/lembarkerja/{id}/periode', [LembarKerjaController::class, 'ubahPeriode'])->whereNumber('id');
    Route::delete('/lembarkerja/{id}', [LembarKerjaController::class, 'hapus'])->whereNumber('id');
});
