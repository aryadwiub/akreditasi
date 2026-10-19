<x-layout>
    <x-slot:judul>{{ $judul }}</x-slot>

    <section class="bg-white">
        <div class="mx-auto max-w-lg px-4 py-8 lg:py-14">
            <div class="overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">
                <div class="border-b border-gray-200 bg-gray-50 px-5 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ trim($data->nama_jenjang . ' ' . $data->nama_prodi) }}</h2>
                    <p class="text-sm text-gray-500">
                        Masa berlaku:
                        {{ $data->start_date ? \Carbon\Carbon::parse($data->start_date)->translatedFormat('d M Y') : '-' }}
                        &ndash;
                        {{ $data->end_date ? \Carbon\Carbon::parse($data->end_date)->translatedFormat('d M Y') : '-' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('berkas.verifikasi', $token) }}" class="px-5 py-5" autocomplete="off">
                    @csrf
                    <label for="captcha" class="mb-2 block text-sm font-medium text-gray-700">
                        Untuk membuka file, jawab hasil penjumlahan berikut:
                    </label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <img src="{{ route('berkas.captcha') }}?t={{ microtime(true) }}" alt="Soal captcha penjumlahan"
                            width="150" height="46" class="rounded-lg ring-1 ring-gray-300" />
                        <input type="text" id="captcha" name="captcha" inputmode="numeric" maxlength="3" required autofocus
                            placeholder="Jawaban"
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:w-32" />
                    </div>
                    @error('captcha')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-5 flex flex-col-reverse gap-2 sm:flex-row sm:justify-between">
                        <a href="{{ url()->current() }}" class="text-center text-sm text-gray-500 hover:underline sm:self-center">Ganti soal</a>
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-gray-800 px-5 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Buka File
                        </button>
                    </div>
                </form>
            </div>
            <p class="mt-3 text-center text-xs text-gray-400">Akses file dicatat untuk keperluan keamanan.</p>
        </div>
    </section>
</x-layout>
