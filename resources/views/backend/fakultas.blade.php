<x-backend-layout>

    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">

        {{-- judul --}}
        <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Fakultas</h2>
                <p class="text-sm text-gray-500">Pilih fakultas untuk melihat program studi.</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white">
                {{ $fakultas->count() }} Fakultas
            </span>
        </div>

        {{-- tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:w-16 sm:px-6">No</th>
                        <th scope="col" class="px-3 py-3 sm:px-6">Fakultas</th>
                        <th scope="col" class="w-24 px-3 py-3 text-center sm:w-32 sm:px-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($fakultas as $item)
                        <tr class="transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                            <td class="px-3 py-3 text-center font-medium text-gray-500 sm:px-6">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-3 py-3 font-medium text-gray-900 sm:px-6">
                                {{ $item->nm_fakultas }}
                            </td>
                            <td class="px-3 py-3 text-center sm:px-6">
                                <a href="{{ url('prodi/' . $item->id_fakultas) }}"
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
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada data fakultas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

</x-backend-layout>
