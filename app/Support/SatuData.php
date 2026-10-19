<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

// Klien API SatuData UNAIR: login -> JWT (berlaku ~1 jam) -> Authorization: Bearer {JWT}
class SatuData
{
    private const KUNCI_TOKEN = 'satudata.token';
    private const KUNCI_PRODI = 'satudata.prodi';
    public const UMUR_DATA = 3600; // detik; data prodi dianggap segar selama 1 jam

    // daftar program studi (AUCC); disimpan di cache, diambil ulang bila sudah > UMUR_DATA atau $paksa.
    // hasil: ['data' => [...], 'diambil' => timestamp, 'peringatan' => ?string]
    public static function prodi(bool $paksa = false): array
    {
        $simpan = Cache::get(self::KUNCI_PRODI);
        if (!$paksa && $simpan && time() - $simpan['diambil'] < self::UMUR_DATA) {
            return $simpan + ['peringatan' => null];
        }

        try {
            $baru = ['data' => self::post('/api/v1/aucc-program-studi', ['limit' => 1000]), 'diambil' => time()];
            Cache::forever(self::KUNCI_PRODI, $baru);
            return $baru + ['peringatan' => null];
        } catch (\Throwable $e) {
            report($e);
            if ($simpan) {
                // API sedang bermasalah: tampilkan data terakhir yang berhasil diambil
                return $simpan + ['peringatan' => 'Gagal mengambil data terbaru dari SatuData (' . $e->getMessage() . '). Ditampilkan data terakhir yang tersimpan.'];
            }
            throw new RuntimeException('Gagal mengambil data dari SatuData: ' . $e->getMessage(), 0, $e);
        }
    }

    private static function post(string $path, array $query = []): array
    {
        $kirim = fn ($token) => Http::withToken($token)->acceptJson()->timeout(30)
            ->post(self::url($path) . ($query ? '?' . http_build_query($query) : ''));

        $respon = $kirim(self::token());
        if ($respon->status() === 401) {
            // token ditolak (kedaluwarsa/dicabut): login ulang sekali
            $respon = $kirim(self::token(true));
        }
        if (!$respon->successful()) {
            throw new RuntimeException('HTTP ' . $respon->status());
        }
        $json = $respon->json();
        if (!is_array($json) || empty($json['success']) || !is_array($json['data'] ?? null)) {
            throw new RuntimeException($json['message'] ?? 'format respon tidak dikenal');
        }
        return $json['data'];
    }

    private static function token(bool $baru = false): string
    {
        if (!$baru && ($token = Cache::get(self::KUNCI_TOKEN))) {
            return $token;
        }

        $cfg = config('services.satudata');
        if (empty($cfg['username']) || empty($cfg['password'])) {
            throw new RuntimeException('kredensial SATUDATA_USERNAME / SATUDATA_PASSWORD belum diisi di .env');
        }
        $respon = Http::asForm()->acceptJson()->timeout(20)
            ->post(self::url('/login'), ['username' => $cfg['username'], 'password' => $cfg['password']]);
        $token = $respon->json('token');
        if (!$respon->successful() || !$respon->json('success') || !is_string($token)) {
            throw new RuntimeException('login gagal (HTTP ' . $respon->status() . ')');
        }

        // simpan sampai 2 menit sebelum masa berlaku JWT habis
        $isi = json_decode(base64_decode(strtr(explode('.', $token)[1] ?? '', '-_', '+/')), true);
        $sisa = isset($isi['exp']) ? (int) $isi['exp'] - time() - 120 : 600;
        if ($sisa > 0) {
            Cache::put(self::KUNCI_TOKEN, $token, $sisa);
        }
        return $token;
    }

    private static function url(string $path): string
    {
        return rtrim((string) config('services.satudata.url'), '/') . $path;
    }
}
