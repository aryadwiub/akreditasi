<x-backend-layout>

    <form class="max-w-sm mx-auto" method="post" action="{{ url('/kadal_internasional') }}">
        @csrf
        <div class="mb-5">
            <label for="tgl" class="block mb-2 text-sm font-medium text-gray-900"> Cari Tanggal Berakhir
                Kareditasi</label>
            <input type="date" id="tgl" name="tgl"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                required value="{{ $tanggal }}"/>
        </div>
        <button type="submit"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cari</button>
    </form>
{{-- <x-slot:judul>{{ $judul }}</x-slot> --}}
    <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg px-3 py-12  place-items-center">
                <table class="display" id="export-table">
                    <thead class=" text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th>No.</th>
                            <th scope="col" class="px-6 py-3">Nama Program Studi</th>
                            <th scope="col" class="px-6 py-3">Jenjang</th>
                            <th scope="col" class="px-6 py-3">Fakultas</th>
                            <th scope="col" class="px-6 py-3">Akreditasi Internasional</th>
                            <th scope="col" class="px-6 py-3">Tgl Berakhir</th>
                            {{-- <th scope="col" class="px-6 py-3">Akreditasi Internasional</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kadal as $kadal)
                            <tr
                                class="text-xs font-medium odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td></td>
                                <td scope="row" class="px-3 py-2 text-gray-900 whitespace-nowrap">
                                    {{ $kadal->nama_prodi }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $kadal->nama_jenjang }}
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $kadal->nm_fakultas }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $kadal->nama_lembaga }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $kadal->tgl_berakhir }}
                                </td>
                                {{-- @if ($kadal->nama_lembaga != 'null')
                                    <td class="text-center px-3 py-2 text-gray-900">
                                        {{ $kadal->internasional }}
                                    </td>
                                @else
                                    <td class="text-center px-3 py-2 text-gray-900">
                                        ----
                                    </td>
                                @endif --}}
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-xs text-center bg-gray-300 dark:bg-gray-300 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-6 py-3"></th>
                            <th scope="col" class="px-6 py-3"></th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Jenjang</th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Fakultas</th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Akreditasi Internasional
                            </th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Tgl Berakhir</th>
                            {{-- <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Akreditasi
                                Internasional</th> --}}
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </article>

    <script type="text/javascript">
        const table = new DataTable('#export-table', {
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
            },
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        let column = this;

                        // Create select element
                        let select = document.createElement('select');
                        select.add(new Option(''));
                        column.footer().replaceChildren(select);

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
            },

        });
    </script>
</x-backend-layout>
