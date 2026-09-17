<x-guest-layout>
    <div class="w-full max-w-4xl bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl shadow-emerald-950/40 overflow-hidden backdrop-blur-xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[560px]">
            
            <!-- Left Panel: Branding & Information -->
            <div class="lg:col-span-5 bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 p-8 sm:p-10 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-800/80 relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute -top-20 -left-20 w-56 h-56 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-56 h-56 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Brand Header -->
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 p-0.5 shadow-lg shadow-emerald-600/30 flex items-center justify-center">
                            <div class="w-full h-full bg-slate-950/60 rounded-[14px] flex items-center justify-center text-white text-2xl">
                                ☘️
                            </div>
                        </div>
                        <div>
                            <h1 class="text-xl font-extrabold text-white tracking-tight flex items-center gap-2">
                                Kasir Clover
                            </h1>
                            <p class="text-xs text-slate-400 font-medium">POS & Cafe Management System</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-2">
                        <h2 class="text-2xl font-bold text-slate-100 leading-snug">
                            Sistem Kasir Pintar & <br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">
                                Manajemen Inventori
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 leading-relaxed pt-1">
                            Akses modul POS kasir, pelacakan bahan baku otomatis, dan laporan laba penjualan harian secara terintegrasi.
                        </p>
                    </div>
                </div>

                <!-- Feature Highlights -->
                <div class="relative z-10 my-8 space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Transaksi Kasir Cepat</div>
                            <div class="text-[11px] text-slate-400">Responsif dan ramah layar sentuh POS</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                        <div class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 shrink-0">
                            <i data-lucide="package-check" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Kalkulasi HPP & Stok Otomatis</div>
                            <div class="text-[11px] text-slate-400">Potong bahan baku per cup secara real-time</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/40 border border-slate-700/50">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0">
                            <i data-lucide="printer" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Cetak Struk & Rekap Laporan</div>
                            <div class="text-[11px] text-slate-400">Dukungan printer thermal dan export PDF/Excel</div>
                        </div>
                    </div>
                </div>

                <!-- Status Live -->
                <div class="relative z-10 pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="inline-flex items-center gap-1.5 font-medium text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Server & Database Terhubung
                    </span>
                    <span class="text-slate-500">&copy; {{ date('Y') }} Kasir Clover</span>
                </div>
            </div>

            <!-- Right Panel: Professional Login Form -->
            <div class="lg:col-span-7 bg-slate-900 p-8 sm:p-12 flex flex-col justify-between">
                <div>
                    <!-- Header -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-1.5">
                            <h2 class="text-2xl font-extrabold text-white tracking-tight">Masuk ke Sistem</h2>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-800 border border-slate-700 text-[11px] font-medium text-slate-300">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                                <span>Aman</span>
                            </span>
                        </div>
                        <p class="text-xs text-slate-400">Masukkan email dan kata sandi akun Anda untuk melanjutkan.</p>
                    </div>

                    <!-- Session Status Alert -->
                    @if (session('status'))
                        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-4 h-4 shrink-0"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors Alert -->
                    @if ($errors->any())
                        <div class="mb-5 p-3.5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                                Gagal Masuk:
                            </div>
                            <ul class="list-disc list-inside text-[11px] text-rose-300/90 pl-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-4">
                        @csrf

                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-xs font-bold text-slate-300 mb-1.5">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                    <i data-lucide="mail" class="w-4 h-4"></i>
                                </div>
                                <input 
                                    id="email" 
                                    type="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    required 
                                    autofocus 
                                    autocomplete="username"
                                    placeholder="nama@email.com"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-950/70 border border-slate-700/90 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                                >
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-xs font-bold text-slate-300">
                                    Kata Sandi
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-[11px] text-slate-400 hover:text-emerald-400 transition-colors">
                                        Lupa sandi?
                                    </a>
                                @endif
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    placeholder="Masukkan kata sandi..."
                                    class="w-full pl-10 pr-11 py-3 rounded-xl bg-slate-950/70 border border-slate-700/90 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                                >
                                <button 
                                    type="button" 
                                    id="btn-toggle-password"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                                    title="Tampilkan / Sembunyikan Sandi"
                                >
                                    <i id="icon-eye-open" data-lucide="eye" class="w-4 h-4"></i>
                                    <i id="icon-eye-closed" data-lucide="eye-off" class="w-4 h-4 hidden"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input 
                                    id="remember_me" 
                                    type="checkbox" 
                                    name="remember" 
                                    class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-emerald-500 focus:ring-emerald-500/30 focus:ring-offset-0 focus:ring-1"
                                >
                                <span class="text-xs text-slate-400 font-medium">Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3">
                            <button 
                                type="submit" 
                                id="btn-submit"
                                class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-500 hover:via-emerald-400 hover:to-teal-500 text-white font-bold text-xs tracking-wide shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed"
                            >
                                <span id="btn-submit-text" class="flex items-center gap-2">
                                    <span>MASUK KE SISTEM</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </span>
                                <span id="btn-submit-loading" class="hidden items-center gap-2">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>MEMVERIFIKASI AKUN...</span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Footer Quick Notes -->
                <div class="mt-8 pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Enkripsi 256-bit Aktif</span>
                    </span>
                    <span class="text-slate-400">Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono text-[10px]">Enter</kbd> untuk masuk</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Interactive Scripts -->
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('icon-eye-open');
            const eyeClosed = document.getElementById('icon-eye-closed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }

        // Form Submit Loading State
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            const submitText = document.getElementById('btn-submit-text');
            const submitLoading = document.getElementById('btn-submit-loading');

            btn.disabled = true;
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            submitLoading.classList.add('flex');
        });
    </script>
</x-guest-layout>
