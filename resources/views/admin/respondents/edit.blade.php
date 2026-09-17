<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Akses Role Responden SML') }}
            </h2>
            <a href="{{ route('admin.respondents.index') }}" class="text-gray-600 hover:text-gray-900 font-medium text-sm">
                &larr; Kembali ke Daftar Responden
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <!-- Informasi User -->
                    <div class="mb-6 p-4 bg-gray-50 border rounded-md">
                        <h3 class="text-lg font-bold text-gray-900">{{ $user->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            <strong>Role Saat Ini:</strong>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @forelse($userRoles as $ur)
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-medium">{{ $ur }}</span>
                                @empty
                                    <span class="italic text-gray-400">Belum ada role</span>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Form Pengaturan Multi-Role SML -->
                    <form action="{{ route('admin.respondents.update', $user) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-900 mb-2">Tentukan Role / Tipe Responden Kepdirjen SML</h4>
                            <p class="text-sm text-gray-600 mb-4">
                                Anda dapat mencentang <strong>1 jenis role atau lebih</strong> yang dapat diakses/diwakili oleh user ini. User akan secara otomatis dapat mengakses survei yang menargetkan salah satu dari role yang dicentang.
                            </p>

                            <div class="space-y-3 border rounded-md p-4 bg-gray-50 max-h-96 overflow-y-auto">
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
                                            <span class="block font-semibold text-gray-800 text-sm">{{ $role->name }}</span>
                                            <span class="block text-xs text-gray-500">
                                                @if($role->name === 'Responden')
                                                    Role dasar responden sistem (diberikan secara otomatis jika memilih tipe SML).
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

                        <div class="flex items-center justify-end gap-3 mt-6">
                            <a href="{{ route('admin.respondents.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md text-sm font-medium transition-colors">
                                Batal
                            </a>
                            <x-primary-button class="px-6 py-2 text-sm">
                                {{ __('Simpan Hak Akses Role') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
