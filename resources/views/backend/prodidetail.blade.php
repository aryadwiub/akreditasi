<x-backend-layout>

    {{-- informasi prodi --}}
    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-start gap-3">
                <a href="{{ url('prodi/' . $detail->id_fakultas) }}"
                    class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $detail->nama_jenjang }} {{ $detail->nama_prodi }}</h2>
                    <p class="text-sm text-gray-500">{{ $detail->nm_fakultas }}</p>
                </div>
            </div>
            @if ($detail->status == 1)
                <span class="inline-flex w-fit items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Prodi Aktif</span>
            @else
                <span class="inline-flex w-fit items-center rounded-full bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">Prodi Tidak Aktif</span>
            @endif
        </div>

        {{-- ringkasan --}}
        <dl class="grid grid-cols-1 divide-y divide-gray-200 text-sm sm:grid-cols-2 sm:divide-y-0 lg:grid-cols-3">
            <div class="px-4 py-3 sm:px-6">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Jenjang</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $detail->nama_jenjang }}</dd>
            </div>
            <div class="px-4 py-3 sm:px-6">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Akreditasi Nasional</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $detail->akreditasi ?: '-' }}</dd>
            </div>
            <div class="px-4 py-3 sm:px-6">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Lembaga Akreditasi Nasional</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $detail->lembaga ?: '-' }}</dd>
            </div>
            <div class="px-4 py-3 sm:px-6">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Berlaku Sampai</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $detail->end_date ?: '-' }}</dd>
            </div>
            <div class="px-4 py-3 sm:px-6">
                <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Akreditasi Internasional</dt>
                <dd class="mt-1 font-medium text-gray-900">
                    {{ $detail->internasional != '' ? $detail->internasional : 'Belum Terakreditasi Internasional' }}
                </dd>
            </div>
            @if ($detail->internasional != '')
                <div class="px-4 py-3 sm:px-6">
                    <dt class="text-xs font-medium uppercase tracking-wider text-gray-500">Internasional Berlaku Sampai</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ $detail->tgl_berakhir }}</dd>
                </div>
            @endif
        </dl>
    </article>

    @if ($errors->any())
        <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <p class="font-semibold">Data gagal disimpan:</p>
            <ul class="mt-1 list-disc ps-5">
                @foreach ($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- riwayat akreditasi nasional --}}
    <article class="mt-6 overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Akreditasi Nasional</h3>
                <p class="text-sm text-gray-500">{{ $riwayat_nasional->count() }} data</p>
            </div>
            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" type="button"
                class="inline-flex w-fit items-center gap-1 rounded-lg bg-gray-800 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                </svg>
                Tambah Data
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Akreditasi</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Lembaga</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Nomor SK</th>
                        <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Tgl Berlaku</th>
                        <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Tgl Berakhir</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">File</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Status SK</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($riwayat_nasional as $item)
                        <tr class="transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 text-center font-medium text-gray-900 sm:px-4">{{ $item->akreditasi }}</td>
                            <td class="whitespace-nowrap px-3 py-3 sm:px-4">{{ $item->lembaga }}</td>
                            <td class="min-w-40 px-3 py-3 sm:px-4">{{ $item->no_sk_akreditasi }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4">{{ $item->start_date }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4">{{ $item->end_date }}</td>
                            <td class="px-3 py-3 sm:px-4">
                                @if ($item->file)
                                    <a href="{{ url('filesk/' . $item->id_riwayat_akreditasi) }}" target="_blank" rel="noopener"
                                        class="whitespace-nowrap font-medium text-indigo-600 hover:underline">Lihat File</a>
                                    <span class="ms-1 inline-flex rounded bg-gray-200 px-1.5 py-0.5 text-[10px] font-semibold text-gray-600">UPLOAD</span>
                                @elseif ($item->url)
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener"
                                        class="whitespace-nowrap font-medium text-indigo-600 hover:underline">Buka Link</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center sm:px-4">
                                @if ($item->status == '1')
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-600">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ url('ubah/' . $item->id_riwayat_akreditasi) }}" title="Ubah"
                                        class="rounded-lg p-1.5 text-gray-600 transition hover:bg-gray-200 hover:text-gray-900">
                                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                                        </svg>
                                        <span class="sr-only">Ubah</span>
                                    </a>
                                    <form action="{{ url('hapus/' . $item->id_riwayat_akreditasi) }}" method="post">
                                        @method('delete')
                                        @csrf
                                        <input type="hidden" name="prodi" value="{{ $item->id_prodi }}">
                                        <button type="submit" title="Hapus"
                                            onclick="return confirm('Yakin dihapus ta om? (iki datae akreditasi nasional)')"
                                            class="rounded-lg p-1.5 text-red-600 transition hover:bg-red-50 hover:text-red-700">
                                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="sr-only">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat akreditasi nasional.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    {{-- riwayat akreditasi internasional --}}
    <article class="mt-6 overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Akreditasi Internasional</h3>
                <p class="text-sm text-gray-500">{{ $riwayat_internasional->count() }} data</p>
            </div>
            <button data-modal-target="crud-modal-internasional" data-modal-toggle="crud-modal-internasional" type="button"
                class="inline-flex w-fit items-center gap-1 rounded-lg bg-gray-800 px-3 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                </svg>
                Tambah Data
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Lembaga</th>
                        <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Tgl Berlaku</th>
                        <th scope="col" class="whitespace-nowrap px-3 py-3 text-center sm:px-4">Tgl Berakhir</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">File</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Status SK</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($riwayat_internasional as $item)
                        <tr class="transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $loop->iteration }}</td>
                            <td class="whitespace-nowrap px-3 py-3 font-medium text-gray-900 sm:px-4">{{ $item->nama_lembaga }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4">{{ $item->tgl_berlaku }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-center sm:px-4">{{ $item->tgl_berakhir }}</td>
                            <td class="px-3 py-3 sm:px-4">
                                @if ($item->url)
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener"
                                        class="font-medium text-indigo-600 hover:underline">Buka File</a>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center sm:px-4">
                                @if ($item->tgl_berakhir > now())
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-600">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 sm:px-4">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ url('ubahinter/' . $item->id_riwayat) }}" title="Ubah"
                                        class="rounded-lg p-1.5 text-gray-600 transition hover:bg-gray-200 hover:text-gray-900">
                                        <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                                        </svg>
                                        <span class="sr-only">Ubah</span>
                                    </a>
                                    <form action="{{ url('hapusinter/' . $item->id_riwayat) }}" method="post">
                                        @method('delete')
                                        @csrf
                                        <input type="hidden" name="prodi" value="{{ $item->id_prodi }}">
                                        <button type="submit" title="Hapus"
                                            onclick="return confirm('Yakin dihapus ta om? (iki datae akreditasi internasional)')"
                                            class="rounded-lg p-1.5 text-red-600 transition hover:bg-red-50 hover:text-red-700">
                                            <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd"
                                                    d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="sr-only">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat akreditasi internasional.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
                        <div class="col-span-2 rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="mb-2 text-xs text-gray-500">Isi <b>salah satu</b>: upload file PDF atau link file. Jika keduanya diisi, file upload yang ditampilkan ke publik.</p>
                            <label for="berkas"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Upload File (PDF, maks. 10 MB)</label>
                            <input type="file" name="berkas" id="berkas" accept="application/pdf,.pdf"
                                class="block w-full cursor-pointer rounded-lg border border-gray-300 bg-white text-sm text-gray-900 file:mr-3 file:border-0 file:bg-gray-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700">
                            <label for="url"
                                class="mt-3 block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link File</label>
                            <input type="url" name="url" id="url" value="{{ old('url') }}"
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                                placeholder="https://...">
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
                        class="text-white inline-flex items-center bg-gray-800 hover:bg-gray-700 focus:ring-2 focus:outline-none focus:ring-indigo-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
                        class="text-white inline-flex items-center bg-gray-800 hover:bg-gray-700 focus:ring-2 focus:outline-none focus:ring-indigo-500 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
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
