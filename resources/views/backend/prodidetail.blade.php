<x-backend-layout>
    {{-- @php
    dd($riwayat_nasional);
@endphp --}}
    <article class="card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <br>
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="table-auto text-sm text-left">
                    <thead class=" text-xs text-gray-700 dark:text-gray-800">
                        <tr>
                            <th scope="col" class="px-3 py-1">Nama Program Studi</th>
                            <th class="px-3 py-1">:</th>
                            <th>{{ $detail->nama_prodi }}</th>
                        </tr>
                        <tr>
                            <th scope="col" class="px-3 py-1">Fakultas</th>
                            <th class="px-3 py-1">:</th>
                            <th>{{ $detail->nm_fakultas }}</th>
                        </tr>
                        <tr>
                            <th scope="col" class="px-3 py-1">Jenjang</th>
                            <th class="px-3 py-1">:</th>
                            <th>{{ $detail->nama_jenjang }}</th>
                        </tr>
                        <tr class="px-3 py-1">
                            <th scope="col" class="px-3 py-1">Terakreditasi Nasional</th>
                            <th class="px-3 py-1">:</th>
                            <th>{{ $detail->akreditasi }} </th>
                        </tr>
                        <tr class="px-6 py-4">
                            <th scope="col" class="px-3 py-1">Berlaku Sampai Tanggal</th>
                            <th class="px-3 py-1">:</th>
                            <th>{{ $detail->end_date }} </th>
                        </tr>
                        <tr>
                            <th scope="col" class="px-3 py-1">Lembaga Akreditasi Nasional</th>
                            <th class="px-3 py-1">:</th>
                            <th>{{ $detail->lembaga }}</th>
                        </tr>
                        @if ($detail->internasional != '')
                            <tr>
                                <th scope="col" class="px-3 py-1">Terakreditasi Internasional</th>
                                <th class="px-3 py-1">:</th>
                                <th>{{ $detail->internasional }}</th>
                            </tr>
                            <tr>
                                <th scope="col" class="px-3 py-1">Berlaku Sampai Tanggl</th>
                                <th class="px-3 py-1">:</th>
                                <th>{{ $detail->tgl_berakhir }}</th>
                            </tr>
                        @else
                            <tr>
                                <th scope="col" class="px-3 py-1">Terakreditasi Internasional</th>
                                <th class="px-3 py-1">:</th>
                                <th> Belum Terakreditasi Internasional </th>
                            </tr>
                        @endif
                    </thead>
                </table>
            </div>
        </div>
    </article>

    <article class="w-full card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <h3 class="mt-1 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                <span class="absolute inset-0"></span>
                History Akreditasi Nasional
            </h3>
            <hr>
            <div class="w-full relative overflow-x-auto shadow-md sm:rounded-lg">
                <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                    class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Tambah Data
                </button>
                <hr>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            <th scope="col" class="text-center px-6 py-3">Akreditasi</th>
                            <th class="text-center px-6 py-3">Lembaga</th>
                            <th class="text-center px-6 py-3">Nomor SK</th>
                            <th class="text-center px-6 py-3">Tanggal Berlaku</th>
                            <th class="text-center px-6 py-3">Tanggal Berakhir</th>
                            <th class="text-center px-6 py-3">File</th>
                            <th class="text-center px-6 py-3">Status SK</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat_nasional as $riwayat_nasional)
                            <tr
                                class="whitespace-wrap odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                <td scope="row"
                                    class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $riwayat_nasional->akreditasi }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $riwayat_nasional->lembaga }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $riwayat_nasional->no_sk_akreditasi }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $riwayat_nasional->start_date }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $riwayat_nasional->end_date }}</td>
                                <td class="w-1/2 text-center text-gray-900 whitespace-wrap dark:text-black">
                                    {{ $riwayat_nasional->url }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    @if ($riwayat_nasional->end_date > now())
                                        Aktif
                                    @else
                                        Tidak Aktif
                                    @endif
                                </td>
                                <td class="px-1 text-center whitespace-nowrap ">
                                    <a class="" type="submit"
                                        href="{{ url('ubah/' . $riwayat_nasional->id_riwayat_akreditasi) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0006ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>                                     
                                    </a>
                                </td>   
                                <td class="px-1 text-center whitespace-nowrap ">    
                                <form action="{{ url('hapus/' . $riwayat_nasional->id_riwayat_akreditasi) }}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <input type="text" name="prodi" id="prodi" value="{{ $riwayat_nasional->id_prodi }}" hidden>
                                    <button class="" onclick="return confirm('Yakin dihapus ta om? (iki datae akreditasi nasional)')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 5a2 2 0 0 0-1.344.519l-6.328 5.74a1 1 0 0 0 0 1.481l6.328 5.741A2 2 0 0 0 10 19h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"></path><path d="m12 9 6 6"></path><path d="m18 9-6 6"></path></svg>
                                   </button>
                                 </form>

                                {{-- <button class="" onclick="event.preventDefault();document.getElementById('delete').submit();">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 5a2 2 0 0 0-1.344.519l-6.328 5.74a1 1 0 0 0 0 1.481l6.328 5.741A2 2 0 0 0 10 19h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"></path><path d="m12 9 6 6"></path><path d="m18 9-6 6"></path></svg>
                                </button>
                                <form id="delete" action="{{ url('hapus/' . $riwayat_nasional->id_riwayat_akreditasi) }}" method="post">
                                </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </article>
<br>
    <article class="w-full card relative overflow-x-auto shadow-md sm:rounded-lg px-6 py-3">
        <div class="group relative grow">
            <h3 class="mt-1 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                <span class="absolute inset-0"></span>
                History Akreditasi Internasional
            </h3>
            <hr>
            <div class="w-full relative overflow-x-auto shadow-md sm:rounded-lg">
                <button data-modal-target="crud-modal-internasional" data-modal-toggle="crud-modal-internasional"
                    class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    Tambah Data
                </button>
                <hr>
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                        <tr>
                            {{-- <th scope="col" class="text-center px-6 py-3">Akreditasi</th> --}}
                            <th class="text-center px-6 py-3">Lembaga</th>
                            {{-- <th class="text-center px-6 py-3">Nomor SK</th> --}}
                            <th class="text-center px-6 py-3">Tanggal Berlaku</th>
                            <th class="text-center px-6 py-3">Tanggal Berakhir</th>
                            <th class="text-center px-6 py-3">File</th>
                            <th class="text-center px-6 py-3">Status SK</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($riwayat_internasional as $detail)
                            <tr
                                class="whitespace-wrap odd:bg-white odd:dark:bg-gray-200 even:bg-gray-50 even:dark:bg-gray-200 border-b dark:border-gray-400 border-gray-200">
                                {{-- <td scope="row"
                                    class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $detail->internasional }}</td> --}}
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $detail->nama_lembaga }}</td>
                                {{-- <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $riwayat_nasional->no_sk_akreditasi }}</td> --}}
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $detail->tgl_berlaku }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    {{ $detail->tgl_berakhir }}</td>
                                <td class="w-1/2 text-center text-gray-900 whitespace-wrap dark:text-black">
                                    {{ $detail->url }}</td>
                                <td class="px-3 py-2 text-center text-gray-900 whitespace-nowrap dark:text-black">
                                    @if ($detail->tgl_berakhir > now())
                                        Aktif
                                    @else
                                        Tidak Aktif
                                    @endif
                                </td>
                                <td class=" text-center whitespace-nowrap ">
                                    <a class="" type="button"
                                        href="{{ url('ubahinter/' . $detail->id_riwayat) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0006ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg>                                     
                                    </a>
                                </td>

                                <td class=" text-center whitespace-nowrap">                                       
                                <form action="{{ url('hapusinter/' . $detail->id_riwayat) }}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <input type="text" name="prodi" id="prodi" value="{{ $detail->id_prodi }}" hidden>
                                    <button class="" onclick="return confirm('Yakin dihapus ta om? (iki datae akreditasi internasional)')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ff0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 5a2 2 0 0 0-1.344.519l-6.328 5.74a1 1 0 0 0 0 1.481l6.328 5.741A2 2 0 0 0 10 19h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"></path><path d="m12 9 6 6"></path><path d="m18 9-6 6"></path></svg>
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

    {{-- //modal tambah nasional --}}
    <div id="crud-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Tambah Data Akreditasi Nasional
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" method="post" action="{{ url('/simpandata') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="prodi" id="prodi" value="{{ $detail->id_prodi }}" hidden>
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="akreditasi"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Akreditasi</label>
                            <select id="akreditasi" name="akreditasi"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected="">------</option>
                                @foreach ($peringkat as $peringkat)
                                    <option value="{{ $peringkat->kode }}">{{ $peringkat->akreditasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="lembaga"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lembaga</label>
                            <select id="lembaga" name="lembaga"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected="">------</option>
                                @foreach ($akreditasi as $akreditasi)
                                    <option value="{{ $akreditasi->nm_akreditasi }}">
                                        {{ $akreditasi->nm_akreditasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="no_sk"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor SK</label>
                            <input type="text" name="no_sk" id="no_sk"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berlaku"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berlaku</label>
                            <input type="date" name="tgl_berlaku" id="tgl_berlaku"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berakhir"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berakhir</label>
                            <input type="date" name="tgl_berakhir" id="tgl_berakhir"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
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
                                placeholder="" required="">
                        </div>

                        <div class="col-span-2">
                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <select id="status" name="status"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected>------</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" onclick="return confirm('wes tit data nasionale?')"
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

    {{-- //modal tambah internasional --}}
<div id="crud-modal-internasional" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Tambah Data Akreditasi Internasional
                    </h3>
                    <button type="button"
                        class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-toggle="crud-modal-internasional">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Tutup</span>
                    </button>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" method="post" action="{{ url('/simpandatainter') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="text" name="prodi" id="prodi" value="{{ $detail->id_prodi }}" hidden>
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="lembaga"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lembaga</label>
                            <select id="lembaga" name="lembaga"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option selected="">------</option>
                                @foreach ($lembaga_internasional as $lembaga)
                                    <option value="{{ $lembaga->id_akre }}">
                                        {{ $lembaga->nama_lembaga }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berlaku"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berlaku</label>
                            <input type="date" name="tgl_berlaku" id="tgl_berlaku"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berakhir"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berakhir</label>
                            <input type="date" name="tgl_berakhir" id="tgl_berakhir"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                        <div class="col-span-2">
                            <label for="file"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link File</label>
                            <input type="text" name="file" id="file"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="">
                        </div>
                    </div>
                    <button type="submit" onclick="return confirm('wes tit data internasionale?')"
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
