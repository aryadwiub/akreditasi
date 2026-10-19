<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
        @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/css/dataTables.tailwindcss.css"></script> 
    <script src="https://cdn.tailwindcss.com/"></script>
    <script>
        // samakan font CDN Tailwind dengan --font-sans di resources/css/app.css (Inter)
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Inter Variable"', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', '"Noto Sans"', 'sans-serif', '"Apple Color Emoji"', '"Segoe UI Emoji"', '"Segoe UI Symbol"', '"Noto Color Emoji"'] } } } };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>

    <link rel="icon" type="image/png" href="{{ asset('img/logo-bpm-bg-putih-removebg-preview.png') }}" />
    <title>SIM Akreditasi</title>
</head>
<body class="flex min-h-full flex-col font-sans antialiased">

    <x-backend-nav></x-backend-nav>

    <main class="w-full flex-1">
        <div class="w-full px-4 py-3 sm:px-10">
            {{ $slot }}
        </div>
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="flex flex-col gap-2 px-4 py-4 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between sm:px-10">
            <p>&copy; {{ date('Y') }} Badan Penjaminan Mutu Universitas Airlangga &middot; SIM Akreditasi</p>
            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-medium text-gray-600 hover:text-gray-900">
                Lihat halaman publik
                <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.25 5.5a.75.75 0 0 0-.75.75v8.5c0 .414.336.75.75.75h8.5a.75.75 0 0 0 .75-.75v-4a.75.75 0 0 1 1.5 0v4A2.25 2.25 0 0 1 12.75 17h-8.5A2.25 2.25 0 0 1 2 14.75v-8.5A2.25 2.25 0 0 1 4.25 4h5a.75.75 0 0 1 0 1.5h-5Z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M6.194 12.753a.75.75 0 0 0 1.06.053L16.5 4.44v2.81a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.553l-9.056 8.194a.75.75 0 0 0-.053 1.06Z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
    </footer>

</body>

</html>
