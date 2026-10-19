<x-backend-layout>
    @php
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
        $batas = \App\Http\Controllers\LembarKerjaController::NILAI_SUBMIT;
        $jumlah = $baris->count();
        $persen = $jumlah ? round($submit / $jumlah * 100, 1) : 0;
        $hariIni = now('Asia/Jakarta')->toDateString();
        $tglSk = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d-m-Y') : '-';
        $waktu = fn ($v) => $v ? \Carbon\Carbon::parse($v, 'UTC')->setTimezone('Asia/Jakarta')->format('d-m-Y H:i') : '-';
    @endphp

    <div class="mb-3">
        <a href="{{ url('lembarkerja') }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-gray-900">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" /></svg>
            Kembali ke daftar periode
        </a>
    </div>

    @if (session('sukses'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('sukses') }}</div>
    @endif
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">Data proses tidak valid, silakan ulangi.</div>
    @endif

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul + ringkasan --}}
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Lembar Kerja &middot; Prodi yang Akan Akreditasi</p>
                    <h2 class="text-lg font-semibold text-gray-900">Periode {{ $periode->tahun }}</h2>
                </div>
                <div class="flex gap-2">
                    <button type="button"
                        class="buka-periode inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100">
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" /><path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" /></svg>
                        Ubah Periode
                    </button>
                    <form method="POST" action="{{ url('lembarkerja/' . $periode->id) }}" id="form-hapus">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 shadow-sm transition hover:bg-red-100">
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" /></svg>
                            Hapus Periode
                        </button>
                    </form>
                </div>
            </div>
            <div class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div class="rounded-lg bg-white px-4 py-3 ring-1 ring-gray-200">
                    <p class="text-xs text-gray-500">Prodi tercentang </p>
                    <p class="text-xl font-semibold text-gray-900">{{ $jumlah }}</p>
                </div>
                <div class="rounded-lg bg-white px-4 py-3 ring-1 ring-gray-200">
                    <p class="text-xs text-gray-500">Proses telah selesai  </p>
                    <p class="text-xl font-semibold text-gray-900">{{ $submit }}</p>
                </div>
                <div class="rounded-lg bg-white px-4 py-3 ring-1 ring-gray-200">
                    <p class="text-xs text-gray-500">Prosentase telah submit</p>
                    <div class="flex items-center gap-3">
                        <p class="text-xl font-semibold text-gray-900">{{ number_format($persen, 1, ',', '.') }}%</p>
                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200">
                            <div class="h-2 rounded-full {{ $persen >= 100 ? 'bg-green-600' : 'bg-indigo-500' }}" style="width: {{ $persen }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ url('lembarkerja/' . $periode->id) }}" id="form-proses">
            @csrf
            @method('PUT')

            {{-- pencarian + simpan --}}
            <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="w-full sm:max-w-sm">
                    <label for="cari-prodi" class="sr-only">Cari prodi</label>
                    <input type="search" id="cari-prodi" placeholder="Cari prodi / fakultas..." class="{{ $inputClass }}" />
                </div>
                <div class="flex items-center gap-3">
                    <span id="info-ubah" class="hidden text-xs font-medium text-amber-700"><span id="jumlah-ubah">0</span> perubahan belum disimpan</span>
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Simpan Perubahan</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-700">
                    <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                        <tr>
                            <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                            <th scope="col" class="px-3 py-3 sm:px-4">Program Studi</th>
                            <th scope="col" class="px-3 py-3 text-center sm:px-4">Akreditasi Terakhir</th>
                            <th scope="col" class="px-3 py-3 sm:px-4">Lembaga Akreditasi</th>
                            <th scope="col" class="min-w-40 px-3 py-3 sm:px-4">No. SK</th>
                            <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Berlaku s/d</th>
                            <th scope="col" class="min-w-56 px-3 py-3 sm:px-4">Proses Akreditasi</th>
                            <th scope="col" class="px-3 py-3 text-center sm:px-4">Nilai</th>
                            <th scope="col" class="hidden px-3 py-3 sm:px-4 lg:table-cell">Terakhir Diubah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($baris as $r)
                            <tr class="baris-proses transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100"
                                data-cari="{{ mb_strtolower($r->nama_jenjang . ' ' . $r->nama_prodi . ' ' . $r->nm_fakultas) }}">
                                <td class="nomor px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $loop->iteration }}</td>
                                <td class="px-3 py-3 sm:px-4">
                                    <div class="font-medium text-gray-900">{{ $r->nama_jenjang }} {{ $r->nama_prodi }}</div>
                                    <div class="text-xs text-gray-500">{{ $r->nm_fakultas }}</div>
                                </td>
                                <td class="px-3 py-3 text-center font-medium text-gray-900 sm:px-4">{{ $r->akreditasi ?? '-' }}</td>
                                <td class="px-3 py-3 sm:px-4">{{ $r->lembaga ?: '-' }}</td>
                                <td class="px-3 py-3 text-xs sm:px-4">{{ $r->no_sk ?: '-' }}</td>
                                <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4 {{ $r->end_date && $r->end_date < $hariIni ? 'font-semibold text-red-600' : '' }}">{{ $tglSk($r->end_date) }}</td>
                                <td class="px-3 py-3 sm:px-4">
                                    <select name="proses[{{ $r->id }}]" data-awal="{{ $r->id_proses }}" class="pilih-proses {{ $inputClass }}" aria-label="Proses {{ $r->nama_prodi }}">
                                        <option value="" data-nilai="">-- Belum ada proses --</option>
                                        @foreach ($proses as $pr)
                                            <option value="{{ $pr->id }}" data-nilai="{{ $pr->nilai }}" @selected((int) $r->id_proses === (int) $pr->id)>{{ $pr->nilai }}. {{ $pr->nama_proses }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-3 py-3 text-center sm:px-4">
                                    <span class="lencana-nilai inline-flex min-w-8 justify-center rounded-full px-2 py-0.5 text-xs font-semibold"></span>
                                </td>
                                <td class="hidden whitespace-nowrap px-3 py-3 text-xs text-gray-500 sm:px-4 lg:table-cell">
                                    @if ($r->id_proses)
                                        {{ $waktu($r->updated_at) }} WIB<br>{{ $r->diubah_oleh ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-8 text-center text-gray-500">Tidak ada prodi pada periode ini.</td>
                            </tr>
                        @endforelse
                        <tr id="baris-kosong" class="hidden">
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">Tidak ada prodi yang cocok.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- <div class="border-t border-gray-200 px-4 py-3 text-xs text-gray-500 sm:px-6">
            Prodi dihitung <b>telah submit</b> bila nilai proses &ge; {{ $batas }}. Tahapan:
            @foreach ($proses as $pr)
                <span class="whitespace-nowrap">{{ $pr->nilai }} = {{ $pr->nama_proses }}{{ $loop->last ? '.' : ',' }}</span>
            @endforeach
        </div> -->
    </article>

    <script>
        (() => {
            const batas = {{ $batas }};
            const baris = [...document.querySelectorAll('.baris-proses')];
            let simpan = false;

            const segarkan = () => {
                let ubah = 0;
                baris.forEach((tr) => {
                    const sel = tr.querySelector('.pilih-proses');
                    const nilai = sel.selectedOptions[0].dataset.nilai;
                    const lencana = tr.querySelector('.lencana-nilai');
                    lencana.textContent = nilai || '-';
                    lencana.className = 'lencana-nilai inline-flex min-w-8 justify-center rounded-full px-2 py-0.5 text-xs font-semibold ' +
                        (nilai === '' ? 'bg-gray-100 text-gray-400' : (+nilai >= batas ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800'));
                    const berubah = sel.value !== (sel.dataset.awal || '');
                    sel.classList.toggle('ring-2', berubah);
                    sel.classList.toggle('ring-amber-400', berubah);
                    if (berubah) ubah++;
                });
                document.getElementById('jumlah-ubah').textContent = ubah;
                document.getElementById('info-ubah').classList.toggle('hidden', ubah === 0);
                return ubah;
            };

            document.querySelectorAll('.pilih-proses').forEach((s) => s.addEventListener('change', segarkan));
            document.getElementById('cari-prodi').addEventListener('input', function () {
                const kata = this.value.trim().toLowerCase();
                let n = 0;
                baris.forEach((tr) => {
                    const cocok = tr.dataset.cari.includes(kata);
                    tr.classList.toggle('hidden', !cocok);
                    if (cocok) tr.querySelector('.nomor').textContent = ++n;
                });
                document.getElementById('baris-kosong').classList.toggle('hidden', n > 0 || baris.length === 0);
            });
            document.getElementById('form-proses').addEventListener('submit', () => { simpan = true; });
            document.getElementById('form-hapus').addEventListener('submit', (e) => {
                const berproses = baris.filter((tr) => tr.querySelector('.pilih-proses').dataset.awal).length;
                const pesan = 'Hapus periode {{ $periode->tahun }} beserta ' + baris.length + ' prodi di dalamnya' +
                    (berproses ? ' (' + berproses + ' prodi sudah memiliki proses)' : '') + '?\n\nTindakan ini tidak dapat dibatalkan.';
                if (confirm(pesan)) { simpan = true; } else { e.preventDefault(); }
            });
            window.addEventListener('beforeunload', (e) => { if (!simpan && segarkan() > 0) e.preventDefault(); });

            segarkan();
        })();
    </script>

    @include('backend.lembarkerja_form', [
        'formAksi' => url('lembarkerja/' . $periode->id . '/periode'),
        'formUbah' => $periode,
        'dipilihAwal' => $baris->pluck('id_prodi')->map(fn ($v) => (string) $v)->all(),
        'tahunAwal' => (int) $periode->tahun,
    ])
</x-backend-layout>
