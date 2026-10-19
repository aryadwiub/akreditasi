<x-backend-layout>
    @php
        // dipakai oleh: Rekap Akreditasi Aktif ($mode=aktif), per Tanggal Nasional (nasional), per Tanggal Internasional (internasional)
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
        $labelClass = 'mb-1 block text-xs font-medium text-gray-600';
        $hariIni = now('Asia/Jakarta')->toDateString();
        $tgl = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d-m-Y') : '-';
        $kelasTgl = fn ($v) => $v && $v < $hariIni ? 'font-semibold text-red-600' : 'text-gray-700';
        $inter = $mode === 'internasional';
        $jumlahKolom = 7;
        $tombolUnduh = 'inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500';
        $ikonUnduh = '<svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" /><path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" /></svg>';
    @endphp

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $judul }}</h2>
                <p class="text-sm text-gray-500">{{ $deskripsi }}</p>
            </div>
            @if ($baris !== null)
                <span class="inline-flex w-fit items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white">
                    <span id="jumlah-rekap">{{ $baris->count() }}</span>&nbsp;{{ $inter ? 'Akreditasi' : 'Program Studi' }}
                </span>
            @endif
        </div>

        {{-- tanggal (rekap per tanggal) --}}
        @if ($aksiTanggal)
            <form method="GET" action="{{ $aksiTanggal }}" class="flex flex-col gap-2 border-b border-gray-200 px-4 py-4 sm:flex-row sm:items-end sm:px-6">
                <div class="sm:w-64">
                    <label for="tgl" class="{{ $labelClass }}">Berakhir sebelum tanggal</label>
                    <input type="date" id="tgl" name="tgl" required value="{{ $tanggal }}" class="{{ $inputClass }}" />
                </div>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Tampilkan
                </button>
                @if ($tanggal)
                    <p class="text-sm text-gray-500 sm:ms-2 sm:self-center">Menampilkan yang berakhir sebelum <b>{{ $tgl($tanggal) }}</b>.</p>
                @endif
            </form>
        @endif

        @if ($baris !== null)
            {{-- pencarian & filter --}}
            <div id="panel-filter" class="grid grid-cols-1 gap-3 border-b border-gray-200 px-4 py-3 sm:grid-cols-2 sm:px-6 lg:grid-cols-4">
                <div>
                    <label for="cari-prodi" class="{{ $labelClass }}">Cari program studi</label>
                    <input type="search" id="cari-prodi" placeholder="Nama prodi atau jenjang..." class="{{ $inputClass }}" />
                </div>
                <div>
                    <label for="filter-fakultas" class="{{ $labelClass }}">Fakultas</label>
                    <select id="filter-fakultas" class="{{ $inputClass }}">
                        <option value="">Semua Fakultas</option>
                        @foreach ($fakultas as $f)
                            <option value="{{ $f }}">{{ $f }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filter-nasional" class="{{ $labelClass }}">Lembaga Akreditasi Nasional</label>
                    <select id="filter-nasional" class="{{ $inputClass }}">
                        <option value="">Semua Lembaga</option>
                        @foreach ($lembagaNasional as $l)
                            <option value="{{ strtolower($l) }}">{{ $l }}</option>
                        @endforeach
                        <option value="-">Belum ada SK nasional</option>
                    </select>
                </div>
                <div>
                    <label for="filter-internasional" class="{{ $labelClass }}">Lembaga Akreditasi Internasional</label>
                    <select id="filter-internasional" class="{{ $inputClass }}">
                        <option value="">Semua Lembaga</option>
                        @foreach ($lembagaInter as $l)
                            <option value="{{ strtolower($l) }}">{{ $l }}</option>
                        @endforeach
                        @unless ($inter)
                            <option value="-">Tidak ada akreditasi internasional</option>
                        @endunless
                    </select>
                </div>
                <div class="flex flex-col gap-2 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between lg:col-span-4">
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <label for="per-halaman">Tampilkan</label>
                        <select id="per-halaman" class="rounded-lg border border-gray-300 bg-white py-1 pl-2 pr-7 text-xs focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            @foreach ([10, 25, 50, 100] as $n)
                                <option value="{{ $n }}" @selected($n === 25)>{{ $n }}</option>
                            @endforeach
                            <option value="0">Semua</option>
                        </select>
                        <span>baris per halaman</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- unduh mengikuti tanggal, pencarian & filter yang sedang dipakai --}}
                        <a id="unduh-xlsx" href="#" class="{{ $tombolUnduh }} bg-green-700 hover:bg-green-800">{!! $ikonUnduh !!} Excel</a>
                        <a id="unduh-pdf" href="#" class="{{ $tombolUnduh }} bg-red-700 hover:bg-red-800">{!! $ikonUnduh !!} PDF</a>
                        <button type="button" id="reset-filter"
                            class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Reset filter</button>
                    </div>
                </div>
            </div>

            {{-- tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                        <tr>
                            <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                            <th scope="col" class="px-3 py-3 sm:px-4">Program Studi</th>
                            <th scope="col" class="hidden px-3 py-3 sm:px-4 md:table-cell">Fakultas</th>
                            @if ($inter)
                                <th scope="col" class="px-3 py-3 sm:px-4">Lembaga Internasional</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Berakhir</th>
                                <th scope="col" class="px-3 py-3 text-center sm:px-4">Akreditasi Nasional</th>
                                <th scope="col" class="px-3 py-3 sm:px-4">Lembaga Nasional</th>
                            @else
                                <th scope="col" class="px-3 py-3 text-center sm:px-4">Akreditasi Nasional</th>
                                <th scope="col" class="px-3 py-3 sm:px-4">Lembaga Nasional</th>
                                <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Berlaku s/d</th>
                                <th scope="col" class="px-3 py-3 sm:px-4">Akreditasi Internasional</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($baris as $r)
                            @php
                                $daftarInter = collect($r->inter);
                                $nilaiInter = $daftarInter->isEmpty() ? '-' : $daftarInter->pluck('lembaga')->map(fn ($l) => strtolower((string) $l))->implode('|');
                            @endphp
                            <tr class="baris-rekap transition-colors hover:bg-gray-100"
                                data-nama="{{ strtolower($r->nama_jenjang . ' ' . $r->nama_prodi) }}"
                                data-fakultas="{{ $r->nm_fakultas }}"
                                data-nasional="{{ $r->nas_lembaga ? strtolower($r->nas_lembaga) : '-' }}"
                                data-internasional="{{ $nilaiInter }}">
                                <td class="nomor-rekap px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3 sm:px-4">
                                    <a href="{{ url('prodidetail/' . $r->id_prodi) }}" class="font-medium text-gray-900 hover:text-indigo-600 hover:underline">{{ $r->nama_jenjang }} {{ $r->nama_prodi }}</a>
                                    <div class="text-xs text-gray-500 md:hidden">{{ $r->nm_fakultas }}</div>
                                </td>
                                <td class="hidden px-3 py-3 sm:px-4 md:table-cell">{{ $r->nm_fakultas }}</td>
                                @if ($inter)
                                    <td class="px-3 py-3 font-medium text-gray-900 sm:px-4">{{ $r->inter[0]['lembaga'] ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4 {{ $kelasTgl($r->inter_end) }}">{{ $tgl($r->inter_end) }}</td>
                                    <td class="px-3 py-3 text-center sm:px-4">{{ $r->nas_akreditasi ?? '-' }}</td>
                                    <td class="px-3 py-3 sm:px-4">{{ $r->nas_lembaga ?: '-' }}</td>
                                @else
                                    <td class="px-3 py-3 text-center font-medium text-gray-900 sm:px-4">{{ $r->nas_akreditasi ?? '-' }}</td>
                                    <td class="px-3 py-3 sm:px-4">{{ $r->nas_lembaga ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4 {{ $kelasTgl($r->nas_end) }}">
                                        {{ $tgl($r->nas_end) }}
                                        @if ($mode === 'aktif' && ($sisa = \App\Http\Controllers\RekapController::sisaMasaAktif($r->nas_end)))
                                            <span class="mt-0.5 block text-xs font-semibold {{ $sisa === 'Expired' ? 'text-red-600' : 'text-amber-600' }}">{{ $sisa }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 sm:px-4">
                                        @forelse ($daftarInter as $i)
                                            <span class="mb-0.5 inline-flex whitespace-nowrap rounded bg-indigo-50 px-1.5 py-0.5 text-xs font-medium text-indigo-700"
                                                title="Berlaku s/d {{ $tgl($i['tgl_berakhir']) }}">{{ $i['lembaga'] }} &middot; {{ $tgl($i['tgl_berakhir']) }}</span>
                                        @empty
                                            <span class="text-gray-400">-</span>
                                        @endforelse
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        <tr id="rekap-kosong" class="{{ $baris->isEmpty() ? '' : 'hidden' }}">
                            <td colspan="{{ $jumlahKolom }}" class="px-6 py-8 text-center text-gray-500">Tidak ada data yang sesuai.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex flex-col items-center justify-between gap-2 border-t border-gray-200 px-4 py-3 text-sm sm:flex-row sm:px-6">
                <span id="info-halaman" class="text-gray-500"></span>
                <div class="flex items-center gap-2">
                    <button type="button" id="hal-sebelum" class="rounded-lg border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">&laquo; Sebelumnya</button>
                    <span id="hal-posisi" class="text-xs text-gray-500"></span>
                    <button type="button" id="hal-berikut" class="rounded-lg border border-gray-300 px-3 py-1.5 text-gray-700 hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-40">Berikutnya &raquo;</button>
                </div>
            </div>
            <p class="border-t border-gray-200 px-4 py-3 text-xs text-gray-500 sm:px-6">
                Tanggal berwarna merah sudah lewat hari ini. Klik nama prodi untuk membuka detailnya.
            </p>
        @else
            <p class="px-6 py-10 text-center text-sm text-gray-500">Pilih tanggal, lalu klik <b>Tampilkan</b>.</p>
        @endif
    </article>

    @if ($baris !== null)
        <script>
            (function () {
                const el = (id) => document.getElementById(id);
                const cari = el('cari-prodi'), fakultas = el('filter-fakultas'), nasional = el('filter-nasional'), internasional = el('filter-internasional');
                const perHalaman = el('per-halaman');
                const baris = Array.from(document.querySelectorAll('.baris-rekap'));
                let halaman = 1;

                // filter -> daftar baris yang cocok
                function cocok(tr) {
                    const d = tr.dataset;
                    return d.nama.includes(cari.value.trim().toLowerCase())
                        && (!fakultas.value || d.fakultas === fakultas.value)
                        && (!nasional.value || d.nasional === nasional.value)
                        && (!internasional.value || d.internasional.split('|').includes(internasional.value));
                }

                function tampilkan() {
                    const lolos = baris.filter(cocok);
                    const n = parseInt(perHalaman.value, 10) || lolos.length || 1;
                    const jumlahHal = Math.max(1, Math.ceil(lolos.length / n));
                    halaman = Math.min(Math.max(1, halaman), jumlahHal);
                    const awal = (halaman - 1) * n, akhir = Math.min(awal + n, lolos.length);

                    baris.forEach((tr) => tr.classList.add('hidden'));
                    lolos.forEach(function (tr, i) {
                        tr.querySelector('.nomor-rekap').textContent = i + 1;
                        tr.classList.toggle('bg-gray-50', i % 2 === 1);
                        if (i >= awal && i < akhir) tr.classList.remove('hidden');
                    });

                    el('jumlah-rekap').textContent = lolos.length;
                    el('rekap-kosong').classList.toggle('hidden', lolos.length > 0);
                    el('info-halaman').textContent = lolos.length
                        ? 'Menampilkan ' + (awal + 1) + '–' + akhir + ' dari ' + lolos.length + ' data' + (lolos.length < baris.length ? ' (difilter dari ' + baris.length + ')' : '')
                        : 'Tidak ada data';
                    el('hal-posisi').textContent = 'Halaman ' + halaman + ' / ' + jumlahHal;
                    el('hal-sebelum').disabled = halaman <= 1;
                    el('hal-berikut').disabled = halaman >= jumlahHal;

                    // tombol unduh mengikuti tanggal & filter yang sedang dipakai (bukan halaman)
                    const param = new URLSearchParams({ jenis: @json($mode) });
                    @if ($tanggal) param.set('tgl', @json($tanggal)); @endif
                    [['cari', cari.value.trim()], ['fakultas', fakultas.value], ['nasional', nasional.value], ['internasional', internasional.value]]
                        .forEach(([k, v]) => v && param.set(k, v));
                    ['xlsx', 'pdf'].forEach(function (format) {
                        param.set('format', format);
                        el('unduh-' + format).href = @json(url('rekap/unduh')) + '?' + param.toString();
                    });
                }

                const ulang = () => { halaman = 1; tampilkan(); };
                cari.addEventListener('input', ulang);
                [fakultas, nasional, internasional, perHalaman].forEach((s) => s.addEventListener('change', ulang));
                el('hal-sebelum').addEventListener('click', () => { halaman--; tampilkan(); });
                el('hal-berikut').addEventListener('click', () => { halaman++; tampilkan(); });
                el('reset-filter').addEventListener('click', function () {
                    [cari, fakultas, nasional, internasional].forEach((x) => (x.value = ''));
                    ulang();
                });
                tampilkan();
            })();
        </script>
    @endif
</x-backend-layout>
