<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Clover — POS & Manajemen Kafe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col justify-between">

    <!-- Navbar Sederhana & Rapi -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 flex items-center justify-center text-white font-bold text-xl shadow-sm">
                    ☘
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900 leading-tight">Kasir Clover</h1>
                    <p class="text-xs text-slate-500">Aplikasi Kasir & Inventaris Kafe</p>
                </div>
            </div>

            <div>
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm transition-all shadow-sm">
                        Buka Aplikasi →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm transition-all shadow-sm">
                        Masuk (Login)
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Konten Utama: Bersih & Ramah Pengguna -->
    <main class="max-w-4xl mx-auto px-4 py-12 flex-1 flex flex-col justify-center">
        
        <div class="text-center mb-10">
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 mb-3">
                Aplikasi Kasir Praktis & Mudah Digunakan
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mb-3">
                Kelola Pesanan Kafe & Stok Jadi Lebih Cepat
            </h2>
            <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto">
                Antarmuka kasir yang simpel untuk melayani pelanggan, menghitung kembalian, cetak struk, dan memantau stok bahan secara real-time.
            </p>
        </div>

        <!-- 2 Pilihan Masuk Akun Cepat -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl mx-auto w-full mb-10">
            
            <!-- Kartu Kasir POS -->
            <div class="bg-white rounded-2xl p-6 border-2 border-emerald-500/20 hover:border-emerald-500 shadow-md hover:shadow-lg transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-2xl mb-4">
                        ☕
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Masuk sebagai Kasir</h3>
                    <p class="text-xs text-slate-500 mb-4">
                        Khusus untuk melayani pesanan pelanggan, input pembayaran, dan cetak struk.
                    </p>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 mb-6 space-y-1">
                        <div>Email: <strong class="text-slate-900">kasir@clover.com</strong></div>
                        <div>Password: <strong class="text-slate-900">password</strong></div>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm text-center transition-all shadow-sm">
                    Masuk ke Kasir POS →
                </a>
            </div>

            <!-- Kartu Administrator -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 hover:border-slate-400 shadow-md hover:shadow-lg transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-black text-2xl mb-4">
                        👑
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Masuk sebagai Admin</h3>
                    <p class="text-xs text-slate-500 mb-4">
                        Akses penuh kelola stok bahan baku, resep HPP menu, dan laporan penjualan.
                    </p>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 mb-6 space-y-1">
                        <div>Email: <strong class="text-slate-900">admin@clover.com</strong></div>
                        <div>Password: <strong class="text-slate-900">password</strong></div>
                    </div>
                </div>
                <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm text-center transition-all shadow-sm">
                    Masuk sebagai Admin →
                </a>
            </div>

        </div>

        <!-- 4 Fitur Ringkas -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xl font-bold text-emerald-600 mb-0.5">Kasir Cepat</div>
                <div class="text-xs text-slate-500">Klik & langsung bayar</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xl font-bold text-emerald-600 mb-0.5">Cetak Struk</div>
                <div class="text-xs text-slate-500">Format thermal 58/80mm</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xl font-bold text-emerald-600 mb-0.5">Stok Otomatis</div>
                <div class="text-xs text-slate-500">Potong bahan per resep</div>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <div class="text-xl font-bold text-emerald-600 mb-0.5">Laporan Rapi</div>
                <div class="text-xs text-slate-500">Export Excel & PDF</div>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Kasir Clover &bull; Sistem Kasir Sederhana & Ramah Pengguna
    </footer>

</body>
</html>
