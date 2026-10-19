@php
    // menu aktif ditentukan dari path halaman (termasuk halaman turunannya)
    $menuAktif = [
        'dashboard' => request()->is('dashboard'),
        'prodi' => request()->is('fakultas', 'prodi/*', 'prodidetail/*', 'ubah/*', 'ubahinter/*'),
        'rekap' => request()->is('rekap*', 'kadal*', 'lembarkerja*'),
        'log' => request()->is('logakses', 'loglembarkerja'),
        'referensi' => request()->is('referensi*', 'ubahfakultas/*', 'ubahprodi/*', 'ubahperingkat/*', 'ubahlembaga*'),
    ];
    $menuRekap = [
        ['/rekapakreditasi', 'Rekap Akreditasi Aktif'],
        ['/rekap_tanggal_awal', 'Akreditasi Nasional per Tanggal'],
        ['/rekap_tanggal_awal_inter', 'Akreditasi Internasional per Tanggal'],
    ];
    $menuLembarKerja = [['/lembarkerja', 'Prodi yang Akan Akreditasi']];
    $menuLog = [
        ['/logakses', 'Log Akses File SK'],
        ['/loglembarkerja', 'Log Lembar Kerja'],
    ];
    $menuReferensi = [
        ['/referensifakultas', 'Fakultas'],
        ['/referensiprodi', 'Prodi'],
        ['/referensiperingkat', 'Peringkat Akreditasi Nasional'],
        ['/referensilembaganasional', 'Lembaga Akreditasi Nasional'],
        ['/referensilembagainter', 'Lembaga Akreditasi Internasional'],
        ['/referensisatudata', 'Prodi SatuData (API)'],
    ];
    $menuPengguna = [['/referensiuser', 'User']];

    $pengguna = auth()->user();
    $namaPengguna = $pengguna?->nama ?: $pengguna?->username;
    $inisial = strtoupper(mb_substr((string) $namaPengguna, 0, 1)) ?: '?';

    $kelasMenu = fn ($aktif) => $aktif
        ? 'bg-gray-900 text-white'
        : 'text-gray-300 hover:bg-white/5 hover:text-white';
    $kelasItem = fn ($url) => request()->is(ltrim($url, '/'), ltrim($url, '/') . '/*')
        ? 'bg-gray-100 font-semibold text-gray-900'
        : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900';
    $kelasMobile = fn ($aktif) => $aktif
        ? 'bg-gray-900 text-white'
        : 'text-gray-300 hover:bg-white/5 hover:text-white';
    $panah = '<svg class="size-4 opacity-70" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>';
@endphp

