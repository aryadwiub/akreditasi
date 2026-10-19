{{-- satu isian form referensi; variabel: $nama, $f (definisi field), $nilai, $opsi, $kunci (hanya tampil), $galat (pesan error) --}}
@php
    $kelas = 'block w-full rounded-lg border bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 '
        . ($galat ? 'border-red-400 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500');
    $wajib = in_array('required', $f['aturan'], true);
    $idEl = $prefix . $nama;
@endphp
<div>
    <label for="{{ $idEl }}" class="mb-1 block text-xs font-medium text-gray-600">
        {{ $f['label'] }} @if ($wajib && !$kunci)<span class="text-red-500">*</span>@endif
    </label>
    @if ($kunci)
        <div class="rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-medium text-gray-700">{{ $nilai }}</div>
    @elseif (isset($opsi[$nama]))
        <select id="{{ $idEl }}" name="{{ $nama }}" class="{{ $kelas }}" @required($wajib)>
            <option value="">-- Pilih {{ mb_strtolower($f['label']) }} --</option>
            @foreach ($opsi[$nama] as $kode => $label)
                <option value="{{ $kode }}" @selected((string) $nilai === (string) $kode)>{{ $label }}</option>
            @endforeach
        </select>
    @else
        <input type="{{ $f['tipe'] ?? 'text' }}" id="{{ $idEl }}" name="{{ $nama }}" value="{{ $nilai }}" class="{{ $kelas }}"
            placeholder="{{ $f['contoh'] ?? '' }}" @required($wajib) autocomplete="off"
            @if (preg_grep('/^max:/', $f['aturan']) && ($f['tipe'] ?? 'text') === 'text') maxlength="{{ substr(current(preg_grep('/^max:/', $f['aturan'])), 4) }}" @endif>
    @endif
    @if ($galat)
        <p class="mt-1 text-xs text-red-600">{{ $galat }}</p>
    @elseif (!empty($f['bantuan']))
        <p class="mt-1 text-xs text-gray-500">{{ $f['bantuan'] }}</p>
    @endif
</div>
