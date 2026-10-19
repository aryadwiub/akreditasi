<x-backend-layout>
    @php
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
        $namaAksi = ['tambah_periode' => ['Tambah periode', 'bg-gray-800 text-white'], 'ubah_proses' => ['Ubah proses', 'bg-indigo-50 text-indigo-700'],
            'ubah_periode' => ['Ubah periode', 'bg-amber-100 text-amber-800'], 'hapus_periode' => ['Hapus periode', 'bg-red-100 text-red-700']];
    @endphp

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Log Lembar Kerja</h2>
                <p class="text-sm text-gray-500">Riwayat transaksi pada Lembar Kerja &rarr; Prodi yang Akan Akreditasi.</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white">
                {{ number_format($total, 0, ',', '.') }} Transaksi
            </span>
        </div>

        {{-- filter --}}
        <form method="GET" action="{{ url('loglembarkerja') }}" class="grid grid-cols-1 gap-3 border-b border-gray-200 px-4 py-3 sm:grid-cols-2 sm:px-6 lg:grid-cols-5 lg:items-end">
            <div>
                <label for="dari" class="mb-1 block text-xs font-medium text-gray-600">Dari tanggal</label>
                <input type="date" id="dari" name="dari" value="{{ $filter['dari'] ?? '' }}" class="{{ $inputClass }}" />
            </div>
            <div>
                <label for="sampai" class="mb-1 block text-xs font-medium text-gray-600">Sampai tanggal</label>
                <input type="date" id="sampai" name="sampai" value="{{ $filter['sampai'] ?? '' }}" class="{{ $inputClass }}" />
            </div>
            <div>
                <label for="tahun" class="mb-1 block text-xs font-medium text-gray-600">Periode</label>
                <select id="tahun" name="tahun" class="{{ $inputClass }}">
                    <option value="">Semua periode</option>
                    @foreach ($daftarTahun as $t)
                        <option value="{{ $t }}" @selected($filter['tahun'] === (int) $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="prodi" class="mb-1 block text-xs font-medium text-gray-600">Program studi</label>
                <input type="search" id="prodi" name="prodi" maxlength="100" value="{{ $filter['prodi'] ?? '' }}" placeholder="Cari nama prodi..." class="{{ $inputClass }}" />
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Terapkan</button>
                <a href="{{ url('loglembarkerja') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100">Reset</a>
            </div>
        </form>

        {{-- tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                        <th scope="col" class="whitespace-nowrap px-3 py-3 sm:px-4">Waktu</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Pengguna</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Aksi</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Periode</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Program Studi</th>
                        <th scope="col" class="min-w-64 px-3 py-3 sm:px-4">Perubahan / Keterangan</th>
                        <th scope="col" class="hidden px-3 py-3 sm:px-4 lg:table-cell">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($log as $item)
                        @php [$labelAksi, $warnaAksi] = $namaAksi[$item->aksi] ?? [$item->aksi, 'bg-gray-200 text-gray-700']; @endphp
                        <tr class="align-top transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $log->firstItem() + $loop->index }}</td>
                            <td class="whitespace-nowrap px-3 py-3 sm:px-4">{{ \Carbon\Carbon::parse($item->dibuat_pada, 'UTC')->setTimezone(\App\Http\Controllers\LogAksesController::ZONA)->format('d-m-Y H:i:s') }} WIB</td>
                            <td class="px-3 py-3 sm:px-4">{{ $item->nama_user ?? '-' }}</td>
                            <td class="px-3 py-3 text-center sm:px-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $warnaAksi }}">{{ $labelAksi }}</span></td>
                            <td class="px-3 py-3 text-center sm:px-4">{{ $item->tahun ?? '-' }}</td>
                            <td class="px-3 py-3 font-medium text-gray-900 sm:px-4">{{ $item->nama_prodi ?? '-' }}</td>
                            <td class="px-3 py-3 sm:px-4">
                                @if ($item->aksi === 'ubah_proses')
                                    <span class="text-gray-500">{{ $item->proses_lama ?? 'Belum ada proses' }}</span>
                                    <span class="mx-1 text-gray-400">&rarr;</span>
                                    <span class="font-medium text-gray-900">{{ $item->proses_baru ?? 'Belum ada proses' }}</span>
                                @else
                                    <span class="text-gray-600">{{ $item->keterangan ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="hidden whitespace-nowrap px-3 py-3 text-gray-500 sm:px-4 lg:table-cell">{{ $item->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">Belum ada log transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($log->hasPages())
            <div class="flex flex-col items-center justify-between gap-2 border-t border-gray-200 px-4 py-3 text-sm sm:flex-row sm:px-6">
                <span class="text-gray-500">Menampilkan {{ $log->firstItem() }}&ndash;{{ $log->lastItem() }} dari {{ number_format($log->total(), 0, ',', '.') }}</span>
                <div class="flex gap-2">
                    @if ($log->onFirstPage())
                        <span class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-400">&laquo; Sebelumnya</span>
                    @else
                        <a href="{{ $log->previousPageUrl() }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-100">&laquo; Sebelumnya</a>
                    @endif
                    @if ($log->hasMorePages())
                        <a href="{{ $log->nextPageUrl() }}" class="rounded-lg border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-100">Berikutnya &raquo;</a>
                    @else
                        <span class="rounded-lg border border-gray-200 px-3 py-1.5 text-gray-400">Berikutnya &raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </article>

</x-backend-layout>
