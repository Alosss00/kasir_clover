<!-- Sidebar Navigasi Kasir Clover (Simpel & User Friendly) -->
<aside 
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 text-slate-700 flex flex-col justify-between transition-transform duration-300 ease-in-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full lg:translate-x-0'"
>
    <div>
        <!-- Logo & Nama Aplikasi -->
        <div class="h-16 flex items-center gap-3 px-5 border-b border-slate-200 bg-white">
            <img src="{{ asset('images/logo-clover-primary.png') }}" alt="Clover Logo" class="w-9 h-9 rounded-xl object-contain shadow-sm p-0.5 bg-slate-50 border border-slate-100">
            <div class="min-w-0">
                <img src="{{ asset('images/logo-clover-space.png') }}" alt="Clover Space" class="h-5 w-auto object-contain">
                <p class="text-[10px] text-slate-500 font-semibold tracking-wide">POS & MANAGEMENT</p>
            </div>
        </div>

        <!-- Profil Pengguna -->
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/70">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->name }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block w-2 h-2 rounded-full {{ Auth::user()->isAdmin() ? 'bg-emerald-600' : 'bg-amber-500' }}"></span>
                        <span class="text-xs uppercase font-bold tracking-wider {{ Auth::user()->isAdmin() ? 'text-emerald-700' : 'text-amber-700' }}">
                            Role: {{ Auth::user()->role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi Utama -->
        <nav class="p-3 space-y-1.5 font-medium text-sm">
            <div class="px-3 pt-2 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                Menu Utama
            </div>

            <!-- 1. Kasir POS -->
            <a href="{{ route('kasir.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('kasir.*') ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'hover:bg-slate-100 text-slate-700' }}">
                <span class="text-lg">☕</span>
                <span class="text-sm">Kasir POS (Jual)</span>
            </a>

            @if(Auth::user()->isAdmin())
            <div class="px-3 pt-3 pb-1 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                Kelola Kafe (Admin)
            </div>

            <!-- 2. Bahan Baku & Stok -->
            <a href="{{ route('bahan-baku.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('bahan-baku.*') ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'hover:bg-slate-100 text-slate-700' }}">
                <span class="text-lg">📦</span>
                <span class="text-sm">Bahan Baku & Stok</span>
            </a>

            <!-- 3. Menu & Resep HPP -->
            <a href="{{ route('menu.index') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('menu.*') ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'hover:bg-slate-100 text-slate-700' }}">
                <span class="text-lg">📋</span>
                <span class="text-sm">Daftar Menu & Resep</span>
            </a>

            <!-- 4. Laporan Penjualan -->
            <a href="{{ route('laporan.penjualan') }}" class="flex items-center gap-3 px-3.5 py-3 rounded-xl transition-all {{ request()->routeIs('laporan.*') ? 'bg-emerald-600 text-white font-bold shadow-sm' : 'hover:bg-slate-100 text-slate-700' }}">
                <span class="text-lg">📊</span>
                <span class="text-sm">Laporan Penjualan</span>
            </a>
            @endif
        </nav>
    </div>

    <!-- Tombol Keluar (Logout) -->
    <div class="p-4 border-t border-slate-200 bg-slate-50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-rose-50 border border-slate-200 hover:border-rose-200 text-slate-700 hover:text-rose-600 transition-all text-xs font-bold shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>

<!-- Backdrop Overlay for Mobile -->
<div 
    x-show="sidebarOpen" 
    @click="sidebarOpen = false" 
    x-cloak
    class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-20 lg:hidden transition-opacity"
></div>
