<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Mengisi riwayat_akreditasi_internasional_series.keterangan:
 * "Aktif" bila tgl_berakhir >= hari ini (SK masih berlaku sampai hari terakhirnya), selain itu "Tidak Aktif".
 */
class StatusInternasional
{
    public const TABEL = 'riwayat_akreditasi_internasional_series';
    public const AKTIF = 'Aktif';
    public const TIDAK_AKTIF = 'Tidak Aktif';
    private const ZONA = 'Asia/Jakarta';

    /** @return int jumlah baris yang berubah */
    public static function perbarui(): int
    {
        $hariIni = now(self::ZONA)->toDateString();

        $aktif = DB::table(self::TABEL)
            ->where('tgl_berakhir', '>=', $hariIni)
            ->where(fn ($q) => $q->whereNull('keterangan')->orWhere('keterangan', '!=', self::AKTIF))
            ->update(['keterangan' => self::AKTIF]);

        $tidakAktif = DB::table(self::TABEL)
            ->where(fn ($q) => $q->where('tgl_berakhir', '<', $hariIni)->orWhereNull('tgl_berakhir'))
            ->where(fn ($q) => $q->whereNull('keterangan')->orWhere('keterangan', '!=', self::TIDAK_AKTIF))
            ->update(['keterangan' => self::TIDAK_AKTIF]);

        return $aktif + $tidakAktif;
    }

    // dipanggil dari halaman publik: cukup sekali per hari (tidak bergantung pada cron)
    public static function perbaruiHarian(): void
    {
        Cache::remember('status-internasional:' . now(self::ZONA)->toDateString(), now()->addDay(), fn () => self::perbarui() + 1);
    }
}
