<x-backend-layout>

    <div class=" max-h-full place-items-center">
        <div class="relative p-4  max-w-md max-h-full ">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700 ">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $judul }}
                    </h3>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" method="post" action="{{ url('ubahsimpaninter/' . $ambil->id_riwayat) }}" enctype="multipart/form-data">
                    @method('put')
                    @csrf
                    <input type="text" name="prodi" id="prodi" value="{{ $ambil->id_prodi }}" hidden>
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="akreditasi"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lembaga</label>
                            <select id="lembaga" name="lembaga"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                    <option selected="{{ $ambil->lembaga }}">{{ $ambil->nama_lembaga }}</option>
                                @foreach ($lembaga as $lembaga)
                                    <option value="{{ $lembaga->id_akre }}">{{ $lembaga->nama_lembaga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berlaku"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berlaku</label>
                            <input type="date" name="tgl_berlaku" id="tgl_berlaku"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="" value="{{ $ambil->tgl_berlaku }}">
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berakhir"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berakhir</label>
                            <input type="date" name="tgl_berakhir" id="tgl_berakhir"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="" value="{{ $ambil->tgl_berakhir }}">
                        </div>

                        {{-- <div class="col-span-2">
                            <label for="file"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">File</label>
                            <input type="file" name="file" id="file" aria-describedby="file"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500 focus:outline-none"
                                required="">
                        </div> --}}
                        <div class="col-span-2">
                            <label for="file"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link File</label>
                            <input type="text" name="file" id="file"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="" value="{{ $ambil->url }}">
                        </div>
                    </div>
                    <button type="submit"
                        class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ url('prodidetail/' . $ambil->id_prodi) }}" 
                    class="text-white inline-flex items-center bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                        <svg class="me-1 -ms-1 w-5 h-5"
                        xmlns="http://www.w3.org/2000/svg" 
                            width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10 5a2 2 0 0 0-1.344.519l-6.328 5.74a1 1 0 0 0 0 1.481l6.328 5.741A2 2 0 0 0 10 19h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"></path>
                            <path d="m12 9 6 6"></path>
                            <path d="m18 9-6 6"></path>
                        </svg>
                        Gak sido di edit</a>
                </form>
            </div>
        </div>
    </div>

</x-backend-layout>