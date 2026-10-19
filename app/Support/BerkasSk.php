<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Helper file SK akreditasi:
 * - token terenkripsi untuk link publik (id riwayat tidak terlihat / tidak bisa ditebak)
 * - simpan & hapus file upload di disk privat (storage/app/private/sk-akreditasi)
 */
class BerkasSk
{
    public const DISK = 'local';
    public const FOLDER = 'sk-akreditasi';

    // sumber data yang boleh dibuka lewat link publik
    public const SUMBER = [
        'n' => 'riwayat_akreditasi',
        'd' => 'riwayat_akreditasi_dokumen',
        'i' => 'riwayat_akreditasi_internasional_series',
    ];

    public static function token(string $kodeSumber, int $id): string
    {
        // hasil enkripsi sudah base64; diubah ke varian url-safe agar aman dipakai sebagai segmen URL
        return rtrim(strtr(Crypt::encryptString($kodeSumber . ':' . $id), '+/', '-_'), '=');
    }

    /** @return array{0: string, 1: int}|null [kode sumber, id] */
    public static function baca(string $token): ?array
    {
        if (strlen($token) > 1000 || !preg_match('/^[A-Za-z0-9_-]+$/', $token)) {
            return null;
        }

        try {
            $b64 = strtr($token, '-_', '+/');
            $isi = Crypt::decryptString($b64 . str_repeat('=', (4 - strlen($b64) % 4) % 4));
        } catch (DecryptException) {
            return null;
        }

        if (!preg_match('/^([ndi]):([0-9]{1,10})$/', $isi, $m)) {
            return null;
        }

        return [$m[1], (int) $m[2]];
    }

    public static function simpan(UploadedFile $file): string
    {
        // nama file acak (nama asli dari user tidak dipakai), ekstensi dipaksa .pdf
        return $file->storeAs(self::FOLDER, Str::random(40) . '.pdf', self::DISK);
    }

    public static function hapus(?string $path): void
    {
        if ($path && str_starts_with($path, self::FOLDER . '/')) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    public static function ada(?string $path): bool
    {
        return $path && str_starts_with($path, self::FOLDER . '/') && Storage::disk(self::DISK)->exists($path);
    }

    public static function pathLengkap(string $path): string
    {
        return Storage::disk(self::DISK)->path($path);
    }
}
