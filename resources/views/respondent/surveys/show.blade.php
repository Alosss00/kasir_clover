<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $survey->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <p class="text-lg">Silakan isi form di bawah ini dengan sebaik-baiknya.</p>
                </div>
            </div>

            <form action="{{ route('respondent.surveys.submit', $survey) }}" method="POST">
                @csrf
                
                @foreach($survey->questions as $index => $question)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-gray-900 border-l-4 border-indigo-500">
                            <label class="block font-medium text-lg text-gray-700 mb-4">
                                {{ $index + 1 }}. {{ $question->question_text }}
                            </label>

                            <div class="mt-2">
                                @switch($question->type)
                                    @case('text')
                                        <input type="text" name="answers[{{ $question->id }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required placeholder="Tuliskan jawaban Anda di sini">
                                        @break

                                    @case('radio')
                                        @if($question->options)
                                            <div class="space-y-2">
                                                @foreach($question->options as $option)
                                                    <div class="flex items-center">
                                                        <input type="radio" id="q_{{ $question->id }}_{{ $loop->index }}" name="answers[{{ $question->id }}]" value="{{ $option }}" class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" required>
                                                        <label for="q_{{ $question->id }}_{{ $loop->index }}" class="ml-2 block text-sm text-gray-900">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @break

                                    @case('checkbox')
                                        @if($question->options)
                                            <div class="space-y-2">
                                                @foreach($question->options as $option)
                                                    <div class="flex items-center">
                                                        <!-- Tambahkan [] pada name untuk checkbox agar terbaca sebagai array di Controller -->
                                                        <input type="checkbox" id="q_{{ $question->id }}_{{ $loop->index }}" name="answers[{{ $question->id }}][]" value="{{ $option }}" class="w-4 h-4 rounded text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                        <label for="q_{{ $question->id }}_{{ $loop->index }}" class="ml-2 block text-sm text-gray-900">
                                                            {{ $option }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @break
                                @endswitch
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex items-center justify-end mb-12">
                    <x-primary-button class="px-8 py-3 text-lg">
                        {{ __('Submit Jawaban') }}
                    </x-primary-button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
