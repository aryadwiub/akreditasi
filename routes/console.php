<?php

use App\Support\StatusInternasional;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('akreditasi:status-internasional', function () {
    $n = StatusInternasional::perbarui();
    $this->info("Keterangan akreditasi internasional diperbarui ({$n} baris berubah).");
})->purpose('Isi keterangan Aktif/Tidak Aktif riwayat akreditasi internasional berdasarkan tanggal berakhir');

// aktif bila cron "php artisan schedule:run" dipasang; tanpa cron, halaman publik memperbarui sekali sehari
Schedule::command('akreditasi:status-internasional')->dailyAt('00:05')->timezone('Asia/Jakarta');
