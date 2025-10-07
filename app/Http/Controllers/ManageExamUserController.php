<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManageExamUser;
use App\Models\Categories;
use App\Models\ExamMaster;
use Brian2694\Toastr\Facades\Toastr;
use Google\Service\Translate\Example;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class ManageExamUserController extends Controller
{

    public function index()
    {
        try {

            $users = ManageExamUser::with('category', 'exam')->orderBy('Exam_User_id', 'desc')
                ->paginate(config('app.per_page'));


            return view('ManageExamUser.index', compact('users'));
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

            $exams = ExamMaster::get();
            return view('ManageExamUser.add', compact('categories', 'exams'));
        } catch (\Throwable $th) {
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
                'name' => 'required',
                'login_id' => 'required',
                'Categoryid' => 'required',
                'examid' => 'required',
            ]);


            // Create a new Managerate record
            ManageExamUser::create([
                'name' => $request->name,
                'login_id' => $request->login_id,
                'cate_id' => $request->Categoryid,
                'exam_id' => $request->examid,
                'password' => Hash::make($request->password),
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            // Commit the transaction
            DB::commit();

            Toastr::success('User created successfully!', 'Success');
            return redirect()->route('manage_exam_user.index')->with('success', 'User Created Successfully.');
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

            $examsuser = ManageExamUser::where('Exam_User_id', $id)->first();

            $exams = ExamMaster::get();
            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();

            return view('ManageExamUser.edit', compact('exams', 'categories', 'examsuser'));
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

                'name' => 'required',
                'login_id' => 'required',
                'Categoryid' => 'required',
                'examid' => 'required',
            ]);

            $data = [

                'Exam_User_id' => $request->Exam_User_id,
                'name' => $request->name,
                'login_id' => $request->login_id,
                'cate_id' => $request->Categoryid,
                'exam_id' => $request->examid,
                'password' => Hash::make($request->password),
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            ManageExamUser::where("Exam_User_id", "=", $request->Exam_User_id)->update($data);
            DB::commit();

            Toastr::success('User updated successfully :)', 'Success');
            return redirect()->route('manage_exam_user.index')->with('success', 'User Update Successfully.');
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
            $exam = ManageExamUser::where([

                'isDelete' => 0,
                'Exam_User_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('User deleted successfully :)', 'Success');
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


    public function deleteselected(Request $request)
    {

        try {
            $ids = $request->input('Exam_User_ids', []);
            ManageExamUser::whereIn('Exam_User_id', $ids)->delete();

            Toastr::success('User deleted successfully :)', 'Success');
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
