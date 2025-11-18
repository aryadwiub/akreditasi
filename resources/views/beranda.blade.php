<x-layout>
    <x-slot:judul>{{ $judul }}</x-slot>

    <div class="card relative overflow-x-auto shadow-md sm:rounded-lg px-20 py-6 place-items-center">
        <table class=
        "display" 
        id="data">
            {{-- <tfoot class="text-s text-center text-gray-700 uppercase bg-gray-300 dark:bg-gray-300 dark:text-gray-600">
                <tr>
                    <th scope="col" class="px-6 py-3"></th> 
                    <th scope="col" class="px-6 py-3">Jenjang</th>
                    <th scope="col" class="px-6 py-3"></th>
                    <th scope="col" class="px-6 py-3">Fakultas</th>
                    <th scope="col" class="px-6 py-3">Akreditasi Nasional</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-6 py-3"></th>
                    <th scope="col" class="px-6 py-3">Akreditasi Interasional</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-10 py-3"></th>
                </tr>
            </tfoot> --}}
            <thead class="text-xs text-center text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-3">NO</th> 
                    <th scope="col" class="px-6 py-3">Jenjang</th>
                    <th scope="col" class="px-6 py-3">Nama Program Studi</th>
                    <th scope="col" class="px-6 py-3">Fakultas</th>
                    <th scope="col" class="px-6 py-3">Akreditasi Nasional</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-6 py-3">Download SK Nasional</th>
                    <th scope="col" class="px-6 py-3">Akreditasi Interasional</th>
                    <th scope="col" class="px-6 py-3">Tanggal Kadaluwarsa</th>
                    <th scope="col" class="px-6 py-3">Download SK Internasional</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($data as $data)
                    <tr
                        class="px-6 py-4 border-b dark:border-gray-300 text-xs font-medium  text-gray-900 whitespace-wrap dark:text-black">
                        <td class="px-6 py-4"></td>
                        <td class="px-6 py-4">{{ $data->nama_jenjang }}</td>
                        <td class="px-6 py-4"><a href="">{{ $data->nama_prodi }}</a></td>
                        <td class="px-6 py-4">{{ $data->nm_fakultas }}</td>
                        <td class="px-6 py-4">{{ $data->akreditasi }}</td>
                        <td class="px-6 py-4">{{ $data->end_date }}</td>
                        <td class="px-6 py-4 text-center">
                            @if ($data->url != '')
                                <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans py-1 px-1 text-xs rounded" href="{{ url('unduh/'.$data->id_prodi) }}">
                                <span>File Terbaru</span>
                                </a>
                                @else
                                <span>-- File Belum Tersedia --</span>
                            @endif
                            <br>
                            @php
                                $dokumen = 
                                // DB::select("select id, date_format(tgl_awal,'%Y') as tgl_awal, date_format(tgl_akhir,'%Y') as tgl_akhir 
                                //     from riwayat_akreditasi_dokumen as Z where Z.id_prodi = '$data->id_prodi'
                                //     order by id desc");
                                    DB::table('riwayat_akreditasi_dokumen as Z')
                                        ->select('id', DB::raw('date_format(tgl_awal,"%Y") as tgl_awal'), DB::raw('date_format(tgl_akhir,"%Y") as tgl_akhir'))
                                        ->where('Z.id_prodi', '=', $data->id_prodi)
                                        ->get();
                            //   dd($dokumen);
                            @endphp
                            @foreach ($dokumen as $dokumen)
                            <br>
                                <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans py-1 px-1 text-xs rounded" href="{{ url('unduhhis/'.$data->id_prodi) }}">
                                <span>{{ $dokumen->tgl_awal }} - {{ $dokumen->tgl_akhir }}</span>
                                </a>
                            <br>
                            @endforeach
                            {{-- <span>tes doku</span> --}}
                        </td>
                        <td class="px-6 py-4">{{ $data->internasional }}

                        </td>
                        <td class="px-6 py-4">
                            @if ($data->tgl_akhir == '1')
                                {{ $data->tgl_berakhir }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($data->urlinter != '')
                            <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans whitespace-wrap py-1 px-2 text-xs rounded"
                                href="{{ url('unduh/' . $data->id_riwayat) }}">Download</a>
                                @else
                                -- File Belum Tersedia --
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <h2 class="text-4xl font-semibold tracking-tight text-pretty text-gray-900 sm:text-5xl">Peta Sebaran
                    Akreditasi</h2>
            </div>
            <div
                class="mx-auto mt-10 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-16 border-t border-gray-200 pt-10 sm:mt-16 sm:pt-16 lg:mx-0 lg:max-w-none lg:grid-cols-1">
                <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
                    <div class="flex items-center gap-x-4 text-xs">
                        <time datetime="2020-03-16" class="text-gray-500">Mar 16, 2020</time>
                        <a href="#"
                            class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100">Marketing</a>
                    </div>
                    <div class="group relative grow">
                        <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                            <span class="absolute inset-0"></span>
                            Akreditasi Nasional
                        </h3>
                        <br>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Jenjang</th>
                                        <th scope="col" class="px-6 py-3">Terakreditasi Unggul</th>
                                        <th scope="col" class="px-6 py-3">Unggul</th>
                                        <th scope="col" class="px-6 py-3">A</th>
                                        <th scope="col" class="px-6 py-3">Baik Sekali</th>
                                        <th scope="col" class="px-6 py-3">Baik</th>
                                        <th scope="col" class="px-6 py-3">B</th>
                                        <th scope="col" class="px-6 py-3">C</th>
                                        <th scope="col" class="px-6 py-3">Minimum</th>
                                        <th scope="col" class="px-6 py-3">Sementara</th>
                                        <th scope="col" class="px-6 py-3">Re Akreditasi</th>
                                        <th scope="col" class="px-6 py-3">Proses Akreditasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($nasional as $nasional)
                                        <tr
                                            class="odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                            <td scope="row"
                                                class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->nama_jenjang }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Terakre_unggul }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Unggul }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->A }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Baik_sekali }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Baik }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->B }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->C }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Minimum }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Sementara }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Reakreditasi }}</td>
                                            <td
                                                class="px-4 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $nasional->Proses }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @foreach ($total_nas as $total_nas)
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                TOTAL</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Terakre_unggul }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Unggul }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->A }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Baik_sekali }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Baik }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->B }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->C }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Minimum }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Sementara }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Reakreditasi }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_nas->Proses }}</th>
                                        </tr>
                                    @endforeach
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </article>
                <br>
                <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
                    <div class="flex items-center gap-x-4 text-xs">
                        <time datetime="2020-03-10" class="text-gray-500">Mar 10, 2020</time>
                        <a href="#"
                            class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100">Sales</a>
                    </div>
                    <div class="group relative grow">
                        <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                            <span class="absolute inset-0"></span>
                            Akreditasi Internasional
                        </h3>
                        <br>
                        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                <thead
                                    class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-center">Jenjang</th>
                                        <th scope="col" class="px-6 py-3 text-center">ASIIN</th>
                                        <th scope="col" class="px-6 py-3 text-center">FIBAA</th>
                                        <th scope="col" class="px-6 py-3 text-center">RSC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($internasional as $internasional)
                                        <tr
                                            class="odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                            <td scope="row"
                                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $internasional->nama_jenjang }}</td>
                                            <td
                                                class="px-6 py-4 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $internasional->ASIIN }}</td>
                                            <td
                                                class="px-6 py-4 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $internasional->FIBAA }}</td>
                                            <td
                                                class="px-6 py-4 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $internasional->RSC }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    @foreach ($total_inter as $total_inter)
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                TOTAL</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_inter->ASIIN }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_inter->FIBAA }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                                {{ $total_inter->RSC }}</th>
                                        </tr>
                                    @endforeach
                                </tfoot>
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

        <script type="text/javascript">
        const table = new DataTable('#data', {
            columnDefs: [{
                searchable: true,
                orderable: true,
                targets: 0,
            }],
            order: [
                [1, 'asc']
            ],
            on: {
                draw: (e) => {
                    let start = e.dt.page.info().start;

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
    </script>
</x-layout>
