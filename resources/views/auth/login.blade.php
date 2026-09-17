<x-guest-layout>
    <div class="mb-5">
        <h2 class="text-lg font-bold text-slate-900">Masuk Akun</h2>
        <p class="text-xs text-slate-500 mt-0.5">Pilih akun instan atau ketik email & password Anda.</p>
    </div>

    <!-- Tombol Isi Instan Cepat -->
    <div class="grid grid-cols-2 gap-2.5 mb-5">
        <button 
            type="button" 
            onclick="document.getElementById('email').value='kasir@clover.com'; document.getElementById('password').value='password';"
            class="p-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-800 text-xs font-bold text-center transition-all cursor-pointer"
        >
            ☕ Isi Akun Kasir
        </button>
        <button 
            type="button" 
            onclick="document.getElementById('email').value='admin@clover.com'; document.getElementById('password').value='password';"
            class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-800 text-xs font-bold text-center transition-all cursor-pointer"
        >
            👑 Isi Akun Admin
        </button>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Email</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email', 'kasir@clover.com') }}" 
                required 
                autofocus 
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-emerald-600 outline-none"
            >
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                value="password"
                required 
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs focus:bg-white focus:border-emerald-600 outline-none"
            >
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded text-emerald-600" name="remember" checked>
                <span class="ms-2 text-xs text-slate-600">Ingat Saya</span>
            </label>
        </div>

        <!-- Submit Button -->
        <div class="pt-2">
            <button 
                type="submit" 
                class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer"
            >
                <span>MASUK APLIKASI</span>
                <span>→</span>
            </button>
        </div>
    </form>
</x-guest-layout>
