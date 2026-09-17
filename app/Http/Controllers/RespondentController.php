<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RespondentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRoleNames = $user->roles->pluck('name')->toArray();
        
        // Ambil survei aktif, lalu filter berdasarkan role yang dimiliki oleh user
        $allActiveSurveys = Survey::where('is_active', true)->latest()->get();

        $surveys = $allActiveSurveys->filter(function ($survey) use ($userRoleNames) {
            $targetRoles = $survey->target_roles ?? [];
            if (empty($targetRoles) && !empty($survey->target_role)) {
                $targetRoles = array_map('trim', explode(',', $survey->target_role));
            }

            // Jika tidak ada target role spesifik, survei berlaku untuk seluruh responden
            if (empty($targetRoles)) {
                return true;
            }

            // Memeriksa apakah ada setidaknya satu role user yang cocok dengan target role survei
            return count(array_intersect($targetRoles, $userRoleNames)) > 0;
        })->values();
            
        // Ambil ID survei yang sudah diisi oleh user
        $submittedSurveyIds = Response::where('user_id', $user->id)
            ->pluck('survey_id')
            ->toArray();

        return view('respondent.surveys.index', compact('surveys', 'submittedSurveyIds'));
    }

    public function show(Survey $survey)
    {
        // Cek apakah user sudah mensubmit
        $hasSubmitted = Response::where('survey_id', $survey->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($hasSubmitted) {
            return redirect()->route('respondent.surveys.index')
                ->with('error', 'Anda sudah mengisi survei ini sebelumnya.');
        }

        // Cek apakah aktif
        if (!$survey->is_active) {
            return redirect()->route('respondent.surveys.index')
                ->with('error', 'Survei ini sudah tidak aktif.');
        }

        $survey->load('questions');

        return view('respondent.surveys.show', compact('survey'));
    }

    public function submit(Request $request, Survey $survey)
    {
        // Cek lagi apakah user sudah mengisi untuk mencegah multiple submit
        $hasSubmitted = Response::where('survey_id', $survey->id)
            ->where('user_id', Auth::id())
            ->exists();

        if ($hasSubmitted) {
            return redirect()->route('respondent.surveys.index')
                ->with('error', 'Anda sudah mengisi survei ini sebelumnya.');
        }

        $request->validate([
            'answers' => 'required|array',
        ]);

        try {
            DB::transaction(function () use ($request, $survey) {
                // Buat Response parent
                $response = Response::create([
                    'survey_id' => $survey->id,
                    'user_id' => Auth::id(),
                ]);

                // Simpan Jawaban
                foreach ($request->answers as $questionId => $answerValue) {
                    // Jika bertipe checkbox, hasil berupa array
                    if (is_array($answerValue)) {
                        $answerValue = implode(', ', $answerValue);
                    }

                    $response->answers()->create([
                        'question_id' => $questionId,
                        'answer_value' => $answerValue,
                    ]);
                }
            });

            return redirect()->route('respondent.surveys.index')
                ->with('success', 'Terima kasih telah mengisi survei ini!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
