<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Survei') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Survei Tersedia</h3>
                    
                    @if($surveys->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($surveys as $survey)
                                @php
                                    $hasSubmitted = in_array($survey->id, $submittedSurveyIds);
                                @endphp
                                <div class="border rounded-lg p-6 {{ $hasSubmitted ? 'bg-gray-100' : 'bg-white shadow-sm hover:shadow-md transition-shadow' }}">
                                    <h4 class="text-xl font-bold mb-2">{{ $survey->title }}</h4>
                                    
                                    @if($hasSubmitted)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mb-4">
                                            Selesai Dikerjakan
                                        </span>
                                        <p class="text-sm text-gray-500 mb-4">Anda sudah mengisi survei ini, terima kasih atas partisipasinya.</p>
                                        <button disabled class="w-full bg-gray-300 text-gray-500 font-bold py-2 px-4 rounded cursor-not-allowed">
                                            Sudah Diisi
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-4">
                                            Belum Dikerjakan
                                        </span>
                                        <p class="text-sm text-gray-600 mb-4">Silakan isi survei ini dengan menekan tombol di bawah.</p>
                                        <a href="{{ route('respondent.surveys.show', $survey) }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-colors">
                                            Isi Survei
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500 italic">
                            Belum ada survei aktif yang tersedia untuk Anda saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
