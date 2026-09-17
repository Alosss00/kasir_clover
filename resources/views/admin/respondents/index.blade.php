<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Akses User Responden (Tipe SML)') }}
            </h2>
            <a href="{{ route('admin.surveys.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm py-2 px-4 rounded-md transition-colors">
                + Buat Survei Baru
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showNewRoleForm: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Flash Alerts -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <!-- Banner Tambah Tipe Responden Baru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Tipe Responden Kepdirjen SML</h3>
                        <p class="text-sm text-gray-600">Daftar kategori/peran responden yang tersedia dalam sistem.</p>
                    </div>
                    <button @click="showNewRoleForm = !showNewRoleForm" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded border transition-colors">
                        <span x-text="showNewRoleForm ? 'Tutup Form' : '+ Tambah Tipe Responden Baru'"></span>
                    </button>
                </div>

                <!-- List Tipe Responden (Badges) -->
                <div class="flex flex-wrap gap-2 mt-4">
                    @foreach($roles as $r)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                            🏷️ {{ $r->name }}
                        </span>
                    @endforeach
                </div>

                <!-- Form Tambah Tipe Responden Baru (Modal / Expandable) -->
                <div x-show="showNewRoleForm" x-transition class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                    <h4 class="font-medium text-gray-900 mb-2">Tambah Tipe Responden SML Baru</h4>
                    <form action="{{ route('admin.respondents.roles.store') }}" method="POST" class="flex gap-4 items-center">
                        @csrf
                        <input type="text" name="name" placeholder="Contoh: Responden - Penguji UTTP" class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm" required>
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-sm py-2 px-4 rounded-md transition-colors">
                            Simpan Tipe Role
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filter & Searching -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('admin.respondents.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="search" value="Cari Nama / Email" />
                        <x-text-input id="search" name="search" type="text" value="{{ request('search') }}" placeholder="Ketik nama atau email..." class="mt-1 block w-full text-sm" />
                    </div>
                    <div>
                        <x-input-label for="role" value="Filter Berdasarkan Tipe SML" />
                        <select id="role" name="role" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <option value="">-- Semua Role SML --</option>
                            @foreach($roles as $r)
                                <option value="{{ $r->name }}" {{ request('role') == $r->name ? 'selected' : '' }}>{{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm py-2 px-4 rounded-md transition-colors w-full">
                            Terapkan Filter
                        </button>
                        @if(request()->hasAny(['search', 'role']))
                            <a href="{{ route('admin.respondents.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium text-sm py-2 px-3 rounded-md transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Tabel User Responden -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Daftar Pengguna Responden ({{ $users->total() }})</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe Responden / Role SML</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                            {{ $user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {{ $user->email }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                @forelse($user->roles as $uRole)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $uRole->name }}
                                                    </span>
                                                @empty
                                                    <span class="text-xs text-gray-400 italic">Belum ada role SML</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.respondents.edit', $user) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md font-medium text-xs border border-indigo-200 transition-colors">
                                                ✏️ Kelola Akses Role
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">
                                            Tidak ada user responden ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
