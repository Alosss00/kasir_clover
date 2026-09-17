<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Survei Baru') }}
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
                    <form action="{{ route('admin.surveys.store') }}" method="POST" x-data="surveyForm()">
                        @csrf
                        
                        <!-- Details Survei -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Survei</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <x-input-label for="title" :value="__('Judul Survei')" />
                                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" placeholder="Masukkan Judul Survei" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                                </div>
                                
                                <div>
                                    <x-input-label :value="__('Target Role / Tipe Responden SML (Kosongkan jika untuk Semua Responden)')" />
                                    <p class="text-xs text-gray-500 mb-2">Pilih 1 atau beberapa Tipe Responden yang menjadi sasaran survei ini:</p>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 p-4 bg-gray-50 border rounded-md">
                                        @foreach($roles as $role)
                                            <label class="flex items-center space-x-2 text-sm text-gray-700 cursor-pointer bg-white p-2 border rounded hover:bg-indigo-50 transition-colors">
                                                <input type="checkbox" name="target_roles[]" value="{{ $role->name }}" class="rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="font-medium">{{ $role->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <x-input-error class="mt-2" :messages="$errors->get('target_roles')" />
                                </div>
                            </div>
                        </div>

                        <hr class="mb-6">

                        <!-- Questions Builder -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Daftar Pertanyaan</h3>
                                <button type="button" @click="addQuestion()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    + Tambah Pertanyaan
                                </button>
                            </div>
                            
                            <x-input-error class="mt-2" :messages="$errors->get('questions')" />

                            <!-- Dynamic Questions List -->
                            <div class="space-y-4">
                                <template x-for="(question, index) in questions" :key="index">
                                    <div class="p-4 border rounded-md bg-gray-50 relative">
                                        <button type="button" @click="removeQuestion(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold" title="Hapus Pertanyaan">&times;</button>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                            <div class="md:col-span-2">
                                                <x-input-label x-bind:for="'q_text_'+index" value="Teks Pertanyaan" />
                                                <input type="text" x-model="question.question_text" x-bind:name="`questions[${index}][question_text]`" x-bind:id="'q_text_'+index" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tuliskan pertanyaan..." required>
                                            </div>
                                            <div>
                                                <x-input-label x-bind:for="'q_type_'+index" value="Tipe Jawaban" />
                                                <select x-model="question.type" x-bind:name="`questions[${index}][type]`" x-bind:id="'q_type_'+index" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                                    <option value="text">Text (Isian Singkat)</option>
                                                    <option value="radio">Radio (Pilih Satu)</option>
                                                    <option value="checkbox">Checkbox (Pilih Banyak)</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Options input only visible for radio/checkbox -->
                                        <div x-show="question.type === 'radio' || question.type === 'checkbox'" x-transition class="mt-2">
                                            <x-input-label x-bind:for="'q_options_'+index" value="Pilihan Jawaban (pisahkan dengan koma)" />
                                            <input type="text" x-model="question.options" x-bind:name="`questions[${index}][options]`" x-bind:id="'q_options_'+index" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Contoh: Sangat Baik, Baik, Cukup, Kurang">
                                            <p class="text-xs text-gray-500 mt-1">Isi opsi pilihan dipisahkan dengan koma jika memilih tipe Radio atau Checkbox.</p>
                                        </div>
                                    </div>
                                </template>
                                
                                <div x-show="questions.length === 0" class="text-center py-4 text-gray-500 italic border-2 border-dashed rounded-md">
                                    Belum ada pertanyaan ditambahkan. Klik tombol "Tambah Pertanyaan" di atas.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Simpan Survey') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('surveyForm', () => ({
                questions: [
                    { question_text: '', type: 'text', options: '' }
                ],
                addQuestion() {
                    this.questions.push({ question_text: '', type: 'text', options: '' });
                },
                removeQuestion(index) {
                    this.questions.splice(index, 1);
                }
            }))
        })
    </script>
</x-app-layout>
