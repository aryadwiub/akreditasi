<x-layout>
    <x-slot:judul>{{ $judul }}</x-slot>

    <div class="card relative shadow-md sm:rounded-lg px-4 md:px-10 lg:px-20 py-6">
        {{-- Panel pencarian & filter tabel akreditasi internasional --}}
        <div id="panel-filter" class="mb-6 rounded-lg border border-gray-200 bg-white p-4 md:p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <div>
                        <label for="cari-prodi" class="mb-1 block text-xs font-medium text-gray-600">Nama Program Studi</label>
                        <input type="search" id="cari-prodi" placeholder="Ketik nama program studi..." autocomplete="off"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="cari-fakultas" class="mb-1 block text-xs font-medium text-gray-600">Fakultas</label>
                        <input type="search" id="cari-fakultas" list="daftar-fakultas" placeholder="Ketik nama fakultas..." autocomplete="off"
                            class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                        <datalist id="daftar-fakultas"></datalist>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label for="filter-jenjang" class="mb-1 block text-xs font-medium text-gray-600">Jenjang</label>
                        <select id="filter-jenjang"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Jenjang</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-lembaga" class="mb-1 block text-xs font-medium text-gray-600">Lembaga Akreditasi Internasional</label>
                        <select id="filter-lembaga"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Lembaga</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex flex-col-reverse gap-3 border-t border-gray-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                <p id="info-filter" class="text-xs text-gray-500"></p>
                <button type="button" id="reset-filter"
                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">
                    Reset Pencarian &amp; Filter
                </button>
            </div>
        </div>

        <table class="display" id="data" style="width:100%">
            <thead class="text-xs text-center text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">NO</th>
                    <th scope="col" class="px-6 py-3">Jenjang</th>
                    <th scope="col" class="px-6 py-3">Nama Program Studi</th>
                    <th scope="col" class="px-6 py-3">Fakultas</th>
                    <th scope="col" class="px-6 py-3">Lembaga Akreditasi</th>
                    <th scope="col" class="px-6 py-3">Tanggal Berlaku</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-6 py-3">SK Akreditasi Internasional</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-4 lg:px-6">
            <div
                class="mx-auto mt-10 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 border-t border-gray-200 pt-10 sm:mt-16 sm:pt-16 lg:mx-0 lg:max-w-none lg:grid-cols-1">
                <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
                    <div class="group relative grow">
                        <h3 class="mt-3 text-lg/6 font-semibold text-gray-900">Akreditasi Internasional</h3>
                        {{-- dihitung ulang setiap tabel di atas difilter --}}
                        <p id="info-peta" class="mt-1 text-sm text-gray-500">Memuat data...</p>
                        <br>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table id="peta-internasional" class="w-full text-sm text-left rtl:text-right text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Jenjang</th>
                                        @foreach ($kolom_peta as $lembaga)
                                            <th scope="col" class="px-4 py-3 text-center">{{ $lembaga }}</th>
                                        @endforeach
                                        <th scope="col" class="px-4 py-3 text-center">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody id="peta-body"></tbody>
                                <tfoot id="peta-foot"></tfoot>
                            </table>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        const escapeHtml = (v) => $('<div>').text(v ?? '').html();

        // Peta sebaran: dihitung dari baris tabel yang lolos filter (satu baris = satu akreditasi internasional aktif)
        const urutanJenjang = @json($urutan_jenjang);
        const kolomPeta = @json($kolom_peta); // semua lembaga di tabel nama_lembaga_internasional
        const hitungPeta = (dt) => {
            const baris = dt.rows({ search: 'applied' }).data().toArray();
            const kosong = () => Array(kolomPeta.length + 1).fill(0); // + Jumlah
            const perJenjang = {};
            const total = kosong();

            baris.forEach((r) => {
                const jenjang = r.nama_jenjang || '-';
                const i = kolomPeta.indexOf(r.nama_lembaga);
                perJenjang[jenjang] = perJenjang[jenjang] || kosong();
                if (i !== -1) {
                    perJenjang[jenjang][i]++;
                    total[i]++;
                }
                perJenjang[jenjang][kolomPeta.length]++;
                total[kolomPeta.length]++;
            });

            const urut = urutanJenjang.filter((j) => perJenjang[j])
                .concat(Object.keys(perJenjang).filter((j) => !urutanJenjang.includes(j)));
            const sel = (n, tag = 'td') => '<' + tag + ' class="px-4 py-2 text-center whitespace-nowrap ' +
                (n ? 'text-gray-900' : 'text-gray-300') + '">' + n + '</' + tag + '>';

            $('#peta-body').html(urut.length ? urut.map((j, k) =>
                '<tr class="border-b border-gray-200 ' + (k % 2 ? 'bg-gray-50' : 'bg-white') + '">' +
                '<td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap">' + escapeHtml(j) + '</td>' +
                perJenjang[j].map((n) => sel(n)).join('') + '</tr>'
            ).join('') : '<tr><td colspan="' + (kolomPeta.length + 2) + '" class="px-4 py-6 text-center text-gray-500">Tidak ada akreditasi internasional yang sesuai filter.</td></tr>');

            $('#peta-foot').html(urut.length ? '<tr class="font-semibold"><th class="px-6 py-3 text-center text-gray-900">TOTAL</th>' +
                total.map((n) => sel(n, 'th')).join('') + '</tr>' : '');

            // keterangan filter yang sedang dipakai
            const filter = [
                ['Prodi', $('#cari-prodi').val()],
                ['Fakultas', $('#cari-fakultas').val()],
                ['Jenjang', $('#filter-jenjang').val()],
                ['Lembaga', $('#filter-lembaga').val()],
            ].filter(([, v]) => v).map(([k, v]) => k + ': ' + v);
            $('#info-peta').text(filter.length
                ? 'Sebaran ' + baris.length + ' akreditasi internasional aktif sesuai filter (' + filter.join(', ') + ').'
                : 'Sebaran seluruh ' + baris.length + ' akreditasi internasional aktif.');
        };

        const table = new DataTable('#data', {
            // Data dimuat via Ajax (JSON) — lihat AkreditasiController@dataInternasional
            ajax: '{{ url('/data/akreditasi-internasional') }}',
            deferRender: true,
            processing: true,
            columns: [
                { data: null, orderable: false, searchable: false, defaultContent: '' },
                { data: 'nama_jenjang', defaultContent: '-' },
                { data: 'nama_prodi', defaultContent: '-' },
                { data: 'nm_fakultas', defaultContent: '-' },
                { data: 'nama_lembaga', defaultContent: '-' },
                { data: 'start_date', type: 'date', defaultContent: '' },
                { data: 'end_date', type: 'date', defaultContent: '' },
                {
                    data: 'url',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (url, type) => {
                        if (type !== 'display') return '';
                        return url
                            ? '<a class="bg-green-600 hover:bg-green-700 text-white font-sans py-1 px-2 text-xs rounded" href="' +
                                escapeHtml(url) + '" target="_blank"><span>Lihat SK</span></a>'
                            : '<span>-- File Belum Tersedia --</span>';
                    }
                }
            ],
            // Kolom yang paling akhir disembunyikan saat layar sempit (angka kecil = prioritas tinggi)
            columnDefs: [
                { targets: [0, 2], responsivePriority: 1 },
                { targets: 7, responsivePriority: 2 },
                { targets: 4, responsivePriority: 3 },
            ],
            responsive: true,
            createdRow: (row) => {
                row.className = 'px-6 py-4 border-b text-xs font-medium text-gray-900 whitespace-wrap';
                $('td', row).addClass('px-6 py-4');
            },
            order: [
                [1, 'asc']
            ],
            orderMulti: true,
            // Kotak pencarian bawaan diganti panel pencarian & filter di atas tabel
            layout: {
                topEnd: null
            },
            initComplete: function() {
                const api = this.api();
                const isiOpsi = (kolom, el) => {
                    api.column(kolom).data().unique().sort().each((v) => {
                        if (v) el.append(new Option(v, v));
                    });
                };
                isiOpsi(3, $('#daftar-fakultas'));
                isiOpsi(4, $('#filter-lembaga'));
                // jenjang mengikuti urutan referensi, hanya yang punya data
                const ada = api.column(1).data().toArray();
                urutanJenjang.filter((j) => ada.includes(j)).forEach((j) => $('#filter-jenjang').append(new Option(j, j)));
            },
            on: {
                draw: (e) => {
                    let start = e.dt.page.info().start;
                    let info = e.dt.page.info();
                    $('#info-filter').text('Menampilkan ' + info.recordsDisplay + ' dari ' + info.recordsTotal + ' akreditasi internasional aktif');
                    hitungPeta(e.dt);

                    e.dt.column(0, {
                            page: 'current'
                        })
                        .nodes()
                        .each((cell, i) => {
                            cell.textContent = start + i + 1;
                        });
                }
            }
        });

        // Pencarian (teks, sebagian kata) dan filter (nilai persis) per kolom
        const cariKolom = (kolom, nilai, persis = false) => {
            if (persis && nilai) {
                table.column(kolom).search('^' + DataTable.util.escapeRegex(nilai) + '$', true, false);
            } else {
                table.column(kolom).search(nilai);
            }
            table.draw();
        };
        $('#cari-prodi').on('input', function() { cariKolom(2, this.value); });
        $('#cari-fakultas').on('input', function() {
            // Nama fakultas lengkap (mis. dipilih dari saran) dicocokkan persis,
            // agar "Kedokteran" tidak ikut menampilkan "Kedokteran Gigi"/"Kedokteran Hewan"
            const lengkap = $('#daftar-fakultas option').filter((i, o) => o.value === this.value).length > 0;
            cariKolom(3, this.value, lengkap);
        });
        $('#filter-jenjang').on('change', function() { cariKolom(1, this.value, true); });
        $('#filter-lembaga').on('change', function() { cariKolom(4, this.value, true); });
        $('#reset-filter').on('click', function() {
            $('#panel-filter').find('input, select').val('');
            table.columns().search('').draw();
        });
    </script>
</x-layout>
