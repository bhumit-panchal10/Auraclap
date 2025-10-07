<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamDetail;
use App\Models\Categories;
use App\Models\ExamMaster;
use App\Models\ExamResult;
use App\Models\ManageExamUser;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{

    public function logout(Request $request)
    {
        Auth::logout(); // Logs out the user
        $request->session()->invalidate(); // Clears session data
        $request->session()->regenerateToken(); // Prevents CSRF attacks

        return redirect()->route('userlogin'); // Redirect to the exam result page
    }
    public function showResults($exam_id, $user_id)
    {
        $examResults = ExamResult::where('Exam_id', $exam_id)
            ->where('Exam_user_id', $user_id)
            ->with('examDetail')
            ->get();
        $totalQuestions = $examResults->count(); // Total attempted questions
        $correctAnswers = $examResults->filter(function ($result) {
            return $result->Answer == optional($result->examDetail)->correct_answer;
        })->count();

        return view('Exam.result', compact('examResults', 'totalQuestions', 'correctAnswers'));
    }



    public function index()
    {
        $user = session('exam_user');
        $examres = ExamResult::where(['Exam_id' => $user['exam_id'], 'Exam_User_id' => $user['Exam_User_id']])->first();
        if (!$user) {
            return redirect()->route('userlogin')->withErrors(['message' => 'Please log in first']);
        }

        return view('Exam.index', compact('user', 'examres'));
    }


    public function showQuestion($examid, $examdetailid = null)
    {
        $examdetailid = (int) $examdetailid;

        if ($examdetailid === 0) {
            $question = ExamDetail::with('exam')->where('exam_id', $examid)->orderBy('Exam_details_id', 'asc')->first();
        } else {
            $question = ExamDetail::with('exam')
                ->where('Exam_details_id', $examdetailid)
                ->first();
        }


        if (!$question) {
            return redirect()->route('manage_exams_user.index');
        }

        $totalQuestions = ExamDetail::count();
        $next = ExamDetail::with('exam')
            ->where('exam_id', $examid)
            ->where('Exam_details_id', '>', $question->Exam_details_id)
            ->orderBy('Exam_details_id', 'asc')
            ->first();
        $previous = ExamDetail::with('exam')
            ->where('exam_id', $examid)
            ->where('Exam_details_id', '<', $question->Exam_details_id)
            ->orderBy('Exam_details_id', 'desc')
            ->first();

        $examUserId = session('exam_user.Exam_User_id');
        $userExam = ManageExamUser::where('exam_id', $question->exam_id)
            ->where('Exam_User_id', $examUserId)
            ->first();
        $ExamResult = ExamResult::where('exam_id', $question->exam_id)
            ->when(!empty($question->Exam_details_id), function ($query) use ($question, $examUserId) {
                return $query->where('exam_detail_id', $question->Exam_details_id)
                    ->where('Exam_User_id', $examUserId);
            })
            ->first();
        //dd($ExamResult);

        $examUserid = $userExam->Exam_User_id ?? null;


        // Determine if it's the last question
        $isLastQuestion = !$next;

        return view('Exam.exam', compact('next', 'previous', 'ExamResult', 'question', 'totalQuestions', 'examUserid', 'isLastQuestion'));
    }




    public function nextQuestion(Request $request)
    {

        $examDetailId = $request->input('exam_detail_id');


        $exam_id = $request->input('exam_id');
        $Exam_User_id = $request->input('Exam_User_id');
        $selectedAnswer = $request->input('answer');

        $question = ExamDetail::where('Exam_details_id', $examDetailId)->first();
        $answerLetter = null;
        if ($selectedAnswer === $question->option_1) {
            $answerLetter = 'A';
        } elseif ($selectedAnswer === $question->option_2) {
            $answerLetter = 'B';
        } elseif ($selectedAnswer === $question->option_3) {
            $answerLetter = 'C';
        } elseif ($selectedAnswer === $question->option_4) {
            $answerLetter = 'D';
        }
        if ($request->Exam_Result_id) {

            $data =  [
                'Exam_id' => $exam_id,
                'Answer' => $answerLetter,
                'Answer_in_option' => $selectedAnswer,
                'Exam_detail_id' => $examDetailId,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];



            ExamResult::where('Exam_Result_id', $request->Exam_Result_id)->update($data);
        } else {

            ExamResult::create(
                [
                    'Exam_user_id' => $Exam_User_id,
                    'Exam_id' => $exam_id,
                    'Answer' => $answerLetter,
                    'Answer_in_option' => $selectedAnswer,
                    'Exam_detail_id' => $examDetailId,
                    'created_at' => now(),
                    'strIP' => $request->ip(),
                ]
            );
        }
        $next = ExamDetail::with('exam')->where('Exam_details_id', '>', $examDetailId)->orderBy('Exam_details_id', 'asc')->first();

        if ($next) {
            return redirect()->route('exam.question', [$exam_id, $next->Exam_details_id]);
        } else {
            return redirect()->route('manage_exams_user.index'); // Redirect to manage exams user if no next question
        }
    }

    public function finalSubmit(Request $request)
    {
        //dd($request);
        $request->validate([
            'exam_id' => 'required',
            'Exam_User_id' => 'required',
            'answer' => 'required'
        ]);

        $examDetailId = $request->input('exam_detail_id');

        $exam_id = $request->input('exam_id');

        $Exam_User_id = $request->input('Exam_User_id');

        $selectedAnswer = $request->input('answer');

        $question = ExamDetail::where('Exam_details_id', $examDetailId)->first();
        $answerLetter = null;
        if ($selectedAnswer === $question->option_1) {
            $answerLetter = 'A';
        } elseif ($selectedAnswer === $question->option_2) {
            $answerLetter = 'B';
        } elseif ($selectedAnswer === $question->option_3) {
            $answerLetter = 'C';
        } elseif ($selectedAnswer === $question->option_4) {
            $answerLetter = 'D';
        }

        if ($request->Exam_Result_id) {

            $data =  [
                'Exam_id' => $exam_id,
                'Answer' => $answerLetter,
                'Answer_in_option' => $selectedAnswer,
                'Exam_detail_id' => $examDetailId,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];



            ExamResult::where('Exam_Result_id', $request->Exam_Result_id)->update($data);
        } else {

            ExamResult::create(
                [
                    'Exam_user_id' => $Exam_User_id,
                    'Exam_id' => $exam_id,
                    'Answer' => $answerLetter,
                    'Answer_in_option' => $selectedAnswer,
                    'Exam_detail_id' => $examDetailId,
                    'created_at' => now(),
                    'strIP' => $request->ip(),
                ]
            );
        }

        // Mark the exam as completed (if applicable)
        ManageExamUser::where('exam_id', $exam_id)
            ->where('Exam_User_id', $Exam_User_id)
            ->update(['status' => 'completed']);

        // Redirect to the manage user list page with a success message
        return redirect()->route('manage_exams_user.index')->with('success', 'Exam submitted successfully! You cannot change your answers now.');
    }
}
