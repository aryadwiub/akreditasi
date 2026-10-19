<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogAksesController extends Controller
{
    public const ZONA = 'Asia/Jakarta';

    public function index(Request $request)
    {
        // filter yang formatnya tidak valid diabaikan (tanpa redirect)
        $tanggal = fn ($v) => is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) && strtotime($v) ? $v : null;
        $filter = [
            'dari' => $tanggal($request->query('dari')),
            'sampai' => $tanggal($request->query('sampai')),
            'prodi' => is_string($request->query('prodi')) ? mb_substr(trim($request->query('prodi')), 0, 100) : null,
        ];

        // dibuka_pada tersimpan dalam UTC (zona waktu aplikasi); filter diisi dalam WIB
        $utc = fn ($v) => Carbon::parse($v, self::ZONA)->utc()->toDateTimeString();

        $query = DB::table('log_akses_file')
            ->when($filter['dari'] ?? null, fn ($q, $v) => $q->where('dibuka_pada', '>=', $utc($v . ' 00:00:00')))
            ->when($filter['sampai'] ?? null, fn ($q, $v) => $q->where('dibuka_pada', '<=', $utc($v . ' 23:59:59')))
            ->when($filter['prodi'] ?? null, fn ($q, $v) => $q->where('nama_prodi', 'like', '%' . addcslashes($v, '%_\\') . '%'));

        $total = (clone $query)->count();
        $log = $query->orderByDesc('dibuka_pada')->orderByDesc('id')->paginate(50)->withQueryString();

        return view(
            'backend/logakses',
            ['judul' => 'Log Akses File'],
            compact('log', 'total', 'filter')
        );
    }
}
