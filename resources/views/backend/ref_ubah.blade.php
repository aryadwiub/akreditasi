<x-backend-layout>
    {{-- form ubah referensi; definisi di ReferensiController::jenis() --}}
    <div class="mb-3">
        <a href="{{ url($j['url']) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-gray-900">
            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" /></svg>
            Kembali ke daftar {{ mb_strtolower($j['judul']) }}
        </a>
    </div>

    <article class="mx-auto max-w-2xl overflow-hidden rounded-xl bg-white shadow-md ring-1 ring-gray-200">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Referensi &middot; {{ $j['judul'] }}</p>
            <h2 class="text-lg font-semibold text-gray-900">Ubah {{ $j['judul'] }}</h2>
            <p class="text-sm text-gray-500">
                {{ $data->{$j['namaKolom']} }}
                @if ($pakai)
                    &middot; dipakai oleh <b>{{ $pakai }}</b> {{ $j['pakaiLabel'] }}
                @endif
            </p>
        </div>

        <form method="POST" action="{{ url('ubahsimpan' . $j['aksi'] . '/' . $id) }}">
            @csrf
            @method('PUT')
            <div class="space-y-4 px-4 py-5 sm:px-6">
                @foreach ($j['field'] as $nama => $f)
                    @include('backend.ref_field', [
                        'prefix' => '',
                        'nilai' => !empty($f['kunci']) ? $data->{$nama} : old($nama, $data->{$nama}),
                        'kunci' => !empty($f['kunci']),
                        'galat' => $errors->first($nama),
                    ])
                @endforeach
            </div>
            <div class="flex justify-end gap-2 border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
                <a href="{{ url($j['url']) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-100">Batal</a>
                <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">Simpan</button>
            </div>
        </form>
    </article>
</x-backend-layout>
