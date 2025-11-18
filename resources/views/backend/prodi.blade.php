<x-backend-layout>

    <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg px-3 py-12  place-items-center">
                <table class="w-fixed text-sm text-left rtl:text-right text-gray-200 dark:text-gray-200 ">
                    <thead class=" text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">Nama Program Studi</th>
                            <th scope="col" class="px-6 py-3">Fakultas</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prodi as $prodi)
                            <tr
                                class="text-xs font-medium odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                
                                <td scope="row" class="px-3 py-2 text-gray-900 whitespace-nowrap">
                                    {{ $prodi->nama_jenjang }} {{ $prodi->nama_prodi }}</td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $prodi->nm_fakultas }}
                                </td>
                                <td class="text-center px-3 py-2 text-gray-900">
                                    @if ($prodi->status = 1)
                                        <span>Aktif</span>
                                    @else
                                        <span>Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans py-1 px-2 text-xs rounded"
                                        href="{{ url('prodidetail/' . $prodi->id_prodi) }}">
                                        <span>Lihat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </article>

</x-backend-layout>
