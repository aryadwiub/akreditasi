<x-layout>
    <x-slot:judul>{{ $judul }}</x-slot>
    {{-- <h3>ini halaman akreditasi universitas</h3> --}}

     <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg px-3 py-12  place-items-center">
                <table class="display" id="export-table">
                    <thead class=" text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th>No.</th>
                            <th scope="col" class="px-6 py-3">Tanggal Berlaku</th>
                            <th scope="col" class="px-6 py-3">Tanggal berakhir</th>
                            <th scope="col" class="px-6 py-3">Akreditasi</th>
                            <th scope="col" class="px-6 py-3">Download SK</th>
                            <th scope="col" class="px-6 py-3">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($univ as $key => $univ)
                            <tr
                                class="text-xs font-medium odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td class="text-center px-3 py-2 text-gray-900">{{ ++ $key }}</td>
                                <td scope="row" class="text-center px-3 py-2 text-gray-900 whitespace-nowrap">
                                    {{ $univ->tgl_awal }}</td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $univ->tgl_berakhir }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $univ->akreditasi }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    <a href="" class="bg-blue-500 hover:bg-blue-700 text-white font-sans whitespace-wrap py-1 px-2 text-xs rounded">Download</a>
                                    {{-- {{ $univ->url }} --}}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    {{ $univ->keterangan }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </article>
</x-layout>