@php
    $menuPublik = [
        ['/', 'Akreditasi Prodi', request()->is('/')],
        ['/akreditasi-internasional', 'Akreditasi Internasional', request()->is('akreditasi-internasional')],
    ];
@endphp

{{-- identitas --}}
<div class="border-b border-gray-200 bg-white">
    <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="/" class="shrink-0">
            <img src="{{ asset('img/logo-bpm-bg-putih-removebg-preview.png') }}" alt="BPM Universitas Airlangga" class="h-14 w-auto sm:h-16" />
        </a>
        <div class="hidden border-l border-gray-200 pl-4 sm:block">
            <p class="text-lg font-semibold leading-tight text-gray-900">Sistem Informasi Akreditasi</p>
            <p class="text-sm text-gray-500">Badan Penjaminan Mutu Universitas Airlangga</p>
        </div>
    </div>
</div>

{{-- menu --}}
<nav class="bg-orange-600 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-12 items-center justify-between">
            <div class="hidden md:flex md:items-center md:gap-1">
                @foreach ($menuPublik as [$url, $label, $aktif])
                    <a href="{{ $url }}" @if ($aktif) aria-current="page" @endif
                        class="{{ $aktif ? 'bg-orange-700 text-white' : 'text-orange-50 hover:bg-orange-500 hover:text-white' }} rounded-md px-3 py-2 text-sm font-medium transition-colors">{{ $label }}</a>
                @endforeach
            </div>

            {{-- judul menu aktif saat layar kecil --}}
            <span class="text-sm font-semibold text-white md:hidden">
                {{ collect($menuPublik)->firstWhere(2, true)[1] ?? 'Menu' }}
            </span>

            <div class="-mr-2 flex md:hidden">
                <button type="button" command="--toggle" commandfor="mobile-menu"
                    class="relative inline-flex items-center justify-center rounded-md p-2 text-white hover:bg-orange-500 focus:outline-2 focus:outline-offset-2 focus:outline-white">
                    <span class="sr-only">Buka menu</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <el-disclosure id="mobile-menu" hidden class="block border-t border-orange-500 md:hidden">
        <div class="space-y-1 px-2 pb-3 pt-2">
            @foreach ($menuPublik as [$url, $label, $aktif])
                <a href="{{ $url }}" @if ($aktif) aria-current="page" @endif
                    class="{{ $aktif ? 'bg-orange-700 text-white' : 'text-orange-50 hover:bg-orange-500 hover:text-white' }} block rounded-md px-3 py-2 text-base font-medium">{{ $label }}</a>
            @endforeach
        </div>
    </el-disclosure>
</nav>
