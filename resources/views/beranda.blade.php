<x-layout>
    <x-slot:judul>{{ $judul }}</x-slot>

    <div class="card relative shadow-md sm:rounded-lg px-4 md:px-10 lg:px-20 py-6">
        {{-- Panel pencarian & filter tabel akreditasi nasional --}}
        <div id="panel-filter" class="mb-6 rounded-lg border border-gray-200 bg-white p-4 md:p-6">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-4">
                    <!-- <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Pencarian</h3> -->
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
                    <!-- <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-700">Filter</h3> -->
                    <div>
                        <label for="filter-akreditasi" class="mb-1 block text-xs font-medium text-gray-600">Akreditasi Nasional</label>
                        <select id="filter-akreditasi"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="">Semua Akreditasi</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter-lembaga" class="mb-1 block text-xs font-medium text-gray-600">Lembaga Akreditasi</label>
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
         
            <thead class="text-xs text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3">NO</th>
                    <th scope="col" class="px-6 py-3">Jenjang</th>
                    <th scope="col" class="px-6 py-3">Nama Program Studi</th>
                    <th scope="col" class="px-6 py-3">Fakultas</th>
                    <th scope="col" class="px-6 py-3">Akreditasi Nasional</th>
                    <th scope="col" class="px-6 py-3">No. SK Akreditasi</th>
                    <th scope="col" class="px-6 py-3">Lembaga Akreditasi</th>
                    <th scope="col" class="px-6 py-3">Tanggal Berlaku</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-6 py-3">SK Akreditasi Nasional</th>
                    <!-- <th scope="col" class="px-6 py-3">Akreditasi Interasional</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-6 py-3">Download SK Internasional</th> -->
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
                        <h3 class="mt-3 text-lg/6 font-semibold text-gray-900">Akreditasi Nasional</h3>
                        {{-- dihitung ulang setiap tabel di atas difilter --}}
                        <p id="info-peta" class="mt-1 text-sm text-gray-500">Memuat data...</p>
                        <br>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table id="peta-nasional" class="w-full text-sm text-left rtl:text-right text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Jenjang</th>
                                        @foreach ($kolom_peta as $kolom)
                                            <th scope="col" class="px-4 py-3 text-center">{{ $kolom['label'] }}</th>
                                        @endforeach
                                        <th scope="col" class="px-4 py-3 text-center">Belum Ada SK</th>
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

    {{-- <script type="text/javascript">
        $(document).ready(function($) {
            $('#data').DataTable({
                "iDisplayLength": 10,
                initComplete: function() {
                    this.api().columns([0, 2, 3, 4, 6, 7]).every(function() {
                        var column = this;
                        var select = $(
                                '<select style="width: 100px"><option value="">Show All</option></select>'
                            )
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );

                                column
                                    .search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value="' + d + '">' + d +
                                '</option>')
                        });
                    });
                }
            });
        });
    </script> --}}

    {{-- <script type="text/javascript">
        new DataTable('#data', {
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        let column = this;

                        // Create select element
                        let select = document.createElement('select');
                        select.add(new Option(''));
                        column.header().appendChild(select);

                        // Apply listener for user change in value
                        select.addEventListener('change', function() {
                            column
                                .search(select.value, {
                                    exact: true
                                })
                                .draw();
                        });

                        // Add list of options
                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function(d, j) {
                                select.add(new Option(d));
                            });
                    });
            }
        });
    </script> --}}

    <script type="text/javascript">
        const escapeHtml = (v) => $('<div>').text(v ?? '').html();

        // Peta sebaran: dihitung dari baris tabel yang lolos filter (satu SK aktif per prodi)
        const urutanJenjang = @json($urutan_jenjang);
        const kolomPeta = @json($kolom_peta->pluck('kode')); // semua kode di tabel peringkat
        const hitungPeta = (dt) => {
            const baris = dt.rows({ search: 'applied' }).data().toArray();
            const kosong = () => Array(kolomPeta.length + 2).fill(0); // + Belum Ada SK + Jumlah
            const iBelum = kolomPeta.length, iJumlah = kolomPeta.length + 1;
            const perJenjang = {};
            const total = kosong();

            baris.forEach((r) => {
                const jenjang = r.nama_jenjang || '-';
                // prodi aktif tanpa SK aktif (kode kosong) -> kolom "Belum Ada SK"
                const i = r.kode_akreditasi ? kolomPeta.indexOf(r.kode_akreditasi) : iBelum;
                perJenjang[jenjang] = perJenjang[jenjang] || kosong();
                if (i !== -1) {
                    perJenjang[jenjang][i]++;
                    total[i]++;
                }
                perJenjang[jenjang][iJumlah]++;
                total[iJumlah]++;
            });

            const urut = urutanJenjang.filter((j) => perJenjang[j])
                .concat(Object.keys(perJenjang).filter((j) => !urutanJenjang.includes(j)));
            const sel = (n, tag = 'td') => '<' + tag + ' class="px-4 py-2 text-center whitespace-nowrap ' +
                (n ? 'text-gray-900' : 'text-gray-300') + '">' + n + '</' + tag + '>';

            $('#peta-body').html(urut.length ? urut.map((j, k) =>
                '<tr class="border-b border-gray-200 ' + (k % 2 ? 'bg-gray-50' : 'bg-white') + '">' +
                '<td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap">' + escapeHtml(j) + '</td>' +
                perJenjang[j].map((n) => sel(n)).join('') + '</tr>'
            ).join('') : '<tr><td colspan="' + (kolomPeta.length + 3) + '" class="px-4 py-6 text-center text-gray-500">Tidak ada program studi yang sesuai filter.</td></tr>');

            $('#peta-foot').html(urut.length ? '<tr class="font-semibold"><th class="px-6 py-3 text-center text-gray-900">TOTAL</th>' +
                total.map((n) => sel(n, 'th')).join('') + '</tr>' : '');

            // keterangan filter yang sedang dipakai
            const filter = [
                ['Prodi', $('#cari-prodi').val()],
                ['Fakultas', $('#cari-fakultas').val()],
                ['Akreditasi', $('#filter-akreditasi').val()],
                ['Lembaga', $('#filter-lembaga').val()],
            ].filter(([, v]) => v).map(([k, v]) => k + ': ' + v);
            $('#info-peta').text(filter.length
                ? 'Sebaran ' + baris.length + ' program studi sesuai filter (' + filter.join(', ') + ').'
                : 'Sebaran seluruh ' + baris.length + ' program studi aktif.');
        };

        const table = new DataTable('#data', {
            // Data dimuat via Ajax (JSON) — lihat AkreditasiController@dataNasional
            ajax: '{{ url('/data/akreditasi-nasional') }}',
            deferRender: true,
            processing: true,
            columns: [
                { data: null, orderable: false, searchable: false, defaultContent: '' },
                { data: 'nama_jenjang', defaultContent: '-' },
                { data: 'nama_prodi', defaultContent: '-' },
                { data: 'nm_fakultas', defaultContent: '-' },
                { data: 'akreditasi', defaultContent: '-' },
                { data: 'no_sk_akreditasi', defaultContent: '-', render: (v, type) => type === 'display' ? (v ? escapeHtml(v) : '-') : (v || '') },
                { data: 'lembaga', defaultContent: '-' },
                { data: 'start_date', type: 'date', defaultContent: '-' },
                { data: 'end_date', type: 'date', defaultContent: '-' },
                {
                    data: 'riwayat',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: (riwayat, type, row) => {
                        if (type !== 'display') return '';
                        // prodi tanpa SK sama sekali: cukup "-"
                        if (!row.url && !(riwayat || []).length) return '-';
                        const btn = 'bg-blue-500 hover:bg-blue-700 text-white font-sans py-1 px-1 text-xs rounded';
                        let daftar = row.url
                            ? '<a class="' + btn + '" href="' + escapeHtml(row.url) + '" target="_blank"><span>File Terbaru</span></a>'
                            : '<span>-- File Belum Tersedia --</span>';
                        daftar += '<br>';
                        (riwayat || []).forEach((d) => {
                            daftar += '<br><a class="' + btn + '" href="' + escapeHtml(d.url) + '" target="_blank"><span>' +
                                escapeHtml(d.tgl_awal) + ' - ' + escapeHtml(d.tgl_akhir) + '</span></a><br>';
                        });
                        return '<button type="button" class="btn-lihat-sk bg-green-600 hover:bg-green-700 text-white font-sans py-1 px-2 text-xs rounded">Lihat SK</button>' +
                            '<div class="daftar-sk hidden mt-2">' + daftar + '</div>';
                    }
                }
            ],
            // Kolom yang paling akhir disembunyikan saat layar sempit (angka kecil = prioritas tinggi)
            columnDefs: [
                { targets: [0, 2], responsivePriority: 1 },
                { targets: 9, responsivePriority: 2 },
                { targets: 4, responsivePriority: 3 },
            ],
            responsive: true,
            createdRow: (row) => {
                row.className = 'px-6 py-4 border-b dark:border-gray-300 text-xs font-medium text-gray-900 whitespace-wrap dark:text-black';
                $('td', row).addClass('px-6 py-4');
            },
            // Urutan awal; klik header untuk sort (asc → desc → kembali ke awal),
            // Shift+klik untuk sort multi-kolom. Sorting DataTables bersifat stable.
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
                isiOpsi(4, $('#filter-akreditasi'));
                isiOpsi(6, $('#filter-lembaga'));
            },
            on: {
                draw: (e) => {
                    let start = e.dt.page.info().start;
                    let info = e.dt.page.info();
                    $('#info-filter').text('Menampilkan ' + info.recordsDisplay + ' dari ' + info.recordsTotal + ' program studi');
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

        // Tombol "Lihat SK": tampilkan/sembunyikan daftar file SK (juga berlaku di child row responsive)
        $('#data').on('click', '.btn-lihat-sk', function() {
            const daftar = this.nextElementSibling;
            const tampil = daftar.classList.toggle('hidden') === false;
            this.textContent = tampil ? 'Tutup SK' : 'Lihat SK';
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
        $('#filter-akreditasi').on('change', function() { cariKolom(4, this.value, true); });
        $('#filter-lembaga').on('change', function() { cariKolom(6, this.value, true); });
        $('#reset-filter').on('click', function() {
            $('#panel-filter').find('input, select').val('');
            table.columns().search('').draw();
        });
    </script>
</x-layout>
