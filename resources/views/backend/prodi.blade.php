<x-backend-layout>

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex items-start gap-3">
                <a href="{{ url('fakultas') }}"
                    class="inline-flex shrink-0 items-center gap-1 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12.79 5.23a.75.75 0 0 1-.02 1.06L8.832 10l3.938 3.71a.75.75 0 1 1-1.04 1.08l-4.5-4.25a.75.75 0 0 1 0-1.08l4.5-4.25a.75.75 0 0 1 1.06.02Z"
                            clip-rule="evenodd" />
                    </svg>
                    Kembali
                </a>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Daftar Program Studi</h2>
                    <p class="text-sm text-gray-500">{{ $prodi->first()->nm_fakultas ?? 'Pilih program studi untuk melihat detail akreditasi.' }}</p>
                </div>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white">
                <span id="jumlah-prodi">{{ $prodi->count() }}</span>&nbsp;Program Studi
            </span>
        </div> 

        {{-- pencarian & filter --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-4 py-3 sm:flex-row sm:items-center sm:px-6">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                        clip-rule="evenodd" />
                </svg>
                <label for="cari-prodi" class="sr-only">Cari program studi</label>
                <input type="search" id="cari-prodi" placeholder="Cari program studi atau jenjang..."
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-9 pr-3 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
            </div>
            <div class="sm:w-48">
                <label for="filter-status" class="sr-only">Filter status</label>
                <select id="filter-status"
                    class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="tidak-aktif">Tidak Aktif</option>
                </select>
            </div>
        </div>

        {{-- tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:w-16 sm:px-6">No</th>
                        <th scope="col" class="px-3 py-3 sm:px-6">Nama Program Studi</th>
                        <th scope="col" class="hidden px-3 py-3 sm:px-6 md:table-cell">Fakultas</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-6">Status</th>
                        <th scope="col" class="w-24 px-3 py-3 text-center sm:w-32 sm:px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($prodi as $item)
                        <tr class="baris-prodi transition-colors hover:bg-gray-100"
                            data-nama="{{ strtolower($item->nama_jenjang . ' ' . $item->nama_prodi) }}"
                            data-status="{{ $item->status == 1 ? 'aktif' : 'tidak-aktif' }}">
                            <td class="nomor-prodi px-3 py-3 text-center font-medium text-gray-500 sm:px-6">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-3 py-3 font-medium text-gray-900 sm:px-6">
                                {{ $item->nama_jenjang }} {{ $item->nama_prodi }}
                            </td>
                            <td class="hidden px-3 py-3 sm:px-6 md:table-cell">
                                {{ $item->nm_fakultas }}
                            </td>
                            <td class="px-3 py-3 text-center sm:px-6">
                                @if ($item->status == 1)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-600">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center sm:px-6">
                                <a href="{{ url('prodidetail/' . $item->id_prodi) }}"
                                    class="inline-flex items-center gap-1 rounded-lg bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    Lihat
                                    <svg class="size-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M7.21 14.77a.75.75 0 0 1 .02-1.06L11.168 10 7.23 6.29a.75.75 0 1 1 1.04-1.08l4.5 4.25a.75.75 0 0 1 0 1.08l-4.5 4.25a.75.75 0 0 1-1.06-.02Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data program studi.</td>
                        </tr>
                    @endforelse
                    <tr id="prodi-tidak-ditemukan" class="hidden">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">Program studi tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>

    <script>
        (function () {
            const inputCari = document.getElementById('cari-prodi');
            const filterStatus = document.getElementById('filter-status');
            const baris = document.querySelectorAll('.baris-prodi');
            const kosong = document.getElementById('prodi-tidak-ditemukan');
            const jumlah = document.getElementById('jumlah-prodi');

            function saring() {
                const kata = inputCari.value.trim().toLowerCase();
                const status = filterStatus.value;
                let tampil = 0;

                baris.forEach(function (tr) {
                    const cocok = tr.dataset.nama.includes(kata) && (!status || tr.dataset.status === status);
                    tr.classList.toggle('hidden', !cocok);
                    if (cocok) {
                        tampil++;
                        tr.querySelector('.nomor-prodi').textContent = tampil;
                        tr.classList.toggle('bg-gray-50', tampil % 2 === 0);
                    }
                });

                jumlah.textContent = tampil;
                kosong.classList.toggle('hidden', tampil > 0 || baris.length === 0);
            }

            inputCari.addEventListener('input', saring);
            filterStatus.addEventListener('change', saring);
            saring();
        })();
    </script>

</x-backend-layout>
