<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class SurveyController extends Controller
{
    public function create()
    {
        $roles = Role::where('name', '!=', 'Admin')->get();
        return view('admin.surveys.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'target_roles' => 'nullable|array',
            'target_roles.*' => 'string',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:text,radio,checkbox',
            'questions.*.options' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $targetRoles = $request->target_roles ?? [];

                $survey = Survey::create([
                    'title' => $request->title,
                    'target_roles' => $targetRoles,
                    'target_role' => !empty($targetRoles) ? implode(', ', $targetRoles) : null,
                    'is_active' => true,
                ]);

                foreach ($request->questions as $questionData) {
                    $options = null;
                    if (in_array($questionData['type'], ['radio', 'checkbox']) && !empty($questionData['options'])) {
                        // Split by comma and trim whitespace
                        $options = array_map('trim', explode(',', $questionData['options']));
                        $options = array_filter($options); // Remove empty options
                    }

                    $survey->questions()->create([
                        'question_text' => $questionData['question_text'],
                        'type' => $questionData['type'],
                        'options' => empty($options) ? null : array_values($options),
                    ]);
                }
            });

            return redirect()->route('admin.surveys.create')->with('success', 'Survei berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat survei: ' . $e->getMessage());
        }
    }

    public function show(Survey $survey)
    {
        // 1. Hitung Populasi Target
        $targetRoles = $survey->target_roles;
        if (empty($targetRoles) && !empty($survey->target_role)) {
            $targetRoles = array_map('trim', explode(',', $survey->target_role));
        }

        if (empty($targetRoles)) {
            // Target seluruh user responden (kecuali admin)
            $targetPopulation = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            })->count();
        } else {
            $targetPopulation = User::role($targetRoles)->count();
        }

        // 2. Hitung Respons yang masuk
        $totalResponses = $survey->responses()->count();
        $responsePercentage = $targetPopulation > 0 ? round(($totalResponses / $targetPopulation) * 100, 2) : 0;

        // 3. Agregasi Jawaban
        $survey->load('questions.answers');
        
        $chartData = [];
        
        foreach ($survey->questions as $question) {
            if (in_array($question->type, ['radio', 'checkbox']) && $question->options) {
                // Initialize counts
                $counts = [];
                foreach ($question->options as $option) {
                    $counts[$option] = 0;
                }

                // Count answers
                foreach ($question->answers as $answer) {
                    if ($question->type === 'checkbox') {
                        // Checkbox can have multiple comma-separated values
                        $selectedOptions = explode(', ', $answer->answer_value);
                        foreach ($selectedOptions as $opt) {
                            $opt = trim($opt);
                            if (isset($counts[$opt])) {
                                $counts[$opt]++;
                            }
                        }
                    } else {
                        // Radio
                        if (isset($counts[$answer->answer_value])) {
                            $counts[$answer->answer_value]++;
                        }
                    }
                }

                // Format for Chart.js
                $chartData[$question->id] = [
                    'labels' => array_keys($counts),
                    'data' => array_values($counts),
                ];
            }
        }

        return view('admin.surveys.show', compact('survey', 'targetPopulation', 'totalResponses', 'responsePercentage', 'chartData', 'targetRoles'));
    }
}
