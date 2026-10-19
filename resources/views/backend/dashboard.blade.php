<x-backend-layout>
    @php
        // warna sel = lembaga akreditasi (palet sama dengan Excel/PDF, lihat App\Support\EksporPeta)
        $gayaWarna = fn (array $w) => 'background-color:#' . $w[0] . ';color:#' . $w[1];
        $warnaSel = fn ($sk) => $gayaWarna(\App\Support\EksporPeta::warnaSel($sk, $lembaga));
        // hanya fakultas yang punya prodi
        $fakultas = $fakultas->whereIn('id_fakultas', $peta->pluck('id_fakultas')->unique());
        $tombolUnduh = 'inline-flex items-center justify-center gap-1.5 rounded-lg px-3 py-2 text-sm font-semibold shadow-sm transition focus:outline-none focus:ring-2 focus:ring-indigo-500';
        $tgl = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d-m-Y') : '-';
    @endphp

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Peta Sebaran Akreditasi Nasional</h2>
                <p class="text-sm text-gray-500">Kode peringkat yang berlaku per tahun, {{ $tahun[0] }}&ndash;{{ end($tahun) }}.</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white">
                <span id="jumlah-prodi">{{ $peta->count() }}</span>&nbsp;Program Studi
            </span>
        </div>

        {{-- pencarian & filter --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:px-6">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                </svg>
                <label for="cari-prodi" class="sr-only">Cari program studi</label>
                <input type="search" id="cari-prodi" placeholder="Cari program studi atau jenjang..."
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
            </div>
            <div class="sm:w-44">
                <label for="filter-status" class="sr-only">Filter status prodi</label>
                <select id="filter-status"
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Prodi</option>
                    <option value="aktif">Prodi Aktif</option>
                    <option value="tidak-aktif">Prodi Tidak Aktif</option>
                </select>
            </div>
            <div class="sm:w-72">
                <label for="filter-fakultas" class="sr-only">Filter fakultas</label>
                <select id="filter-fakultas"
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Fakultas</option>
                    @foreach ($fakultas as $f)
                        <option value="{{ $f->id_fakultas }}">{{ $f->nm_fakultas }}</option>
                    @endforeach
                </select>
            </div>
            {{-- unduh mengikuti filter status & fakultas di sebelahnya --}}
            <div class="flex gap-2">
                <a id="unduh-xlsx" href="{{ url('dashboard/unduh?format=xlsx') }}" title="Unduh Excel sesuai filter status & fakultas"
                    class="{{ $tombolUnduh }} flex-1 bg-green-700 text-white hover:bg-green-800">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                        <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                    </svg>
                    Excel
                </a>
                <a id="unduh-pdf" href="{{ url('dashboard/unduh?format=pdf') }}" title="Unduh PDF sesuai filter status & fakultas"
                    class="{{ $tombolUnduh }} flex-1 bg-red-700 text-white hover:bg-red-800">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10.75 2.75a.75.75 0 0 0-1.5 0v8.614L6.295 8.235a.75.75 0 1 0-1.09 1.03l4.25 4.5a.75.75 0 0 0 1.09 0l4.25-4.5a.75.75 0 0 0-1.09-1.03l-2.955 3.129V2.75Z" />
                        <path d="M3.5 12.75a.75.75 0 0 0-1.5 0v2.5A2.75 2.75 0 0 0 4.75 18h10.5A2.75 2.75 0 0 0 18 15.25v-2.5a.75.75 0 0 0-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5Z" />
                    </svg>
                    PDF
                </a>
            </div>
        </div>

        {{-- legenda: warna = lembaga, teks = kode peringkat --}}
        <div class="space-y-2 border-b border-gray-200 px-4 py-3 text-xs text-gray-600 sm:px-6">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                <span class="w-36 font-semibold text-gray-700">Warna = Lembaga</span>
                @foreach ($lembaga as $nama => $w)
                    <span class="inline-flex rounded px-2 py-0.5 font-semibold" style="{{ $gayaWarna($w) }}">{{ $nama }}</span>
                @endforeach
                <span class="inline-flex rounded px-2 py-0.5 font-semibold" style="{{ $gayaWarna(\App\Support\EksporPeta::WARNA_LAIN) }}">Tidak tercatat</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                <span class="w-36 font-semibold text-gray-700">Kode = Peringkat</span>
                @foreach ($peringkat as $kode => $nama)
                    <span class="inline-flex items-center gap-1.5">
                        <span class="inline-flex min-w-8 justify-center rounded border border-gray-300 px-1.5 py-0.5 font-semibold text-gray-800">{{ $kode }}</span>
                        {{ ucwords(strtolower($nama)) }}
                    </span>
                @endforeach
                <span class="inline-flex items-center gap-1.5">
                    <span class="inline-flex min-w-8 justify-center px-1.5 py-0.5 font-semibold text-gray-300">&ndash;</span>
                    Tidak ada SK berlaku
                </span>
            </div>
        </div>

        {{-- tabel --}}
        <div class="max-h-[70vh] overflow-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="sticky top-0 z-10 bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" rowspan="2" class="sticky left-0 z-20 w-12 bg-gray-800 px-3 py-3 text-center">No</th>
                        <th scope="col" rowspan="2" class="sticky left-12 z-20 min-w-64 bg-gray-800 px-3 py-3">Nama Program Studi</th>
                        <th scope="colgroup" colspan="{{ count($tahun) }}" class="border-b border-gray-700 px-3 py-2 text-center">Tahun</th>
                    </tr>
                    <tr>
                        @foreach ($tahun as $t)
                            <th scope="col" class="{{ $t === $tahunIni ? 'bg-indigo-600' : '' }} px-2 py-2 text-center">{{ $t }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($peta as $p)
                        <tr class="baris-peta group hover:bg-gray-50"
                            data-nama="{{ strtolower($p->nama_jenjang . ' ' . $p->nama_prodi) }}"
                            data-fakultas="{{ $p->id_fakultas }}"
                            data-status="{{ $p->aktif ? 'aktif' : 'tidak-aktif' }}">
                            <td class="nomor-peta sticky left-0 bg-white px-3 py-2 text-center font-medium text-gray-500 group-hover:bg-gray-50">{{ $loop->iteration }}</td>
                            <td class="sticky left-12 bg-white px-3 py-2 group-hover:bg-gray-50">
                                <a href="{{ url('prodidetail/' . $p->id_prodi) }}" class="{{ $p->aktif ? 'text-gray-900' : 'italic text-gray-500' }} font-medium hover:text-indigo-600 hover:underline">
                                    {{ $p->nama_jenjang }} {{ $p->nama_prodi }}
                                </a>
                                @unless ($p->aktif)
                                    <span class="ms-1 inline-flex rounded bg-red-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase text-red-700">Tidak Aktif</span>
                                @endunless
                                <div class="text-xs text-gray-500">{{ $p->nm_fakultas }}</div>
                            </td>
                            @foreach ($tahun as $t)
                                @php $sk = $p->sel[$t]; @endphp
                                <td class="{{ $t === $tahunIni ? 'bg-indigo-50/60' : '' }} px-1.5 py-2 text-center">
                                    @if ($sk)
                                        <span title="{{ $peringkat[$sk->akreditasi] ?? $sk->akreditasi }} ({{ $sk->lembaga ?: 'lembaga tidak tercatat' }}): {{ $tgl($sk->mulai) }} s/d {{ $tgl($sk->end_date) }}"
                                            class="inline-flex min-w-9 justify-center rounded px-1.5 py-0.5 text-xs font-semibold" style="{{ $warnaSel($sk) }}">{{ $sk->akreditasi }}</span>
                                    @else
                                        <span class="text-gray-300">&ndash;</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr id="peta-kosong" class="hidden">
                        <td colspan="{{ count($tahun) + 2 }}" class="px-6 py-8 text-center text-gray-500">Program studi tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="border-t border-gray-200 px-4 py-3 text-xs text-gray-500 sm:px-6">
            Tanggal berlaku SK yang kosong dihitung 5 tahun sebelum tanggal berakhir.
            Arahkan kursor ke kode untuk melihat lembaga dan masa berlaku. Klik nama prodi untuk membuka detailnya.
        </p>
    </article>

    <script>
        (function () {
            const cari = document.getElementById('cari-prodi');
            const fakultas = document.getElementById('filter-fakultas');
            const status = document.getElementById('filter-status');
            const baris = document.querySelectorAll('.baris-peta');
            const kosong = document.getElementById('peta-kosong');
            const jumlah = document.getElementById('jumlah-prodi');

            function saring() {
                const kata = cari.value.trim().toLowerCase();
                const fak = fakultas.value;
                const st = status.value;
                let tampil = 0;
                baris.forEach(function (tr) {
                    const cocok = tr.dataset.nama.includes(kata) && (!fak || tr.dataset.fakultas === fak) && (!st || tr.dataset.status === st);
                    tr.classList.toggle('hidden', !cocok);
                    if (cocok) {
                        tampil++;
                        tr.querySelector('.nomor-peta').textContent = tampil;
                    }
                });
                jumlah.textContent = tampil;
                kosong.classList.toggle('hidden', tampil > 0);

                // tombol unduh mengikuti status & fakultas yang dipilih
                ['xlsx', 'pdf'].forEach(function (format) {
                    document.getElementById('unduh-' + format).href = '{{ url('dashboard/unduh') }}?format=' + format
                        + (st ? '&status=' + encodeURIComponent(st) : '')
                        + (fak ? '&fakultas=' + encodeURIComponent(fak) : '');
                });
            }

            cari.addEventListener('input', saring);
            fakultas.addEventListener('change', saring);
            status.addEventListener('change', saring);
        })();
    </script>
</x-backend-layout>
