<x-backend-layout>
    @php
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
        $labelClass = 'mb-1 block text-xs font-medium text-gray-600';
        $status = \App\Http\Controllers\SatuDataController::STATUS;
        $jumlahStatus = $baris->countBy('status');
        $tombolUnduh = 'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500';
        $ikonUnduh = '<svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" /><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" /></svg>';
    @endphp

    @if (session('info'))
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
    @endif
    @if ($peringatan)
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ $peringatan }}</div>
    @endif

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Program Studi SatuData</h2>
                <p class="text-sm text-gray-500">
                    Data program studi dari API SatuData UNAIR, dibandingkan dengan referensi prodi lokal (kolom <code>id_cyber</code>).
                    @if ($diambil)
                        <span class="whitespace-nowrap">Diambil {{ \Carbon\Carbon::createFromTimestamp($diambil, 'Asia/Jakarta')->format('d-m-Y H:i') }} WIB.</span>
                    @endif
                </p>
            </div>
            <form method="POST" action="{{ url('referensisatudata/muat-ulang') }}">
                @csrf
                <button type="submit"
                    class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" /></svg>
                    Muat Ulang dari SatuData
                </button>
            </form>
        </div>

        @if ($gagal)
            <div class="px-6 py-10 text-center">
                <p class="font-semibold text-red-700">Data SatuData tidak dapat diambil.</p>
                <p class="mt-1 text-sm text-gray-500">{{ $gagal }}</p>
                <p class="mt-1 text-sm text-gray-500">Coba lagi beberapa saat dengan tombol <b>Muat Ulang dari SatuData</b>.</p>
            </div>
        @else
            {{-- ringkasan status (klik untuk memfilter) --}}
            <div class="grid grid-cols-2 gap-3 border-b border-gray-200 px-4 py-3 sm:grid-cols-3 sm:px-6 lg:grid-cols-6">
                <button type="button" data-status="" class="kartu-status rounded-lg px-3 py-2 text-left ring-1 ring-gray-200 hover:bg-gray-50">
                    <p class="text-xs text-gray-500">Semua</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $baris->count() }}</p>
                </button>
                @foreach ($status as $kode => [$label, $warna])
                    <button type="button" data-status="{{ $kode }}" class="kartu-status rounded-lg px-3 py-2 text-left ring-1 ring-gray-200 hover:bg-gray-50">
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $jumlahStatus[$kode] ?? 0 }}</p>
                    </button>
                @endforeach
            </div>

            {{-- filter --}}
            <div id="panel-filter" class="grid grid-cols-1 gap-3 border-b border-gray-200 px-4 py-3 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
                <div>
                    <label for="cari" class="{{ $labelClass }}">Cari prodi</label>
                    <input type="search" id="cari" maxlength="100" placeholder="Nama prodi / singkatan..." class="{{ $inputClass }}" />
                </div>
                <div>
                    <label for="filter-fakultas" class="{{ $labelClass }}">Fakultas</label>
                    <select id="filter-fakultas" class="{{ $inputClass }}">
                        <option value="">Semua fakultas</option>
                        @foreach ($baris->pluck('fakultas')->filter()->unique()->sort() as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-jenjang" class="{{ $labelClass }}">Jenjang</label>
                    <select id="filter-jenjang" class="{{ $inputClass }}">
                        <option value="">Semua jenjang</option>
                        @foreach ($baris->pluck('jenjang')->filter()->unique()->sort() as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-status" class="{{ $labelClass }}">Status pembanding</label>
                    <select id="filter-status" class="{{ $inputClass }}">
                        <option value="">Semua status</option>
                        @foreach ($status as $kode => [$label])
                            <option value="{{ $kode }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- jumlah per halaman + unduh --}}
            <div class="flex flex-col gap-2 border-b border-gray-200 px-4 py-2 text-xs text-gray-600 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-2">
                    <label for="per-halaman">Tampilkan</label>
                    <select id="per-halaman" class="rounded-lg border border-gray-300 bg-white py-1 pl-2 pr-7 text-xs focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        @foreach ([10, 25, 50, 100] as $n)
                            <option value="{{ $n }}" @selected($n === 25)>{{ $n }}</option>
                        @endforeach
                        <option value="0">Semua</option>
                    </select>
                    <span>baris</span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a id="unduh-xlsx" href="#" class="{{ $tombolUnduh }} bg-green-700 hover:bg-green-600">{!! $ikonUnduh !!} Excel</a>
                    <a id="unduh-pdf" href="#" class="{{ $tombolUnduh }} bg-red-700 hover:bg-red-600">{!! $ikonUnduh !!} PDF</a>
                    <button type="button" id="reset-filter" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 font-semibold text-gray-700 shadow-sm hover:bg-gray-100">Reset</button>
                </div>
            </div>

            {{-- tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                        <tr>
                            <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                            <th scope="col" class="px-3 py-3 text-center sm:px-4">ID AUCC</th>
                            <th scope="col" class="px-3 py-3 sm:px-4">Program Studi (SatuData)</th>
                            <th scope="col" class="hidden px-3 py-3 sm:px-4 md:table-cell">Fakultas</th>
                            <th scope="col" class="hidden px-3 py-3 text-center sm:px-4 lg:table-cell">Masa Studi Maks</th>
                            <th scope="col" class="hidden px-3 py-3 sm:px-4 lg:table-cell">Subject THE WUR</th>
                            <th scope="col" class="px-3 py-3 sm:px-4">Prodi Lokal</th>
                            <th scope="col" class="px-3 py-3 text-center sm:px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($baris as $r)
                            @php [$labelStatus, $warnaStatus] = $status[$r->status]; @endphp
                            <tr class="baris-sd transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100"
                                data-cari="{{ mb_strtolower($r->prodi . ' ' . $r->lokal_nama . ' ' . $r->singkatan) }}"
                                data-fakultas="{{ $r->fakultas }}" data-jenjang="{{ $r->jenjang }}" data-status="{{ $r->status }}">
                                <td class="nomor px-3 py-3 text-center font-medium text-gray-500 sm:px-4"></td>
                                <td class="px-3 py-3 text-center sm:px-4">{{ $r->id_aucc ?? '-' }}</td>
                                <td class="px-3 py-3 sm:px-4">
                                    @if ($r->prodi)
                                        <div class="font-medium text-gray-900">{{ $r->prodi }}
                                            @if ($r->singkatan)
                                                <span class="ml-1 rounded bg-gray-100 px-1.5 py-0.5 text-xs font-medium text-gray-600">{{ $r->singkatan }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                    <div class="text-xs text-gray-500 md:hidden">{{ $r->fakultas }}</div>
                                </td>
                                <td class="hidden px-3 py-3 sm:px-4 md:table-cell">{{ $r->fakultas ?: '-' }}
                                    @if ($r->singkatan_fakultas)
                                        <span class="text-xs text-gray-500">({{ $r->singkatan_fakultas }})</span>
                                    @endif
                                </td>
                                <td class="hidden px-3 py-3 text-center sm:px-4 lg:table-cell">{{ $r->masa_studi ? $r->masa_studi . ' smt' : '-' }}</td>
                                <td class="hidden px-3 py-3 sm:px-4 lg:table-cell">{{ $r->subject ?: '-' }}</td>
                                <td class="px-3 py-3 sm:px-4">
                                    @if ($r->lokal_id)
                                        <a href="{{ url('prodidetail/' . $r->lokal_id) }}" class="text-gray-900 hover:text-indigo-600 hover:underline">{{ $r->lokal_nama }}</a>
                                        <div class="text-xs text-gray-500">ID {{ $r->lokal_id }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center sm:px-4">
                                    <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $warnaStatus }}">{{ $labelStatus }}</span>
                                </td>
                            </tr>
                        @endforeach
                        <tr id="baris-kosong" class="hidden">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">Tidak ada data yang cocok dengan filter.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- navigasi halaman --}}
            <div class="flex flex-col items-center justify-between gap-2 border-t border-gray-200 px-4 py-3 text-sm sm:flex-row sm:px-6">
                <span id="info-halaman" class="text-gray-500"></span>
                <div class="flex items-center gap-2">
                    <button type="button" id="hal-sebelum" class="rounded-lg border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:border-gray-200 disabled:text-gray-400 disabled:hover:bg-transparent">&laquo; Sebelumnya</button>
                    <span id="hal-posisi" class="text-gray-500"></span>
                    <button type="button" id="hal-berikut" class="rounded-lg border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:border-gray-200 disabled:text-gray-400 disabled:hover:bg-transparent">Berikutnya &raquo;</button>
                </div>
            </div>

            <script>
                (() => {
                    const el = (id) => document.getElementById(id);
                    const baris = [...document.querySelectorAll('.baris-sd')];
                    const filter = { cari: el('cari'), fakultas: el('filter-fakultas'), jenjang: el('filter-jenjang'), status: el('filter-status') };
                    let halaman = 1;

                    const cocok = (tr) => tr.dataset.cari.includes(filter.cari.value.trim().toLowerCase())
                        && (!filter.fakultas.value || tr.dataset.fakultas === filter.fakultas.value)
                        && (!filter.jenjang.value || tr.dataset.jenjang === filter.jenjang.value)
                        && (!filter.status.value || tr.dataset.status === filter.status.value);

                    function tampilkan() {
                        const lolos = baris.filter(cocok);
                        const n = parseInt(el('per-halaman').value, 10) || lolos.length || 1;
                        const jumlahHal = Math.max(1, Math.ceil(lolos.length / n));
                        halaman = Math.min(halaman, jumlahHal);
                        const awal = (halaman - 1) * n;

                        baris.forEach((tr) => tr.classList.add('hidden'));
                        lolos.slice(awal, awal + n).forEach((tr, i) => {
                            tr.classList.remove('hidden');
                            tr.querySelector('.nomor').textContent = awal + i + 1;
                        });
                        el('baris-kosong').classList.toggle('hidden', lolos.length > 0);
                        el('info-halaman').textContent = lolos.length
                            ? 'Menampilkan ' + (awal + 1) + '–' + Math.min(awal + n, lolos.length) + ' dari ' + lolos.length + ' data' + (lolos.length < baris.length ? ' (difilter dari ' + baris.length + ')' : '')
                            : 'Tidak ada data';
                        el('hal-posisi').textContent = 'Halaman ' + halaman + ' / ' + jumlahHal;
                        el('hal-sebelum').disabled = halaman <= 1;
                        el('hal-berikut').disabled = halaman >= jumlahHal;

                        document.querySelectorAll('.kartu-status').forEach((k) => {
                            const aktif = k.dataset.status === filter.status.value;
                            k.classList.toggle('ring-2', aktif);
                            k.classList.toggle('ring-indigo-500', aktif);
                        });

                        const q = new URLSearchParams({ cari: filter.cari.value.trim(), fakultas: filter.fakultas.value, jenjang: filter.jenjang.value, status: filter.status.value });
                        el('unduh-xlsx').href = '{{ url('referensisatudata/unduh') }}?' + q + '&format=xlsx';
                        el('unduh-pdf').href = '{{ url('referensisatudata/unduh') }}?' + q + '&format=pdf';
                    }
                    const ulang = () => { halaman = 1; tampilkan(); };

                    filter.cari.addEventListener('input', ulang);
                    [filter.fakultas, filter.jenjang, filter.status, el('per-halaman')].forEach((s) => s.addEventListener('change', ulang));
                    document.querySelectorAll('.kartu-status').forEach((k) => k.addEventListener('click', () => { filter.status.value = k.dataset.status; ulang(); }));
                    el('reset-filter').addEventListener('click', () => { Object.values(filter).forEach((f) => { f.value = ''; }); ulang(); });
                    el('hal-sebelum').addEventListener('click', () => { halaman--; tampilkan(); });
                    el('hal-berikut').addEventListener('click', () => { halaman++; tampilkan(); });

                    tampilkan();
                })();
            </script>
        @endif
    </article>
</x-backend-layout>
