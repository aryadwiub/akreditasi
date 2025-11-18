<x-backend-layout>

    <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">

        <div class="group relative grow">


            <br>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-200 dark:text-gray-200">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th scope="col" class="px-6 py-3">Fakultas</th>
                            <th scope="col" class="px-6 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fakultas as $fakultas)
                            <tr
                                class="odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td scope="row"
                                    class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $fakultas->nm_fakultas }}</td>
                                <td>
                                    <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans py-2 px-2 text-xs rounded"
                                        href="{{ url('prodi/' . $fakultas->id_fakultas) }}">
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
