<x-backend-layout>

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
                            <th scope="col" class="px-6 py-3">Akreditasi Nasional</th>
                            <th scope="col" class="px-6 py-3">Lembaga Akreditasi Nasional</th>
                            <th scope="col" class="px-6 py-3">Akreditasi Internasional</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($akreditasi as $akreditasi)
                            <tr
                                class="text-xs font-medium odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td></td>
                                <td scope="row" class="px-3 py-2 text-gray-900 whitespace-nowrap">
                                    {{ $akreditasi->nama_prodi }}</td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $akreditasi->nama_jenjang }}
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $akreditasi->nm_fakultas }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $akreditasi->akreditasi }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $akreditasi->lembaga }}
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $akreditasi->internasional }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="text-s text-center bg-gray-300 dark:bg-gray-300 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-6 py-3"></th>
                            <th scope="col" class="px-6 py-3"></th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Jenjang</th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Fakultas</th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Akreditasi Nasional
                            </th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Lembaga Akreditasi
                                Nasional</th>
                            <th scope="col" class="px-6 py-3 text-gray-900 whitespace-nowrap">Akreditasi Interasional
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </article>


    {{-- <script type="text/javascript">
        $(document).ready(function($) {
            $('#export-table').DataTable({
                "iDisplayLength": 10,
                initComplete: function() {

                    this.api().columns([2, 3, 4, 5, 6]).every(function() {
                        var column = this;
                        var select = $(
                                '<select style="width: 100px" class="text-s text-left bg-gray-300"><option value="">Show All</option></select>'
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
                },

            });
        });

    </script> --}}

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
