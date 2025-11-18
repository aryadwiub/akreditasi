<a {{ $attributes }}
 class="{{ request() -> is ('/') ? 'bg-gray-800/50  text-white' : 
 'text-gray/300 hover:bg-white/5 hover:text-white' }} 
 rounded-md px-3 py-2 text-sm font-large" 
 aria-current="{{ request() -> is ('/') ? 'page' : false }}">
 {{ $slot }}
</a>