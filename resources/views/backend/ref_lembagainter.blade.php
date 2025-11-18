<x-backend-layout>

    <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3  place-items-center">
        <div class="group relative grow">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg px-3 py-12 bg-gray-300 place-items-center">
                <button data-modal-target="crud-modal-peringkat" data-modal-toggle="crud-modal-peringkat"
                    class=" block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Tambah Data Lembaga Internasional
                </button>
                <br>
                <table class="w-relative text-sm text-left rtl:text-right text-gray-200 dark:text-gray-200 ">
                    <thead class=" text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th scope="col" class="px-1 py-1">No.</th>
                            <th scope="col" class="px-6 py-3">Nama Lembaga Internasional</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($lembaga as $lembaga)
                            <tr
                                class="text-xs font-medium odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td scope="row" class="px-1 py-1 text-center text-gray-900 whitespace-nowrap">{{ $no++ }}.</td>
                                <td scope="row" class="px-3 py-2 text-gray-900 whitespace-nowrap">
                                    {{ $lembaga->nama_lembaga }}</td>
                                <td class="px-1 py-1 text-gray-900">
                                    <a class="bg-blue-500 hover:bg-blue-700 text-white font-sans py-1 px-1 text-xs rounded" type="button"
                                    href="{{ url('ubahlembagainter/' . $lembaga->id_akre) }}">
                                    Edit
                                </a>
                                </td>
                                <td class="px-1 py-1 text-gray-900">
                                <form action="{{ url('hapuslembagainter/' . $lembaga->id_akre) }}" method="post">
                                    @method('delete')
                                    @csrf
                                    <button class="bg-red-500 hover:bg-red-700 text-white font-sans py-1 px-1 text-xs rounded"
                                     onclick="return confirm('Yakin dihapus ta om? (iki datae lembaga internasional)')">
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

        <div id="crud-modal-peringkat" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Tambah Data Lembaga Internasional
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal-peringkat">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" method="post" action="{{ url('/simpanlembagainter') }}"
                    enctype="multipart/form-data">
                    @csrf
                    {{-- <input type="text" name="prodi" id="prodi" value="{{ $fakultas->id_fakultas }}" hidden> --}}
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="nama"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lembaga Internasional</label>
                            <input type="text" name="nama" id="nama"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                    </div>
                    <button type="submit" onclick="return confirm('wes tit data fakultase?')"
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