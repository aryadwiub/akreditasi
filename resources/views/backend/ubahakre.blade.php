<x-backend-layout>

    <div class=" max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-700">
                <!-- Modal header -->
                <div
                    class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $judul }}
                    </h3>
                </div>
                <!-- Modal body -->
                <form class="p-4 md:p-5" method="post" action="{{ url('ubahsimpan/' . $ambil->id_riwayat_akreditasi) }}" enctype="multipart/form-data">
                    @method('put')
                    @csrf
                    <input type="text" name="prodi" id="prodi" value="{{ $ambil->id_prodi }}" hidden>
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="akreditasi"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Akreditasi</label>
                            <select id="akreditasi" name="akreditasi"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                @if ($ambil->akreditasi == '')
                                    <option value="" selected disabled>------</option>
                                @elseif (!$ambil->akre)
                                    {{-- kode tidak ada di tabel peringkat: tetap pertahankan nilai lama --}}
                                    <option value="{{ $ambil->akreditasi }}" selected>{{ $ambil->akreditasi }}</option>
                                @endif
                                @foreach ($peringkat as $item)
                                    <option value="{{ $item->kode }}" @selected($item->kode === $ambil->akreditasi)>{{ $item->akreditasi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="lembaga" 
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Lembaga</label>
                            <select id="lembaga" name="lembaga"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                @if ($ambil->lembaga == '')
                                    <option value="" selected disabled>------</option>
                                @else
                                    {{-- nilai lama dipertahankan apa adanya walau tidak ada di tabel jenis_akreditasi --}}
                                    <option value="{{ $ambil->lembaga }}" selected>{{ $ambil->lembaga }}</option>
                                @endif
                                @foreach ($lembaga as $item)
                                    @if (strcasecmp($item->nm_akreditasi, (string) $ambil->lembaga) !== 0)
                                        <option value="{{ $item->nm_akreditasi }}">{{ $item->nm_akreditasi }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="no_sk"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nomor SK</label>
                            <input type="text" name="no_sk" id="no_sk"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="" value="{{ $ambil->no_sk_akreditasi }}">
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berlaku"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berlaku</label>
                            <input type="date" name="tgl_berlaku" id="tgl_berlaku"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="" value="{{ $ambil->start_date }}">
                        </div>
                        <div class="col-span-2">
                            <label for="tgl_berakhir"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal
                                Berakhir</label>
                            <input type="date" name="tgl_berakhir" id="tgl_berakhir"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                placeholder="" required="" value="{{ $ambil->end_date }}">
                        </div>

                        <div class="col-span-2 rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="mb-2 text-xs text-gray-500">Minimal salah satu harus ada: file PDF atau link file. Jika ada file upload, file itu yang ditampilkan ke publik.</p>
                            @if ($ambil->file)
                                <div class="mb-3 flex flex-wrap items-center gap-3 text-sm">
                                    <a href="{{ url('filesk/' . $ambil->id_riwayat_akreditasi) }}" target="_blank" rel="noopener"
                                        class="font-medium text-indigo-600 hover:underline">Lihat file saat ini</a>
                                    <label class="inline-flex items-center gap-1 text-red-700">
                                        <input type="checkbox" name="hapus_berkas" value="1" @checked(old('hapus_berkas'))
                                            class="size-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        Hapus file upload
                                    </label>
                                </div>
                            @endif
                            <label for="berkas"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ $ambil->file ? 'Ganti File' : 'Upload File' }} (PDF, maks. 10 MB)</label>
                            <input type="file" name="berkas" id="berkas" accept="application/pdf,.pdf"
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-sm text-gray-900 file:mr-3 file:border-0 file:bg-gray-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700">
                            @error('berkas') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            <label for="url"
                                class="mt-3 block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link File</label>
                            <input type="url" name="url" id="url"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="https://..." value="{{ old('url', $ambil->url) }}">
                            @error('url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="col-span-2">
                            <label for="status"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                            <select id="status" name="status"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
                                <option value="1" @selected($ambil->status == 1)>Aktif</option>
                                <option value="0" @selected($ambil->status != 1)>Tidak Aktif</option>
                            </select>
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