<x-guest-layout>
    <div class="w-full max-w-5xl bg-slate-900/90 border border-slate-800/80 rounded-3xl shadow-2xl shadow-emerald-950/40 overflow-hidden backdrop-blur-xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">
            
            <!-- Left Panel: Branding & POS Showcase -->
            <div class="lg:col-span-5 bg-gradient-to-br from-emerald-950 via-slate-900 to-slate-950 p-8 sm:p-10 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-800/80 relative overflow-hidden">
                <!-- Background decorative flare -->
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-emerald-500/15 rounded-full blur-2xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

                <!-- Top Header / Logo -->
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
                                <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full">POS Pro</span>
                            </h1>
                            <p class="text-xs text-slate-400 font-medium">Coffee & Eatery Management System</p>
                        </div>
                    </div>

                    <div class="mt-8 space-y-3">
                        <h2 class="text-2xl font-bold text-slate-100 leading-snug">
                            Sistem Kasir Pintar & <br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">
                                Manajemen Resep Kafe
                            </span>
                        </h2>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Kelola pesanan kasir cepat, hitung HPP otomatis per gram bahan baku, dan pantau laba bersih secara real-time.
                        </p>
                    </div>
                </div>

                <!-- Middle: Key Highlights -->
                <div class="relative z-10 my-8 space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/40 border border-slate-700/50 backdrop-blur-sm">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Point of Sales Kilat</div>
                            <div class="text-[11px] text-slate-400">Pilih menu, diskon & hitung kembalian instan</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/40 border border-slate-700/50 backdrop-blur-sm">
                        <div class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/20 flex items-center justify-center text-teal-400 shrink-0">
                            <i data-lucide="coffee" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Auto Deduct Bahan Baku</div>
                            <div class="text-[11px] text-slate-400">Stok biji kopi & susu otomatis berkurang per cup</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-800/40 border border-slate-700/50 backdrop-blur-sm">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0">
                            <i data-lucide="printer" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-200">Cetak Struk & Export Laporan</div>
                            <div class="text-[11px] text-slate-400">Siap cetak printer thermal & rekap file PDF / Excel</div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Status -->
                <div class="relative z-10 pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                    <span class="inline-flex items-center gap-1.5 font-medium text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sistem Siap Digunakan
                    </span>
                    <span class="text-slate-500">&copy; {{ date('Y') }} Clover Team</span>
                </div>
            </div>

            <!-- Right Panel: Login Form -->
            <div class="lg:col-span-7 bg-slate-900 p-8 sm:p-12 flex flex-col justify-between">
                <div>
                    <!-- Form Title -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-extrabold text-white tracking-tight">Masuk Akun</h2>
                            <p class="text-xs text-slate-400 mt-1">Masukkan kredensial Anda atau gunakan tombol instan di bawah.</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-1 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-[11px] text-slate-300">
                            <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-400"></i>
                            <span>Secure Access</span>
                        </div>
                    </div>

                    <!-- Quick Demo Role Switcher -->
                    <div class="mb-6">
                        <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                            Pilihan Akun Cepat (Klik untuk Mengisi):
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <button 
                                type="button" 
                                id="btn-quick-kasir"
                                onclick="fillAccount('kasir@clover.com', 'password', 'kasir')"
                                class="quick-role-btn group text-left p-3 rounded-2xl bg-slate-800/80 hover:bg-emerald-950/40 border border-slate-700/80 hover:border-emerald-500/50 transition-all duration-200 cursor-pointer relative"
                            >
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        ☕
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-slate-200 group-hover:text-emerald-300 transition-colors flex items-center justify-between">
                                            <span>Akun Kasir (POS)</span>
                                            <span id="check-kasir" class="hidden text-emerald-400 text-xs">✓ Aktif</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono truncate">kasir@clover.com</div>
                                    </div>
                                </div>
                            </button>

                            <button 
                                type="button" 
                                id="btn-quick-admin"
                                onclick="fillAccount('admin@clover.com', 'password', 'admin')"
                                class="quick-role-btn group text-left p-3 rounded-2xl bg-slate-800/80 hover:bg-teal-950/40 border border-slate-700/80 hover:border-teal-500/50 transition-all duration-200 cursor-pointer relative"
                            >
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-xl bg-teal-500/10 text-teal-400 border border-teal-500/20 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                        👑
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-slate-200 group-hover:text-teal-300 transition-colors flex items-center justify-between">
                                            <span>Akun Administrator</span>
                                            <span id="check-admin" class="hidden text-teal-400 text-xs">✓ Aktif</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono truncate">admin@clover.com</div>
                                    </div>
                                </div>
                            </button>
                        </div>
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

                    <!-- Form Input -->
                    <form method="POST" action="{{ route('login') }}" id="login-form" class="space-y-4">
                        @csrf

                        <!-- Email Address Input -->
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
                                    value="{{ old('email', 'kasir@clover.com') }}" 
                                    required 
                                    autofocus 
                                    placeholder="nama@clover.com"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl bg-slate-950/70 border border-slate-700/90 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                                >
                            </div>
                        </div>

                        <!-- Password Input with Show/Hide Toggle -->
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
                                    <i data-lucide="key" class="w-4 h-4"></i>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    value="password"
                                    required 
                                    placeholder="Masukkan kata sandi..."
                                    class="w-full pl-10 pr-11 py-3 rounded-xl bg-slate-950/70 border border-slate-700/90 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 transition-all font-medium"
                                >
                                <button 
                                    type="button" 
                                    id="btn-toggle-password"
                                    onclick="togglePasswordVisibility()"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition-colors cursor-pointer"
                                    title="Tampilkan / Sembunyikan Password"
                                >
                                    <i id="icon-eye-open" data-lucide="eye" class="w-4 h-4"></i>
                                    <i id="icon-eye-closed" data-lucide="eye-off" class="w-4 h-4 hidden"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me & Helpers -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input 
                                    id="remember_me" 
                                    type="checkbox" 
                                    name="remember" 
                                    checked 
                                    class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-emerald-500 focus:ring-emerald-500/30 focus:ring-offset-0 focus:ring-1"
                                >
                                <span class="text-xs text-slate-400 font-medium">Ingat saya di perangkat ini</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                id="btn-submit"
                                class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 hover:from-emerald-500 hover:via-emerald-400 hover:to-teal-500 text-white font-bold text-xs tracking-wide shadow-lg shadow-emerald-600/25 hover:shadow-emerald-600/40 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-75 disabled:cursor-not-allowed"
                            >
                                <span id="btn-submit-text" class="flex items-center gap-2">
                                    <span>MASUK KE SISTEM KASIR</span>
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
                <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Enkripsi 256-bit Aktif</span>
                    </span>
                    <span class="text-slate-400">Tekan <kbd class="px-1.5 py-0.5 rounded bg-slate-800 border border-slate-700 text-slate-300 font-mono text-[10px]">Enter</kbd> untuk login</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Client-Side Scripts for High Polish -->
    <script>
        function fillAccount(email, password, role) {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            const checkKasir = document.getElementById('check-kasir');
            const checkAdmin = document.getElementById('check-admin');
            const btnKasir = document.getElementById('btn-quick-kasir');
            const btnAdmin = document.getElementById('btn-quick-admin');

            emailInput.value = email;
            passInput.value = password;

            // Highlight Active Role
            if (role === 'kasir') {
                checkKasir.classList.remove('hidden');
                checkAdmin.classList.add('hidden');
                btnKasir.classList.add('ring-2', 'ring-emerald-500', 'bg-emerald-950/40');
                btnAdmin.classList.remove('ring-2', 'ring-teal-500', 'bg-teal-950/40');
            } else {
                checkAdmin.classList.remove('hidden');
                checkKasir.classList.add('hidden');
                btnAdmin.classList.add('ring-2', 'ring-teal-500', 'bg-teal-950/40');
                btnKasir.classList.remove('ring-2', 'ring-emerald-500', 'bg-emerald-950/40');
            }

            // Animate Focus
            emailInput.focus();
        }

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

        // Initialize active state on load
        window.addEventListener('DOMContentLoaded', () => {
            const currentEmail = document.getElementById('email').value;
            if (currentEmail === 'kasir@clover.com') {
                fillAccount('kasir@clover.com', 'password', 'kasir');
            } else if (currentEmail === 'admin@clover.com') {
                fillAccount('admin@clover.com', 'password', 'admin');
            }
        });
    </script>
</x-guest-layout>
