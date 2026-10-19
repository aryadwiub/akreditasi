<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.7/css/responsive.dataTables.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/responsive/3.0.7/js/dataTables.responsive.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/css/dataTables.tailwindcss.css"></script> 
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
    <script src="https://cdn.tailwindcss.com/"></script>
    <script>
        // samakan font CDN Tailwind dengan --font-sans di resources/css/app.css (Inter)
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Inter Variable"', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', '"Noto Sans"', 'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"'] } } } };
    </script>

    <link rel="icon" type="image/png" href="{{ asset('img/logo-bpm-bg-putih-removebg-preview.png') }}" />
    <title>SIM AKREDITASI</title>
</head>

<body class="flex min-h-full flex-col font-sans antialiased">

    <x-navbar></x-navbar>
    <x-header>{{ $judul }}</x-header>

    <main class="w-full flex-1">
        {{ $slot }}
    </main>

    <footer class="bg-gray-900 text-gray-300">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                <div class="flex items-start gap-4">
                    <span class="flex shrink-0 items-center rounded-lg bg-white p-2">
                        <img src="{{ asset('img/logo-bpm-bg-putih-removebg-preview.png') }}" alt="BPM Universitas Airlangga" class="h-12 w-auto" />
                    </span>
                    <div>
                        <p class="font-semibold leading-snug text-white">Badan Penjaminan Mutu<br>Universitas Airlangga</p>
                        <p class="mt-1 text-sm text-gray-400">Sistem Informasi Akreditasi</p>
                    </div>
                </div>
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-white">Informasi Akreditasi</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="/" class="hover:text-white">Akreditasi Prodi</a></li>
                        <li><a href="/akreditasi-internasional" class="hover:text-white">Akreditasi Internasional</a></li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-white">Tautan</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a href="https://bpm.unair.ac.id" target="_blank" rel="noopener" class="hover:text-white">Badan Penjaminan Mutu UNAIR</a></li>
                        <li><a href="https://unair.ac.id" target="_blank" rel="noopener" class="hover:text-white">Universitas Airlangga</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-8 flex flex-col gap-2 border-t border-gray-800 pt-6 text-sm text-gray-400 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} Badan Penjaminan Mutu Universitas Airlangga</p>
                <!-- <a href="{{ url('/login') }}" class="hover:text-white">Login Admin</a> -->
            </div>
        </div>
    </footer>

</body>

</html>
