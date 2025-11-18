<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.css" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/css/dataTables.tailwindcss.css"></script> 
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3"></script>
    <script src="https://cdn.tailwindcss.com/"></script>

    <title>SIM AKREDITASI</title>
</head>

<body class="h-full"> 

<div class="min-h-full">

    <x-navbar></x-navbar>
    <x-header>{{ $judul }}</x-header>

  <main>
    <div class="w-full">
      {{ $slot }}
    </div>
  </main>

</div>

</body>

<footer class="w-full bg-black shadow-sm dark:bg-orange-600">
    <div class="w-full max-w-screen-xl mx-auto p-4 md:py-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <a href="https://flowbite.com/" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
                <img src="img/logo-bpm-bg-putih-removebg-preview.png" class="w-[180px]" alt="Flowbite Logo" />
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Badan Penjaminan Mutu <br>Universitas Airlangga</span>
            </a>
            <ul class="flex flex-wrap items-center mb-6 text-sm font-medium text-gray-200 sm:mb-0 dark:text-gray-200">
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">About</a>
                </li>
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">Privacy Policy</a>
                </li>
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">Licensing</a>
                </li>
                <li>
                    <a href="#" class="hover:underline me-4 md:me-6">Contact</a>
                </li>
                <li>
                    <a href="{{ url('/login') }}" class="hover:underline me-4 md:me-6">Login</a>
                </li>
            </ul>
        </div>
        <hr class="my-6 border-gray-800 sm:mx-auto dark:border-gray-700 lg:my-8" />
        <span class="block text-sm text-gray-200 sm:text-center dark:text-gray-200">© 2025 <a href="https://flowbite.com/" class="hover:underline">BPM</a></span>
    </div>
</footer>


</html>