<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Rekapitulasi: {{ $survey->title }}
            </h2>
            <a href="{{ route('admin.surveys.create') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Buat Survei Baru</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Cards Statistik Utama -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Target Populasi</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ $targetPopulation }} <span class="text-lg text-gray-500 font-normal">User</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        <strong>Role Target SML:</strong>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @forelse($targetRoles as $tRole)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $tRole }}
                                </span>
                            @empty
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Semua Role / Publik
                                </span>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-emerald-500">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Respons Masuk</div>
                    <div class="mt-2 text-3xl font-semibold text-gray-900">
                        {{ $totalResponses }} <span class="text-lg text-gray-500 font-normal">Jawaban</span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Persentase Partisipasi</div>
                    <div class="mt-2 text-3xl font-semibold {{ $responsePercentage >= 50 ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $responsePercentage }}%
                    </div>
                </div>
            </div>

            <!-- Analisis Per Pertanyaan -->
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Analisis Jawaban</h3>
            
            <div class="space-y-6">
                @foreach($survey->questions as $index => $question)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h4 class="font-medium text-lg text-gray-900 mb-4">{{ $index + 1 }}. {{ $question->question_text }}</h4>
                        
                        @if($question->type === 'text')
                            <!-- List Teks -->
                            <div class="bg-gray-50 rounded-md p-4 max-h-64 overflow-y-auto border border-gray-200">
                                <ul class="list-disc pl-5 space-y-2 text-gray-700">
                                    @forelse($question->answers as $answer)
                                        @if(!empty(trim($answer->answer_value)))
                                            <li>{{ $answer->answer_value }}</li>
                                        @endif
                                    @empty
                                        <li class="text-gray-500 italic">Belum ada jawaban</li>
                                    @endforelse
                                </ul>
                            </div>
                        @else
                            <!-- Grafik Chart.js -->
                            <div class="w-full mx-auto" style="max-height: 400px; display: flex; justify-content: center;">
                                <canvas id="chart_q{{ $question->id }}"></canvas>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <!-- Chart.js (Offline Ready) -->
    <script src="{{ asset('vendor/chartjs/chart.umd.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartData = @json($chartData);
            
            for (const [questionId, data] of Object.entries(chartData)) {
                const ctx = document.getElementById('chart_q' + questionId);
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar', // Bisa diganti 'pie' atau 'doughnut' jika diinginkan
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Jumlah Responden',
                                data: data.data,
                                backgroundColor: [
                                    'rgba(99, 102, 241, 0.8)', // Indigo
                                    'rgba(16, 185, 129, 0.8)', // Emerald
                                    'rgba(245, 158, 11, 0.8)', // Amber
                                    'rgba(239, 68, 68, 0.8)',  // Red
                                    'rgba(59, 130, 246, 0.8)', // Blue
                                    'rgba(139, 92, 246, 0.8)'  // Purple
                                ],
                                borderWidth: 0,
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false // Sembunyikan legend untuk bar chart tunggal
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    </script>
</x-app-layout>
