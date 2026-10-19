<x-backend-layout>
    {{-- daftar referensi (Fakultas, Prodi, Peringkat, Lembaga Nasional, Lembaga Internasional); definisi di ReferensiController::jenis() --}}
    @php
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
        $labelClass = 'mb-1 block text-xs font-medium text-gray-600';
        $statusProdi = ['1' => ['Aktif', 'bg-green-100 text-green-800'], '2' => ['Tidak Aktif', 'bg-gray-200 text-gray-700']];
        $nilaiTeks = fn ($r, $k) => $k === 'status' ? ($statusProdi[(string) $r->status][0] ?? (string) $r->status) : (string) ($r->{$k} ?? '');
        $jumlahKolom = count($j['kolom']) + 3;
        $galatTambah = $errors->tambah;
    @endphp

    @if (session('sukses'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('sukses') }}</div>
    @endif
    @if (session('gagal'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('gagal') }}</div>
    @endif

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Referensi</p>
                <h2 class="text-lg font-semibold text-gray-900">{{ $j['judul'] }}</h2>
                <p class="text-sm text-gray-500">{{ $j['deskripsi'] }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white"><span id="jumlah-data">{{ $baris->count() }}</span>&nbsp;Data</span>
                <button type="button" class="buka-tambah inline-flex items-center gap-1.5 rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" /></svg>
                    Tambah
                </button>
            </div>
        </div>

        {{-- pencarian & filter --}}
        <div id="panel-filter" class="grid grid-cols-1 gap-3 border-b border-gray-200 px-4 py-3 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:items-end">
            <div>
                <label for="cari" class="{{ $labelClass }}">Cari</label>
                <input type="search" id="cari" maxlength="100" placeholder="Ketik untuk mencari..." class="{{ $inputClass }}" />
            </div>
            @foreach ($j['filter'] ?? [] as [$kunci, $label])
                <div>
                    <label for="filter-{{ $kunci }}" class="{{ $labelClass }}">{{ $label }}</label>
                    <select id="filter-{{ $kunci }}" data-kunci="{{ $kunci }}" class="filter-kolom {{ $inputClass }}">
                        <option value="">Semua {{ mb_strtolower($label) }}</option>
                        @foreach ($baris->map(fn ($r) => $nilaiTeks($r, $kunci))->filter()->unique()->sort() as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
        </div>

        {{-- jumlah per halaman --}}
        <div class="flex items-center justify-between gap-2 border-b border-gray-200 px-4 py-2 text-xs text-gray-600 sm:px-6">
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
            <button type="button" id="reset-filter" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 font-semibold text-gray-700 shadow-sm hover:bg-gray-100">Reset</button>
        </div>

        {{-- tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                        @foreach ($j['kolom'] as [$label, $kunci, $gaya])
                            <th scope="col" class="px-3 py-3 sm:px-4 {{ in_array($gaya, ['tengah', 'kode', 'status']) ? 'text-center' : '' }} {{ $gaya === 'md' ? 'hidden md:table-cell' : '' }}">{{ $label }}</th>
                        @endforeach
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Dipakai</th>
                        <th scope="col" class="w-36 px-3 py-3 text-center sm:px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($baris as $r)
                        <tr class="baris-ref transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100"
                            data-cari="{{ mb_strtolower(collect($j['kolom'])->map(fn ($k) => $nilaiTeks($r, $k[1]))->implode(' ')) }}"
                            @foreach ($j['filter'] ?? [] as [$kunci]) data-{{ $kunci }}="{{ $nilaiTeks($r, $kunci) }}" @endforeach>
                            <td class="nomor px-3 py-3 text-center font-medium text-gray-500 sm:px-4"></td>
                            @foreach ($j['kolom'] as [$label, $kunci, $gaya])
                                @php $v = $r->{$kunci} ?? null; @endphp
                                @switch($gaya)
                                    @case('utama')
                                        <td class="px-3 py-3 font-medium text-gray-900 sm:px-4">
                                            {{ $v ?? '-' }}
                                            @if ($j['aksi'] === 'prodi')
                                                <div class="text-xs font-normal text-gray-500 md:hidden">{{ $r->nm_fakultas }}</div>
                                            @endif
                                        </td>
                                        @break
                                    @case('kode')
                                        <td class="px-3 py-3 text-center sm:px-4"><span class="inline-flex rounded bg-gray-100 px-2 py-0.5 font-mono text-xs font-semibold text-gray-800">{{ $v }}</span></td>
                                        @break
                                    @case('status')
                                        @php [$sl, $sw] = $statusProdi[(string) $v] ?? [$v, 'bg-gray-200 text-gray-700']; @endphp
                                        <td class="px-3 py-3 text-center sm:px-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium {{ $sw }}">{{ $sl }}</span></td>
                                        @break
                                    @default
                                        <td class="px-3 py-3 sm:px-4 {{ $gaya === 'tengah' ? 'text-center' : '' }} {{ $gaya === 'md' ? 'hidden md:table-cell' : '' }}">{{ ($v === null || $v === '') ? '-' : $v }}</td>
                                @endswitch
                            @endforeach
                            <td class="px-3 py-3 text-center sm:px-4">
                                @if ($r->_pakai)
                                    <span class="inline-flex rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700" title="Dipakai oleh {{ $r->_pakai }} {{ $j['pakaiLabel'] }}">{{ $r->_pakai }}</span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ url('ubah' . $j['aksi'] . '/' . $r->_id) }}"
                                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-2.5 py-1 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-100">Edit</a>
                                    @if ($r->_pakai)
                                        <span class="inline-flex cursor-not-allowed items-center rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-semibold text-gray-300"
                                            title="Tidak dapat dihapus: masih dipakai oleh {{ $r->_pakai }} {{ $j['pakaiLabel'] }}">Hapus</span>
                                    @else
                                        <form method="POST" action="{{ url('hapus' . $j['aksi'] . '/' . $r->_id) }}" class="form-hapus" data-nama="{{ $r->{$j['namaKolom']} }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 shadow-sm hover:bg-red-100">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr id="baris-kosong" class="hidden">
                        <td colspan="{{ $jumlahKolom }}" class="px-6 py-8 text-center text-gray-500">{{ $baris->isEmpty() ? 'Belum ada data.' : 'Tidak ada data yang cocok.' }}</td>
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
    </article>

    {{-- form tambah --}}
    <div id="modal-tambah" class="{{ $galatTambah->any() ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-start justify-center overflow-y-auto bg-gray-900/50 p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="judul-tambah">
        <form method="POST" action="{{ url('simpan' . $j['aksi']) }}" class="w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-xl">
            @csrf
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
                <h3 id="judul-tambah" class="text-lg font-semibold text-gray-900">Tambah {{ $j['judul'] }}</h3>
                <button type="button" class="tutup-tambah rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900" aria-label="Tutup">
                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-4">
                @foreach ($j['field'] as $nama => $f)
                    @include('backend.ref_field', ['prefix' => 'tambah-', 'nilai' => old($nama), 'kunci' => false, 'galat' => $galatTambah->first($nama)])
                @endforeach
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-3">
                <button type="button" class="tutup-tambah rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-100">Batal</button>
                <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        (() => {
            const el = (id) => document.getElementById(id);
            const baris = [...document.querySelectorAll('.baris-ref')];
            const filter = [...document.querySelectorAll('.filter-kolom')];
            const modal = el('modal-tambah');
            let halaman = 1;

            const cocok = (tr) => tr.dataset.cari.includes(el('cari').value.trim().toLowerCase())
                && filter.every((f) => !f.value || tr.dataset[f.dataset.kunci] === f.value);

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
                el('jumlah-data').textContent = lolos.length;
                el('info-halaman').textContent = lolos.length
                    ? 'Menampilkan ' + (awal + 1) + '–' + Math.min(awal + n, lolos.length) + ' dari ' + lolos.length + ' data' + (lolos.length < baris.length ? ' (difilter dari ' + baris.length + ')' : '')
                    : 'Tidak ada data';
                el('hal-posisi').textContent = 'Halaman ' + halaman + ' / ' + jumlahHal;
                el('hal-sebelum').disabled = halaman <= 1;
                el('hal-berikut').disabled = halaman >= jumlahHal;
            }
            const ulang = () => { halaman = 1; tampilkan(); };

            el('cari').addEventListener('input', ulang);
            [...filter, el('per-halaman')].forEach((s) => s.addEventListener('change', ulang));
            el('reset-filter').addEventListener('click', () => { el('cari').value = ''; filter.forEach((f) => { f.value = ''; }); ulang(); });
            el('hal-sebelum').addEventListener('click', () => { halaman--; tampilkan(); });
            el('hal-berikut').addEventListener('click', () => { halaman++; tampilkan(); });

            document.querySelectorAll('.form-hapus').forEach((f) => f.addEventListener('submit', (e) => {
                if (!confirm('Hapus "' + f.dataset.nama + '"?\n\nData yang dihapus tidak dapat dikembalikan.')) e.preventDefault();
            }));

            const buka = (tampil) => {
                modal.classList.toggle('hidden', !tampil);
                modal.classList.toggle('flex', tampil);
                document.body.classList.toggle('overflow-hidden', tampil);
                if (tampil) modal.querySelector('input:not([type=hidden]), select')?.focus();
            };
            document.querySelectorAll('.buka-tambah').forEach((b) => b.addEventListener('click', () => buka(true)));
            document.querySelectorAll('.tutup-tambah').forEach((b) => b.addEventListener('click', () => buka(false)));
            modal.addEventListener('click', (e) => { if (e.target === modal) buka(false); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') buka(false); });
            if (!modal.classList.contains('hidden')) document.body.classList.add('overflow-hidden');

            tampilkan();
        })();
    </script>
</x-backend-layout>
