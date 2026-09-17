<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600 mb-4">Anda login sebagai: 
                        @foreach(Auth::user()->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 mr-1">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </p>

                    @hasrole('Admin')
                        <div class="mt-6 border-t pt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-5 border rounded-lg bg-indigo-50 border-indigo-200">
                                <h4 class="font-bold text-indigo-900 text-lg mb-2">📋 Manajemen Survei</h4>
                                <p class="text-sm text-indigo-700 mb-4">Buat survei baru dan targetkan ke role/tipe responden Kepdirjen SML tertentu.</p>
                                <a href="{{ route('admin.surveys.create') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-2 px-4 rounded transition-colors">
                                    + Buat Survei Baru
                                </a>
                            </div>

                            <div class="p-5 border rounded-lg bg-emerald-50 border-emerald-200">
                                <h4 class="font-bold text-emerald-900 text-lg mb-2">👥 Kelola User & Hak Akses Role</h4>
                                <p class="text-sm text-emerald-700 mb-4">Tambah pengguna baru, edit data user, aktif/nonaktifkan akun, tentukan role pengguna (Admin/SML), dan reset password.</p>
                                <div class="flex flex-wrap gap-2">
                                    <a href="{{ route('admin.users.create') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold py-2 px-4 rounded transition-colors shadow-sm">
                                        + Tambah User Baru
                                    </a>
                                    <a href="{{ route('admin.users.index') }}" class="inline-block bg-white hover:bg-emerald-100 text-emerald-800 text-sm font-semibold py-2 px-4 rounded border border-emerald-300 transition-colors">
                                        Daftar Pengguna &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endhasrole

                    @hasrole('Responden')
                        <div class="mt-6 border-t pt-6">
                            <div class="p-5 border rounded-lg bg-blue-50 border-blue-200">
                                <h4 class="font-bold text-blue-900 text-lg mb-2">📝 Survei SML</h4>
                                <p class="text-sm text-blue-700 mb-4">Lihat dan isi survei yang tersedia sesuai dengan tipe responden Anda.</p>
                                <a href="{{ route('respondent.surveys.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2 px-4 rounded transition-colors">
                                    Lihat Daftar Survei &rarr;
                                </a>
                            </div>
                        </div>
                    @endhasrole
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
