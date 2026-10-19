{{-- form Tambah / Ubah periode (dipakai di halaman daftar periode dan halaman View) --}}
@php
    $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
    $tgl = fn ($v) => $v ? \Carbon\Carbon::parse($v)->format('d-m-Y') : null;
    $hariIni = now('Asia/Jakarta')->toDateString();
    $prosesProdi = $prosesProdi ?? [];
    $dipilih = collect(old('prodi', $dipilihAwal))->map(fn ($v) => (string) $v)->all();
    $tahunTerpilih = (int) old('tahun', $tahunAwal);
    $prodiPerFakultas = $prodi->groupBy(fn ($p) => $p->nm_fakultas ?: 'Tanpa Fakultas');
@endphp

<div id="modal-periode" class="{{ $errors->periode->any() ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-start justify-center overflow-y-auto bg-gray-900/50 p-4 sm:items-center" role="dialog" aria-modal="true" aria-labelledby="judul-periode">
    <form method="POST" action="{{ $formAksi }}" id="form-periode" class="flex max-h-[calc(100vh-2rem)] w-full max-w-3xl flex-col overflow-hidden rounded-xl bg-white shadow-xl">
        @csrf
        @if ($formUbah)
            @method('PUT')
        @endif
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h3 id="judul-periode" class="text-lg font-semibold text-gray-900">{{ $formUbah ? 'Ubah Periode ' . $formUbah->tahun : 'Tambah Periode' }}</h3>
            <button type="button" class="tutup-periode rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-900" aria-label="Tutup">
                <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" /></svg>
            </button>
        </div>

        <div class="space-y-4 overflow-y-auto px-5 py-4">
            @if ($errors->periode->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    @foreach ($errors->periode->all() as $pesan)
                        <div>{{ $pesan }}</div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <div>
                    <label for="tahun" class="mb-1 block text-xs font-medium text-gray-600">1. Tahun</label>
                    <select id="tahun" name="tahun" required class="{{ $inputClass }}">
                        <option value="">-- Pilih tahun --</option>
                        @foreach ($pilihanTahun as $t)
                            <option value="{{ $t }}" @selected($tahunTerpilih === $t) @disabled(in_array($t, $tahunTerpakai))>
                                {{ $t }}{{ in_array($t, $tahunTerpakai) ? ' (sudah ada)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label for="cari-pilihan" class="mb-1 block text-xs font-medium text-gray-600">Cari prodi / fakultas</label>
                    <input type="search" id="cari-pilihan" placeholder="Ketik untuk menyaring daftar..." class="{{ $inputClass }}" />
                </div>
            </div>

            <div>
                <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                    <p class="text-xs font-medium text-gray-600">2. Prodi yang akan diakreditasi
                        <span class="ml-1 rounded-full bg-gray-800 px-2 py-0.5 font-semibold text-white"><span id="jumlah-dipilih">0</span> dipilih</span>
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs">
                        <button type="button" id="pilih-sk" class="rounded-lg border border-amber-300 bg-amber-50 px-2.5 py-1 font-semibold text-amber-800 hover:bg-amber-100"
                            title="Centang prodi yang SK nasionalnya berakhir paling lambat pada tahun yang dipilih">Pilih SK berakhir &le; tahun ini</button>
                        <button type="button" id="pilih-semua" class="rounded-lg border border-gray-300 px-2.5 py-1 font-semibold text-gray-700 hover:bg-gray-100">Pilih semua (tersaring)</button>
                        <button type="button" id="kosongkan" class="rounded-lg border border-gray-300 px-2.5 py-1 font-semibold text-gray-700 hover:bg-gray-100">Kosongkan</button>
                    </div>
                </div>

                <div class="max-h-[50vh] overflow-y-auto rounded-lg border border-gray-200">
                    @foreach ($prodiPerFakultas as $namaFakultas => $daftar)
                        <fieldset class="grup-fakultas border-b border-gray-100 last:border-b-0">
                            <legend class="sr-only">{{ $namaFakultas }}</legend>
                            <div class="sticky top-0 z-10 flex items-center justify-between bg-gray-100 px-3 py-1.5 text-xs font-semibold uppercase tracking-wider text-gray-600">
                                <span>{{ $namaFakultas }}</span>
                                <label class="flex items-center gap-1.5 font-medium normal-case tracking-normal">
                                    <input type="checkbox" class="centang-fakultas size-3.5 rounded border-gray-300 text-gray-800 focus:ring-indigo-500"> semua
                                </label>
                            </div>
                            @foreach ($daftar as $p)
                                @php $nama = trim($p->nama_jenjang . ' ' . $p->nama_prodi); @endphp
                                <label class="pilihan-prodi flex cursor-pointer items-center gap-3 px-3 py-1.5 text-sm hover:bg-gray-50"
                                    data-cari="{{ mb_strtolower($nama . ' ' . $namaFakultas) }}" data-akhir="{{ $p->nas_end }}" data-proses="{{ $prosesProdi[$p->id_prodi] ?? '' }}">
                                    <input type="checkbox" name="prodi[]" value="{{ $p->id_prodi }}" @checked(in_array((string) $p->id_prodi, $dipilih, true))
                                        class="cek-prodi size-4 rounded border-gray-300 text-gray-800 focus:ring-indigo-500">
                                    <span class="flex-1 text-gray-900">{{ $nama }}</span>
                                    @if (isset($prosesProdi[$p->id_prodi]))
                                        <span class="whitespace-nowrap rounded bg-indigo-50 px-1.5 py-0.5 text-xs font-medium text-indigo-700">Proses: {{ $prosesProdi[$p->id_prodi] }}</span>
                                    @endif
                                    @if ($p->nas_end)
                                        <span class="whitespace-nowrap text-xs {{ $p->nas_end < $hariIni ? 'font-semibold text-red-600' : 'text-gray-500' }}">SK s/d {{ $tgl($p->nas_end) }}</span>
                                    @else
                                        <span class="whitespace-nowrap text-xs text-gray-400">Belum ada SK</span>
                                    @endif
                                </label>
                            @endforeach
                        </fieldset>
                    @endforeach
                    <p id="pilihan-kosong" class="hidden px-4 py-6 text-center text-sm text-gray-500">Tidak ada prodi yang cocok.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-5 py-3">
            <button type="button" class="tutup-periode rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-100">Batal</button>
            <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Simpan</button>
        </div>
    </form>
</div>

<script>
    (() => {
        const modal = document.getElementById('modal-periode');
        const pilihan = [...document.querySelectorAll('.pilihan-prodi')];
        const grup = [...document.querySelectorAll('.grup-fakultas')];
        const cek = (el) => el.querySelector('.cek-prodi');
        const terlihat = () => pilihan.filter((el) => !el.classList.contains('hidden'));

        const segarkan = () => {
            document.getElementById('jumlah-dipilih').textContent = pilihan.filter((el) => cek(el).checked).length;
            grup.forEach((g) => {
                const isi = [...g.querySelectorAll('.pilihan-prodi')].filter((el) => !el.classList.contains('hidden'));
                g.classList.toggle('hidden', isi.length === 0);
                g.querySelector('.centang-fakultas').checked = isi.length > 0 && isi.every((el) => cek(el).checked);
            });
            document.getElementById('pilihan-kosong').classList.toggle('hidden', terlihat().length > 0);
        };
        const buka = (tampil) => {
            modal.classList.toggle('hidden', !tampil);
            modal.classList.toggle('flex', tampil);
            document.body.classList.toggle('overflow-hidden', tampil);
        };

        document.querySelectorAll('.buka-periode').forEach((b) => b.addEventListener('click', () => buka(true)));
        document.querySelectorAll('.tutup-periode').forEach((b) => b.addEventListener('click', () => buka(false)));
        modal.addEventListener('click', (e) => { if (e.target === modal) buka(false); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') buka(false); });

        document.getElementById('cari-pilihan').addEventListener('input', function () {
            const kata = this.value.trim().toLowerCase();
            pilihan.forEach((el) => el.classList.toggle('hidden', !el.dataset.cari.includes(kata)));
            segarkan();
        });
        pilihan.forEach((el) => cek(el).addEventListener('change', segarkan));
        grup.forEach((g) => g.querySelector('.centang-fakultas').addEventListener('change', function () {
            [...g.querySelectorAll('.pilihan-prodi')].filter((el) => !el.classList.contains('hidden')).forEach((el) => { cek(el).checked = this.checked; });
            segarkan();
        }));
        document.getElementById('pilih-semua').addEventListener('click', () => { terlihat().forEach((el) => { cek(el).checked = true; }); segarkan(); });
        document.getElementById('kosongkan').addEventListener('click', () => { pilihan.forEach((el) => { cek(el).checked = false; }); segarkan(); });
        document.getElementById('pilih-sk').addEventListener('click', () => {
            const tahun = document.getElementById('tahun').value;
            if (!tahun) { alert('Pilih tahun terlebih dahulu.'); return; }
            pilihan.forEach((el) => { if (el.dataset.akhir && el.dataset.akhir <= tahun + '-12-31') cek(el).checked = true; });
            segarkan();
        });

        // ubah periode: konfirmasi bila prodi yang sudah berproses dihapus dari periode
        document.getElementById('form-periode').addEventListener('submit', (e) => {
            const hilang = pilihan.filter((el) => el.dataset.proses && !cek(el).checked)
                .map((el) => '- ' + el.querySelector('.flex-1').textContent.trim() + ' (' + el.dataset.proses + ')');
            if (hilang.length && !confirm('Prodi berikut sudah memiliki proses dan akan dihapus dari periode beserta prosesnya:\n\n' + hilang.join('\n') + '\n\nLanjutkan?')) {
                e.preventDefault();
            }
        });

        segarkan();
        if (!modal.classList.contains('hidden')) document.body.classList.add('overflow-hidden');
    })();
</script>
