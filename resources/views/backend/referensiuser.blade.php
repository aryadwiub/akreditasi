<x-backend-layout>
    @php
        $inputClass = 'block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500';
        $labelClass = 'mb-1 block text-sm font-medium text-gray-700';
        $errorClass = 'mt-1 text-xs text-red-600';
        $aktif = old('aktif', $edit ? ($edit->aktif ? '1' : null) : (session()->hasOldInput() ? null : '1'));
    @endphp

    {{-- pesan --}}
    @if (session('sukses'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('sukses') }}</div>
    @endif
    @if (session('info'))
        <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
    @endif

    {{-- form pengguna --}}
    <article class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
            <h2 class="text-lg font-semibold text-gray-900">{{ $edit ? 'Ubah Pengguna' : 'Tambah Pengguna' }}</h2>
            <p class="text-sm text-gray-500">
                {{ $edit ? 'Mengubah data ' . ($edit->nama ?: $edit->username) . '.' : 'Cari NIP terlebih dahulu, lalu lengkapi data pengguna.' }}
            </p>
        </div>

        {{-- pencarian NIP --}}
        <form method="GET" action="{{ url('referensiuser') }}" class="border-b border-gray-200 px-4 py-4 sm:px-6">
            <label for="cari-nip" class="{{ $labelClass }}">Cari berdasarkan NIP</label>
            <div class="flex flex-col gap-2 sm:flex-row">
                <input type="text" id="cari-nip" name="nip" inputmode="numeric" maxlength="30"
                    value="{{ request('nip') }}" placeholder="Contoh: 198001252018013101" class="{{ $inputClass }} sm:max-w-sm" />
                <button type="submit"
                    class="inline-flex items-center justify-center gap-1 rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z"
                            clip-rule="evenodd" />
                    </svg>
                    Cari
                </button>
            </div>
            @if ($errorCari)
                <p class="{{ $errorClass }}">{{ $errorCari }}</p>
            @endif
        </form>

        {{-- data pengguna --}}
        <form method="POST" action="{{ $edit ? url('ubahsimpanuser/' . $edit->id) : url('simpanuser') }}" class="px-4 py-4 sm:px-6" autocomplete="off">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="username" class="{{ $labelClass }}">Username <span class="text-red-500">*</span></label>
                    <input type="text" id="username" name="username" maxlength="50" required
                        value="{{ old('username', $edit->username ?? '') }}" class="{{ $inputClass }}" />
                    @error('username') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nama" class="{{ $labelClass }}">Nama <span class="text-red-500">*</span></label>
                    <input type="text" id="nama" name="nama" maxlength="255" required
                        value="{{ old('nama', $edit->nama ?? '') }}" class="{{ $inputClass }}" />
                    @error('nama') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nip" class="{{ $labelClass }}">NIP <span class="text-red-500">*</span></label>
                    <input type="text" id="nip" name="nip" inputmode="numeric" maxlength="30" required
                        value="{{ old('nip', $edit->nip ?? $nipCari) }}" class="{{ $inputClass }}" />
                    @error('nip') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="{{ $labelClass }}">Email</label>
                    <input type="email" id="email" name="email" maxlength="255"
                        value="{{ old('email', $edit->email ?? '') }}" class="{{ $inputClass }}" />
                    @error('email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="{{ $labelClass }}">
                        Password @unless ($edit)<span class="text-red-500">*</span>@endunless
                    </label>
                    <input type="password" id="password" name="password" maxlength="255" autocomplete="new-password"
                        @unless ($edit) required @endunless
                        placeholder="{{ $edit ? 'Kosongkan jika tidak diganti' : 'Minimal 8 karakter' }}" class="{{ $inputClass }}" />
                    @error('password') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
                <div class="flex flex-col justify-end">
                    <label class="inline-flex cursor-pointer items-center gap-2 py-2">
                        <input type="checkbox" name="aktif" value="1" @checked($aktif)
                            class="size-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                        <span class="text-xs text-gray-500">(tidak dicentang = tidak aktif, tidak bisa login)</span>
                    </label>
                    @error('aktif') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-2 border-t border-gray-200 pt-4 sm:flex-row sm:justify-end">
                <a href="{{ url('referensiuser') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </article>

    {{-- daftar pengguna --}}
    <article class="mt-6 overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">
        <div class="flex flex-col gap-2 border-b border-gray-200 bg-gray-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Pengguna</h3>
            <span class="inline-flex w-fit items-center rounded-full bg-gray-800 px-3 py-1 text-xs font-semibold text-white">
                {{ $users->count() }} Pengguna
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-700">
                <thead class="bg-gray-800 text-xs uppercase tracking-wider text-white">
                    <tr>
                        <th scope="col" class="w-12 px-3 py-3 text-center sm:px-4">No</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Nama</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">Username</th>
                        <th scope="col" class="px-3 py-3 sm:px-4">NIP</th>
                        <th scope="col" class="hidden px-3 py-3 sm:px-4 md:table-cell">Email</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Status</th>
                        <th scope="col" class="px-3 py-3 text-center sm:px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($users as $item)
                        <tr class="transition-colors odd:bg-white even:bg-gray-50 hover:bg-gray-100 {{ $edit && $edit->id === $item->id ? '!bg-indigo-50' : '' }}">
                            <td class="px-3 py-3 text-center font-medium text-gray-500 sm:px-4">{{ $loop->iteration }}</td>
                            <td class="px-3 py-3 font-medium text-gray-900 sm:px-4">{{ $item->nama ?: '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 sm:px-4">{{ $item->username }}</td>
                            <td class="whitespace-nowrap px-3 py-3 sm:px-4">{{ $item->nip ?: '-' }}</td>
                            <td class="hidden px-3 py-3 sm:px-4 md:table-cell">{{ $item->email ?: '-' }}</td>
                            <td class="px-3 py-3 text-center sm:px-4">
                                @if ($item->aktif)
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Aktif</span>
                                @else
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-medium text-gray-600">Tidak Aktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-3 text-center sm:px-4">
                                <a href="{{ url('referensiuser?edit=' . $item->id) }}" title="Ubah"
                                    class="inline-flex rounded-lg p-1.5 text-gray-600 transition hover:bg-gray-200 hover:text-gray-900">
                                    <svg class="size-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="m5.433 13.917 1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                                    </svg>
                                    <span class="sr-only">Ubah</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

</x-backend-layout>