<nav class="bg-gray-800 shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">

            {{-- brand + menu utama --}}
            <div class="flex items-center gap-8">
                <a href="/dashboard" class="flex shrink-0 items-center gap-3">
                    <span class="flex h-10 items-center rounded-lg bg-white px-2">
                        <img src="{{ asset('img/logo-bpm-bg-putih-removebg-preview.png') }}" alt="BPM Universitas Airlangga" class="h-8 w-auto" />
                    </span>
                    <span class="hidden leading-tight lg:block">
                        <span class="block text-sm font-semibold text-white">SIM Akreditasi</span>
                        <span class="block text-xs text-gray-400">Admin BPM</span>
                    </span>
                </a>

                <div class="hidden md:flex md:items-center md:gap-1">
                    <a href="/dashboard" @if ($menuAktif['dashboard']) aria-current="page" @endif
                        class="{{ $kelasMenu($menuAktif['dashboard']) }} rounded-md px-3 py-2 text-sm font-medium">Dashboard</a>

                    <a href="/fakultas" @if ($menuAktif['prodi']) aria-current="page" @endif
                        class="{{ $kelasMenu($menuAktif['prodi']) }} rounded-md px-3 py-2 text-sm font-medium">Prodi</a>

                    <button type="button" id="menuRekapTombol" data-dropdown-toggle="menuRekap" data-dropdown-offset-distance="8"
                        class="{{ $kelasMenu($menuAktif['rekap']) }} inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium">
                        Rekap {!! $panah !!}
                    </button>
                    <div id="menuRekap" class="z-20 hidden w-72 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5">
                        <ul class="text-sm" aria-labelledby="menuRekapTombol">
                            @foreach ($menuRekap as [$url, $label])
                                <li><a href="{{ $url }}" class="{{ $kelasItem($url) }} block px-4 py-2">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                        <div class="mt-1 border-t border-gray-100 pt-1">
                            <p class="px-4 pb-1 pt-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Lembar Kerja</p>
                            <ul class="text-sm">
                                @foreach ($menuLembarKerja as [$url, $label])
                                    <li><a href="{{ $url }}" class="{{ $kelasItem($url) }} block px-4 py-2">{{ $label }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <button type="button" id="menuLogTombol" data-dropdown-toggle="menuLog" data-dropdown-offset-distance="8"
                        class="{{ $kelasMenu($menuAktif['log']) }} inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium">
                        Log {!! $panah !!}
                    </button>
                    <div id="menuLog" class="z-20 hidden w-60 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5">
                        <ul class="text-sm" aria-labelledby="menuLogTombol">
                            @foreach ($menuLog as [$url, $label])
                                <li><a href="{{ $url }}" class="{{ $kelasItem($url) }} block px-4 py-2">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    <button type="button" id="menuReferensiTombol" data-dropdown-toggle="menuReferensi" data-dropdown-offset-distance="8"
                        class="{{ $kelasMenu($menuAktif['referensi']) }} inline-flex items-center gap-1 rounded-md px-3 py-2 text-sm font-medium">
                        Referensi {!! $panah !!}
                    </button>
                    <div id="menuReferensi" class="z-20 hidden w-72 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5">
                        <ul class="text-sm" aria-labelledby="menuReferensiTombol">
                            @foreach ($menuReferensi as [$url, $label])
                                <li><a href="{{ $url }}" class="{{ $kelasItem($url) }} block px-4 py-2">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                        <div class="mt-1 border-t border-gray-100 pt-1">
                            <p class="px-4 pb-1 pt-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Pengguna</p>
                            <ul class="text-sm">
                                @foreach ($menuPengguna as [$url, $label])
                                    <li><a href="{{ $url }}" class="{{ $kelasItem($url) }} block px-4 py-2">{{ $label }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- menu pengguna --}}
            <div class="hidden md:block">
                <button type="button" id="menuAkunTombol" data-dropdown-toggle="menuAkun" data-dropdown-placement="bottom-end" data-dropdown-offset-distance="8"
                    class="flex items-center gap-2 rounded-full py-1 pl-1 pr-2 text-sm text-gray-300 hover:bg-white/5 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="flex size-8 items-center justify-center rounded-full bg-indigo-500 text-sm font-semibold text-white">{{ $inisial }}</span>
                    <span class="hidden max-w-40 truncate font-medium lg:block">{{ $namaPengguna }}</span>
                    {!! $panah !!}
                </button>
                <div id="menuAkun" class="z-20 hidden w-56 rounded-lg bg-white py-1 shadow-lg ring-1 ring-black/5">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ $namaPengguna }}</p>
                        <p class="truncate text-xs text-gray-500">{{ $pengguna?->username }}</p>
                    </div>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-.943a.75.75 0 1 0-1.004-1.114l-2.5 2.25a.75.75 0 0 0 0 1.114l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-.943h9.546A.75.75 0 0 0 19 10Z" clip-rule="evenodd" />
                            </svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>

            {{-- tombol menu mobile --}}
            <div class="-mr-2 flex md:hidden">
                <button type="button" command="--toggle" commandfor="mobile-menu"
                    class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
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

    {{-- menu mobile --}}
    <el-disclosure id="mobile-menu" hidden class="block border-t border-white/10 md:hidden">
        <div class="space-y-1 px-2 pb-3 pt-2">
            <a href="/dashboard" class="{{ $kelasMobile($menuAktif['dashboard']) }} block rounded-md px-3 py-2 text-base font-medium">Dashboard</a>
            <a href="/fakultas" class="{{ $kelasMobile($menuAktif['prodi']) }} block rounded-md px-3 py-2 text-base font-medium">Prodi</a>

            <p class="px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Rekap</p>
            @foreach ($menuRekap as [$url, $label])
                <a href="{{ $url }}" class="{{ $kelasMobile(request()->is(ltrim($url, '/'))) }} block rounded-md px-3 py-2 pl-6 text-sm font-medium">{{ $label }}</a>
            @endforeach

            <p class="px-6 pb-1 pt-2 text-xs font-semibold text-gray-500">Lembar Kerja</p>
            @foreach ($menuLembarKerja as [$url, $label])
                <a href="{{ $url }}" class="{{ $kelasMobile(request()->is(ltrim($url, '/'), ltrim($url, '/') . '/*')) }} block rounded-md px-3 py-2 pl-9 text-sm font-medium">{{ $label }}</a>
            @endforeach

            <p class="px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Log</p>
            @foreach ($menuLog as [$url, $label])
                <a href="{{ $url }}" class="{{ $kelasMobile(request()->is(ltrim($url, '/'))) }} block rounded-md px-3 py-2 pl-6 text-sm font-medium">{{ $label }}</a>
            @endforeach

            <p class="px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Referensi</p>
            @foreach ($menuReferensi as [$url, $label])
                <a href="{{ $url }}" class="{{ $kelasMobile(request()->is(ltrim($url, '/'))) }} block rounded-md px-3 py-2 pl-6 text-sm font-medium">{{ $label }}</a>
            @endforeach
            <p class="px-6 pb-1 pt-2 text-xs font-semibold text-gray-500">Pengguna</p>
            @foreach ($menuPengguna as [$url, $label])
                <a href="{{ $url }}" class="{{ $kelasMobile(request()->is(ltrim($url, '/'))) }} block rounded-md px-3 py-2 pl-9 text-sm font-medium">{{ $label }}</a>
            @endforeach
        </div>
        <div class="border-t border-white/10 pb-3 pt-4">
            <div class="flex items-center gap-3 px-5">
                <span class="flex size-10 items-center justify-center rounded-full bg-indigo-500 text-base font-semibold text-white">{{ $inisial }}</span>
                <div class="min-w-0">
                    <div class="truncate text-base font-medium text-white">{{ $namaPengguna }}</div>
                    <div class="truncate text-sm text-gray-400">{{ $pengguna?->username }}</div>
                </div>
            </div>
            <div class="mt-3 px-2">
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-base font-medium text-gray-300 hover:bg-white/5 hover:text-white">Log out</button>
                </form>
            </div>
        </div>
    </el-disclosure>
</nav>
