<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit User: {{ $user->name }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                &larr; Kembali ke Daftar User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Informasi Utama -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi & Status Pengguna</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="email" :value="__('Alamat Email')" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak diubah)')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="••••••••" />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>

                                <div class="flex items-center mt-6">
                                    <label class="flex items-center space-x-2 cursor-pointer p-3 bg-gray-50 border rounded-md w-full">
                                        <input 
                                            type="checkbox" 
                                            name="is_active" 
                                            value="1" 
                                            class="w-5 h-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                            {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                            {{ $user->id === Auth::id() ? 'disabled' : '' }}
                                        >
                                        <div>
                                            <span class="font-semibold text-gray-800 text-sm">Status Akun Aktif</span>
                                            <span class="block text-xs text-gray-500">Jika di-uncheck (nonaktif), user tidak akan bisa login ke sistem.</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="mb-6">

                        <!-- Pilihan Roles / Hak Akses SML -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Tentukan Role / Peran User</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Centang <strong>1 jenis role atau lebih</strong> untuk menetapkan peran pengguna ini:
                            </p>

                            <div class="space-y-3 border rounded-md p-4 bg-gray-50 max-h-60 overflow-y-auto">
                                @foreach($roles as $role)
                                    <label class="flex items-start p-3 bg-white border rounded-md hover:bg-indigo-50 cursor-pointer transition-colors shadow-sm">
                                        <input 
                                            type="checkbox" 
                                            name="roles[]" 
                                            value="{{ $role->name }}" 
                                            class="mt-1 w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            {{ in_array($role->name, $userRoles) ? 'checked' : '' }}
                                        >
                                        <div class="ml-3">
                                            <span class="block font-semibold text-gray-800 text-sm">
                                                {{ $role->name }}
                                                @if($role->name === 'Admin')
                                                    <span class="ml-2 px-2 py-0.5 text-xs bg-purple-100 text-purple-800 font-bold rounded">Akses Administrator</span>
                                                @endif
                                            </span>
                                            <span class="block text-xs text-gray-500">
                                                @if($role->name === 'Admin')
                                                    Dapat mengelola survei, pengguna, dan hak akses sistem.
                                                @elseif($role->name === 'Responden')
                                                    Role dasar responden untuk pengisian survei.
                                                @else
                                                    Kategori Tipe Responden berdasarkan Kepdirjen SML.
                                                @endif
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('roles')" />
                        </div>

                        <!-- Pilihan Direct Permissions / Hak Akses Spesifik Fitur -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Kontrol Hak Akses Fitur Spesifik (Direct Permissions)</h3>
                            <p class="text-sm text-gray-600 mb-4">
                                Berikan izin fitur secara langsung di luar role yang dimiliki:
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border rounded-md p-4 bg-gray-50">
                                @foreach($permissions as $perm)
                                    <label class="flex items-center space-x-3 p-3 bg-white border rounded-md hover:bg-emerald-50 cursor-pointer transition-colors shadow-sm">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $perm->name }}" 
                                            class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500"
                                            {{ in_array($perm->name, $userPermissions) ? 'checked' : '' }}
                                        >
                                        <div>
                                            <span class="block font-semibold text-gray-800 text-sm">🔑 {{ $perm->name }}</span>
                                            <span class="block text-xs text-gray-500">Izin fitur {{ $perm->name }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('permissions')" />
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-6">
                            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium transition-colors">
                                Batal
                            </a>
                            <x-primary-button class="px-6 py-2 text-sm">
                                {{ __('Simpan Perubahan User') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
