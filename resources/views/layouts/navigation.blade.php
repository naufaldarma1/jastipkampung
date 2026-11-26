<nav class="sticky top-0 z-50 bg-[#FEFAF7] px-6 py-4 flex flex-wrap justify-between items-center border-b border-[#EADBD1] gap-y-4" x-data="{ open: false }">
    <!-- Logo Kiri -->
    <div class="flex items-center">
        <!-- Icon JastipKampung -->
        <a href="/" class="flex items-center gap-2 hover:opacity-80 transition">
            <div class="bg-[#B5502E] text-white p-1 rounded-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            </div>
            <span class="font-bold text-[15px] text-gray-800 tracking-tight">Jastip<span class="text-[#B5502E]">Kampung</span></span>
        </a>

    </div>

    <!-- Links Menu & Settings Dropdown (Desktop) -->
    <div class="hidden sm:flex items-center gap-8 font-bold text-[14px]">
        <a href="/" class="text-gray-600 hover:text-gray-900 pb-0.5">Home</a>
        <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog') ? 'text-[#B5502E] border-b-2 border-[#B5502E]' : 'text-gray-600 hover:text-gray-900' }} pb-0.5">Katalog PO</a>
        @if (Auth::user()->role === 'pembeli')
            <a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*', 'checkout.*', 'payment.*') ? 'text-[#B5502E] border-b-2 border-[#B5502E]' : 'text-gray-600 hover:text-gray-900' }} pb-0.5">Keranjang <span class="ml-1 rounded-full bg-[#F5EBE4] px-2 py-0.5 text-[11px] text-[#B5502E]">{{ collect(session('cart', []))->sum('quantity') }}</span></a>
            <a href="{{ route('pesanan.index') }}" class="{{ request()->routeIs('pesanan.*') ? 'text-[#B5502E] border-b-2 border-[#B5502E]' : 'text-gray-600 hover:text-gray-900' }} pb-0.5">Pesanan Saya</a>
            <a href="{{ route('jastiper.join') }}" class="{{ request()->routeIs('jastiper.join') ? 'text-[#B5502E] border-b-2 border-[#B5502E]' : 'text-gray-600 hover:text-gray-900' }} pb-0.5">Jadi Jastiper</a>
        @endif
        
        @php 
            $dashboardRoute = Auth::user()->role === 'jastiper' ? route('dashboard.jastiper') : route('dashboard.pembeli'); 
        @endphp
        <a href="{{ $dashboardRoute }}" class="{{ request()->routeIs('dashboard.*') ? 'text-[#B5502E] border-b-2 border-[#B5502E]' : 'text-gray-600 hover:text-gray-900' }} pb-0.5">{{ Auth::user()->role === 'jastiper' ? 'Dashboard Jastiper' : 'Dashboard' }}</a>
        
        <!-- Settings Dropdown -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="inline-flex items-center px-3 py-2 border border-[#EADBD1] text-sm font-bold rounded-lg text-gray-700 bg-white hover:text-gray-900 focus:outline-none transition shadow-sm ml-2">
                    <div>{{ Auth::user()->name }}</div>
                    <div class="ms-1">
                        <svg class="fill-current h-4 w-4 text-[#B5502E]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    Profile
                </x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>

    <!-- Mobile Menu Button -->
    <div class="-me-2 flex items-center sm:hidden">
        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden w-full">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="/" :active="request()->is('/')">Home</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('katalog')" :active="request()->routeIs('katalog')">Katalog PO</x-responsive-nav-link>
            @if (Auth::user()->role === 'pembeli')
                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*', 'checkout.*', 'payment.*')">Keranjang ({{ collect(session('cart', []))->sum('quantity') }})</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pesanan.index')" :active="request()->routeIs('pesanan.*')">Pesanan Saya</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('jastiper.join')" :active="request()->routeIs('jastiper.join')">Jadi Jastiper</x-responsive-nav-link>
            @endif
            <x-responsive-nav-link :href="$dashboardRoute" :active="request()->routeIs('dashboard.*')">{{ Auth::user()->role === 'jastiper' ? 'Dashboard Jastiper' : 'Dashboard' }}</x-responsive-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-[#EADBD1]">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        Log Out
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
