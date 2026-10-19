<?php

namespace App\Http\Controllers;

use App\Support\BerkasSk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

/**
 * Akses publik file SK akreditasi:
 * link terenkripsi -> captcha -> dicatat di log -> file (link sementara 10 menit) atau redirect ke link lama.
 */
class BerkasController extends Controller
{
    private const SESI_CAPTCHA = 'captcha_berkas';
    private const SESI_IZIN = 'berkas_izin';

    public function form(string $token)
    {
        $data = $this->cari($token);

        $a = random_int(1, 20);
        $b = random_int(1, 20);
        session([self::SESI_CAPTCHA => ['a' => $a, 'b' => $b]]);

        return view('berkas', [
            'judul' => 'Unduh File SK Akreditasi',
            'data' => $data,
            'token' => $token,
        ]);
    }

    // gambar soal captcha (angka tidak muncul sebagai teks di HTML)
    public function captcha()
    {
        $soal = session(self::SESI_CAPTCHA);
        if (!$soal) {
            abort(404);
        }

        $img = imagecreatetruecolor(150, 46);
        $bg = imagecolorallocate($img, 243, 244, 246);
        imagefilledrectangle($img, 0, 0, 150, 46, $bg);

        // garis & titik pengganggu
        for ($i = 0; $i < 6; $i++) {
            $warna = imagecolorallocate($img, random_int(120, 200), random_int(120, 200), random_int(120, 200));
            imageline($img, random_int(0, 150), random_int(0, 46), random_int(0, 150), random_int(0, 46), $warna);
        }
        for ($i = 0; $i < 120; $i++) {
            imagesetpixel($img, random_int(0, 149), random_int(0, 45), imagecolorallocate($img, random_int(100, 220), random_int(100, 220), random_int(100, 220)));
        }

        $teks = $soal['a'] . ' + ' . $soal['b'] . ' = ?';
        $x = 10;
        foreach (str_split($teks) as $huruf) {
            $warna = imagecolorallocate($img, random_int(20, 80), random_int(20, 80), random_int(20, 80));
            imagestring($img, 5, $x, random_int(8, 22), $huruf, $warna);
            $x += 11;
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function verifikasi(Request $request, string $token)
    {
        $data = $this->cari($token);

        $soal = $request->session()->pull(self::SESI_CAPTCHA); // sekali pakai
        $jawaban = trim((string) $request->input('captcha'));

        if (!$soal || !preg_match('/^[0-9]{1,3}$/', $jawaban) || (int) $jawaban !== $soal['a'] + $soal['b']) {
            return redirect()->route('berkas.form', $token)->withErrors(['captcha' => 'Jawaban captcha salah, silakan coba lagi.']);
        }

        $adaFile = BerkasSk::ada($data->file);
        $linkValid = $data->url && filter_var($data->url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $data->url);

        if (!$adaFile && !$linkValid) {
            return redirect()->route('berkas.form', $token)->withErrors(['captcha' => 'File belum tersedia.']);
        }

        DB::table('log_akses_file')->insert([
            'sumber' => $data->sumber,
            'id_riwayat' => $data->id,
            'id_prodi' => $data->id_prodi,
            'nama_prodi' => trim($data->nama_jenjang . ' ' . $data->nama_prodi),
            'start_date' => $data->start_date,
            'end_date' => $data->end_date,
            'jenis' => $adaFile ? 'file' : 'link',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'dibuka_pada' => now(),
        ]);

        if (!$adaFile) {
            return redirect()->away($data->url);
        }

        // link file hanya berlaku 10 menit dan hanya untuk sesi yang lolos captcha
        $izin = $request->session()->get(self::SESI_IZIN, []);
        $izin[$token] = now()->addMinutes(10)->timestamp;
        $request->session()->put(self::SESI_IZIN, array_filter($izin, fn ($t) => $t > time()));

        return redirect()->to(URL::temporarySignedRoute('berkas.unduh', now()->addMinutes(10), ['token' => $token]));
    }

    public function unduh(Request $request, string $token)
    {
        $izin = $request->session()->get(self::SESI_IZIN, []);
        if (($izin[$token] ?? 0) < time()) {
            return redirect()->route('berkas.form', $token);
        }

        $data = $this->cari($token);
        if (!BerkasSk::ada($data->file)) {
            abort(404);
        }

        $nama = 'SK-Akreditasi-' . preg_replace('/[^A-Za-z0-9]+/', '-', trim($data->nama_jenjang . ' ' . $data->nama_prodi))
            . '-' . substr((string) $data->start_date, 0, 4) . '-' . substr((string) $data->end_date, 0, 4) . '.pdf';

        return response()->file(BerkasSk::pathLengkap($data->file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nama . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    private function cari(string $token): object
    {
        $isi = BerkasSk::baca($token);
        if (!$isi) {
            abort(404);
        }
        [$kode, $id] = $isi;

        if ($kode === 'n') {
            $data = DB::table('riwayat_akreditasi as a')
                ->select('a.id_riwayat_akreditasi as id', 'a.id_prodi', 'b.nama_prodi', 'c.nama_jenjang', 'a.start_date', 'a.end_date', 'a.url', 'a.file')
                ->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
                ->leftJoin('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
                ->where('a.id_riwayat_akreditasi', '=', $id)
                ->where('b.status', '=', 1)
                ->first();
        } elseif ($kode === 'i') {
            $data = DB::table('riwayat_akreditasi_internasional_series as a')
                ->select('a.id_riwayat as id', 'a.id_prodi', 'b.nama_prodi', 'c.nama_jenjang', 'a.tgl_berlaku as start_date', 'a.tgl_berakhir as end_date', 'a.url', DB::raw('NULL as file'))
                ->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
                ->leftJoin('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
                ->where('a.id_riwayat', '=', $id)
                ->where('b.status', '=', 1)
                ->first();
        } else {
            $data = DB::table('riwayat_akreditasi_dokumen as a')
                ->select('a.id', 'a.id_prodi', 'b.nama_prodi', 'c.nama_jenjang', 'a.tgl_awal as start_date', 'a.tgl_akhir as end_date', 'a.file as url', DB::raw('NULL as file'))
                ->join('prodi as b', 'a.id_prodi', '=', 'b.id_prodi')
                ->leftJoin('jenjang as c', 'b.id_jenjang', '=', 'c.id_jenjang')
                ->where('a.id', '=', $id)
                ->where('b.status', '=', 1)
                ->first();
        }

        if (!$data) {
            abort(404);
        }
        $data->sumber = BerkasSk::SUMBER[$kode];

        return $data;
    }
}
