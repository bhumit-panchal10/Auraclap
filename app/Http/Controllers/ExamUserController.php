<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamDetail;
use App\Models\Categories;
use App\Models\ExamMaster;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class ExamUserController extends Controller
{
    public function technicialexamregister(Request $request)
    {
        $categories = Categories::orderBy('Category_name', 'asc')
            ->select('Categories_id', 'Category_name')
            ->where(['iStatus' => 1])
            ->get();

        try {
            return view('TechnicialExam', compact('categories'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function index()
    {
        try {

            $exams = ExamMaster::with('category')->orderBy('Exam_master_id', 'desc')
                ->paginate(config('app.per_page'));

            // dd($rates);

            return view('ExamMaster.index', compact('exams'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function questionpaperlist($id)
    {
        try {

            $question_ans = ExamDetail::with('exam')->where('exam_id',$id)->orderBy('Exam_details_id', 'desc')
                ->paginate(config('app.per_page'));
            return view('ExamDetail.index', compact('question_ans', 'id'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {

            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();

            return view('ExamMaster.add', compact('categories'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function questionadd($id)
    {
        try {
            return view('ExamDetail.addquestion', compact('id'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function questionstore(Request $request, $id)
    {
        $request->validate([
            'question' => 'required|string',
            'options' => 'required|array|min:4|max:4', // Ensure exactly 4 options
            'options.*' => 'required|string',
            'correct_answer' => 'required',
        ]);

        ExamDetail::create([
            'question' => $request->question,
            'option_1' => $request->options[0],
            'option_2' => $request->options[1],
            'option_3' => $request->options[2],
            'option_4' => $request->options[3],
            'correct_answer' => $request->correct_answer,
            'exam_id' => $request->exam_detail_id,
            'strIP' => $request->ip(),

        ]);

        return redirect()->route('exam_master.questionpaperlist', $id)->with('success', 'Question added successfully!');
    }

    public function questionedit(Request $request, $id, $examid)
    {

        try {

            $questionans = ExamDetail::where('Exam_details_id', $id)->first();


            return view('ExamDetail.edit', compact('questionans', 'examid'));
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function questionupdate(Request $request)
    {

        DB::beginTransaction();

        try {


            $data = [


                'Exam_details_id' => $request->Exam_details_id,
                'question' => $request->question,
                'option_1' => $request->option_1,
                'option_2' => $request->option_2,
                'option_3' => $request->option_3,
                'option_4' => $request->option_4,
                'correct_answer' => $request->correct_answer,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            ExamDetail::where("Exam_details_id", "=", $request->Exam_details_id)->update($data);
            DB::commit();

            Toastr::success('Question Answer updated successfully :)', 'Success');
            return redirect()->route('exam_master.questionpaperlist', $request->examid)->with('success', 'Question Answer Update Successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errorMessageString = implode(', ', Arr::flatten($e->errors()));
            Toastr::error($errorMessageString, 'Error');
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function store(Request $request)
    {

        DB::beginTransaction();


        try {
            // Validate input data
            $request->validate([
                'Categoryid' => 'required',
            ]);


            // Create a new Managerate record
            ExamMaster::create([
                'cat_id' => $request->Categoryid,
                'Exam_name' => $request->Exam_name,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            // Commit the transaction
            DB::commit();

            Toastr::success('Rate created successfully!', 'Success');
            return redirect()->route('exam_master.index')->with('success', 'Rate Created Successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->errors();  // Get the validation errors
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }

            $errorMessageString = implode(', ', $errorMessages);
            Toastr::error($errorMessageString, 'Error');
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Failed to create Rate: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }


    public function edit(Request $request, $id)
    {

        try {

            $exams = ExamMaster::where('Exam_master_id', $id)->first();
            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();

            return view('ExamMaster.edit', compact('exams', 'categories'));
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {
            $request->validate([
                'Categoryid' => 'required',
            ]);

            $data = [

                'Exam_master_id' => $request->Exam_master_id,
                'cat_id' => $request->Categoryid,
                'Exam_name' => $request->Exam_name,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            ExamMaster::where("Exam_master_id", "=", $request->Exam_master_id)->update($data);
            DB::commit();

            Toastr::success('Exam updated successfully :)', 'Success');
            return redirect()->route('exam_master.index')->with('success', 'Exam Update Successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errorMessageString = implode(', ', Arr::flatten($e->errors()));
            Toastr::error($errorMessageString, 'Error');
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function delete(Request $request)
    {

        DB::beginTransaction();

        try {
            $exam = ExamMaster::where([

                'isDelete' => 0,
                'Exam_master_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Exam deleted successfully :)', 'Success');
            return response()->json(['success' => true]);
            //return back();
        } catch (ValidationException $e) {
            DB::rollBack();
            Toastr::error('Validation Error: ' . implode(', ', $e->errors()));
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function questiondelete(Request $request)
    {

        DB::beginTransaction();

        try {
            $exam = ExamDetail::where([

                'isDelete' => 0,
                'Exam_details_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Question Answer deleted successfully :)', 'Success');
            return response()->json(['success' => true]);
            //return back();
        } catch (ValidationException $e) {
            DB::rollBack();
            Toastr::error('Validation Error: ' . implode(', ', $e->errors()));
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function questiondeleteselected(Request $request)
    {

        try {
            $ids = $request->input('Exam_details_ids', []);
            ExamDetail::whereIn('Exam_details_id', $ids)->delete();

            Toastr::success('Rate deleted successfully :)', 'Success');
            return back();
        } catch (ValidationException $e) {
            DB::rollBack();
            Toastr::error('Validation Error: ' . implode(', ', $e->errors()));
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
