<x-backend-layout>

    <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg px-3 py-12  place-items-center">
                <button data-modal-target="crud-modal-prodi" data-modal-toggle="crud-modal-prodi"
                    class=" block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Tambah Data Prodi
                </button>
                <br>
                <table class="w-fixed text-sm text-left rtl:text-right text-gray-200 dark:text-gray-200 ">
                    <thead class=" text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th scope="col" class="px-1 py-1">No.</th>
                            <th scope="col" class="px-6 py-3">Jenjang</th>
                            <th scope="col" class="px-6 py-3">Program Studi</th>
                            <th scope="col" class="px-6 py-3">Fakultas</th>
                            <th scope="col" class="px-6 py-3">ID Cyber</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($prodi as $prodi)
                            <tr
                                class="text-xs font-medium odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td scope="row" class="px-1 py-1 text-center text-gray-900 whitespace-nowrap">{{ $no++ }}.</td>
                                <td scope="row" class="px-3 py-2 text-gray-900 whitespace-nowrap">
                                    {{ $prodi->nama_jenjang }}</td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $prodi->nama_prodi }}
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $prodi->nm_fakultas }}
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                    {{ $prodi->id_cyber }}
                                </td>
                                <td class="px-3 py-2 text-gray-900">
                                   @if ($prodi->status == 1)
                                       Aktif
                                       @else
                                       Tutup
                                   @endif
                                </td>
                                <td class="px-1 py-1 text-gray-900">
                                    <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans py-1 px-1 text-xs rounded" type="button"
                                    href="{{ url('ubahprodi/' . $prodi->id_prodi) }}">
                                    Edit
                                </a>
                                </td>
                                <td class="px-1 py-1 text-gray-900">
                                <form action="{{ url('hapusprodi/' . $prodi->id_prodi) }}" method="post">
                                    @method('delete')
                                    @csrf
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-sans py-1 px-1 text-xs rounded"
                                     onclick="return confirm('Yakin dihapus ta om? (iki datae prodi)')">
                                       <span>hapus</span>
                                    </button>
                                 </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </article>

        <div id="crud-modal-prodi" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Tambah Data Prodi
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal-prodi">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" method="post" action="{{ url('/simpanprodi') }}"
                    enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="text" name="prodi" id="prodi" value="{{ $fakultas->id_fakultas }}" hidden> --}}
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="fakultas"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fakultas</label>
                            <select id="fakultas" name="fakultas"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected="">------</option>
                                @foreach ($fakultas as $fakultas)
                                    <option value="{{ $fakultas->id_fakultas }}">
                                        {{ $fakultas->nm_fakultas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="jenjang"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenjang</label>
                            <select id="jenjang" name="jenjang"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected="">------</option>
                                @foreach ($jenjang as $jenjang)
                                    <option value="{{ $jenjang->id_jenjang }}">
                                        {{ $jenjang->nama_jenjang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="prodi"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Prodi</label>
                            <input type="text" name="prodi" id="prodi"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="id_cyber"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID Cyber</label>
                            <input type="number" name="id_cyber" id="id_cyber"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="">
                        </div>
                        <div class="col-span-2">
                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <select id="status" name="status"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected>------</option>
                                <option value="1">Aktif</option>
                                <option value="2">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" onclick="return confirm('wes tit data prodine?')"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-backend-layout>