<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Clover — POS & Manajemen Kafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <header class="bg-slate-900/80 border-b border-slate-800 sticky top-0 z-50 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center text-white font-bold text-xl shadow-sm">
                    ☘️
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white leading-tight">Kasir Clover</h1>
                    <p class="text-xs text-slate-400">Aplikasi Kasir & Inventaris Kafe</p>
                </div>
            </div>

            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-all shadow-md">
                        Buka Dashboard →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition-all shadow-md">
                        Masuk (Login)
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="max-w-4xl mx-auto px-4 py-16 flex-1 flex flex-col justify-center text-center">
        
        <div class="mb-10">
            <span class="inline-block px-3.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 text-xs font-bold border border-emerald-500/30 mb-4">
                Sistem Kasir & Resep Terintegrasi
            </span>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white mb-4 tracking-tight">
                Kelola Pesanan Kafe & Stok Jadi Lebih Cepat
            </h2>
            <p class="text-slate-400 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
                Antarmuka kasir modern untuk melayani transaksi pelanggan, menghitung kembalian otomatis, cetak struk thermal, dan melacak persediaan bahan baku.
            </p>
            <div class="mt-8">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-xl shadow-emerald-950/50 hover:scale-105 transition-all">
                    <span>Masuk ke Aplikasi Kasir</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>

        <!-- 4 Fitur Ringkas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center mt-6">
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm">
                <div class="text-lg font-bold text-emerald-400 mb-1">Kasir Cepat</div>
                <div class="text-xs text-slate-400">Klik & langsung bayar</div>
            </div>
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm">
                <div class="text-lg font-bold text-emerald-400 mb-1">Cetak Struk</div>
                <div class="text-xs text-slate-400">Format thermal 58/80mm</div>
            </div>
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm">
                <div class="text-lg font-bold text-emerald-400 mb-1">Stok Otomatis</div>
                <div class="text-xs text-slate-400">Potong bahan per resep</div>
            </div>
            <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-sm">
                <div class="text-lg font-bold text-emerald-400 mb-1">Laporan Rapi</div>
                <div class="text-xs text-slate-400">Export Excel & PDF</div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 border-t border-slate-800/80 py-4 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Kasir Clover &bull; Sistem POS & Inventori Kafe
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
