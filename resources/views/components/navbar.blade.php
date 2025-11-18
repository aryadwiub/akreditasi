{{-- logo --}}
  <nav class="bg-white/5">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-30 items-center justify-between">
        <div class="flex items-center">
          <div class="shrink-0">
            <img src="img/logo-bpm-bg-putih-removebg-preview.png" alt="Your Company" class="w-[180px]" />
          </div>
        </div>
      </div>
    </div>
  </nav>

{{-- menu --}}
  <nav class="bg-orange-600">
    <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-2">
      <div class="flex h-15 items-center justify-between">
        <div class="flex items-center">
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-2">
              <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->           
              <a href="/" class="{{ request() -> is ('/') ? 'bg-gray-800/50 px-3 py-2 text-sm font-medium text-white' : 'px-3 py-2 text-sm font-medium text-white/300 hover:bg-white/5 hover:text-white' }} rounded-md ">Akreditasi Prodi</a>
              <a href="/akreuniv" class="{{ request() -> is ('akreuniv') ? 'bg-gray-800/50 px-3 py-2 text-sm font-medium text-white' : 'px-3 py-2 text-sm font-medium text-white/300 hover:bg-white/5 hover:text-white' }} rounded-md">Akreditasi Universitas</a>
            </div>
          </div>
        </div>
        <div class="hidden md:block">
          <div class="ml-4 flex items-center md:ml-6">

          </div>
        </div>
        <div class="-mr-2 flex md:hidden">
          <!-- Mobile menu button -->
          <button type="button" command="--toggle" commandfor="mobile-menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
            <span class="absolute -inset-0.5"></span>
            <span class="sr-only">Open main menu</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
              <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
              <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <el-disclosure id="mobile-menu" hidden class="block md:hidden">
      <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
        <!-- Current: "bg-gray-950/50 text-white", Default: "text-gray-300 hover:bg-white/5 hover:text-white" -->
        <a href="/" class="{{ request() -> is ('/') ? 'bg-gray-800/50  text-white' : 'text-white/300 hover:bg-white/5 hover:text-white' }} block rounded-md px-3 py-2 text-sm font-medium">Akreditasi Prodi</a>
        <a href="/akreuniv" class="{{ request() -> is ('akreuniv') ? 'bg-gray-800/50 text-white' : 'text-white/300 hover:bg-white/5 hover:text-white' }} block rounded-md px-3 py-2 text-sm font-medium">Akreditasi Universitas</a>
      </div>
    </el-disclosure>
  </nav>