<x-backend-layout>
    @if (session('sukses'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('sukses') }}</div>
    @endif

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Lembar Kerja</p>
                <h2 class="text-lg font-semibold text-gray-900">Prodi yang Akan Akreditasi</h2>
                <p class="text-sm text-gray-500">Pemetaan program studi yang akan diakreditasi per tahun beserta progres prosesnya.</p>
            </div>
            <button type="button"
                class="buka-periode inline-flex w-fit items-center gap-1.5 rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                Tambah Periode
            </button>
        </div>

        {{-- tabel periode --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Tahun</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Jumlah Prodi</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">View</th>
                        <th scope="col" class="min-w-56 px-3 py-3 sm:px-4">Prosentase Telah Submit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($periode as $p)
                        @php $persen = $p->jumlah ? round($p->submit / $p->jumlah * 100, 1) : 0; @endphp
                        <tr class="transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 text-center sm:px-4">
                                <span class="font-semibold text-gray-900">{{ $p->tahun }}</span>
                                @if ((int) $p->tahun === $tahunIni)
                                    <span class="ml-1 inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">Tahun berjalan</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center sm:px-4">{{ $p->jumlah }}</td>
                            <td class="px-3 py-3 text-center sm:px-4">
                                <a href="{{ url('lembarkerja/' . $p->id) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100">View</a>
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200">
                                        <div class="h-2 rounded-full {{ $persen >= 100 ? 'bg-green-600' : 'bg-indigo-500' }}" style="width: {{ $persen }}%"></div>
                                    </div>
                                    <span class="w-14 text-right font-semibold text-gray-900">{{ number_format($persen, 1, ',', '.') }}%</span>
                                </div>
                                <p class="mt-0.5 text-xs text-gray-500">{{ $p->submit }} dari {{ $p->jumlah }} prodi bernilai proses &ge; {{ \App\Http\Controllers\LembarKerjaController::NILAI_SUBMIT }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada periode. Klik <b>Tambah Periode</b> untuk membuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    @include('backend.lembarkerja_form', [
        'formAksi' => url('lembarkerja'),
        'formUbah' => null,
        'dipilihAwal' => [],
        'tahunAwal' => in_array($tahunIni, $tahunTerpakai) ? 0 : $tahunIni,
    ])
</x-backend-layout>
