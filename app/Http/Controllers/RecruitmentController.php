<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use App\Models\CategoryOffer;
use App\Models\StateMaster;
use App\Models\CityMaster;
use App\Models\Recruitment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class RecruitmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {

            $Recruitments = Recruitment::orderBy('Recruitment_id', 'desc')
                ->paginate(config('app.per_page'));
            return view('Recruitment.index', compact('Recruitments'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function create()
    {

        return view('Recruitment.add');
    }



    public function store(Request $request)

    {

        DB::beginTransaction();

        try {

            $Recruitment = new Recruitment();

            $Recruitment->job_title = $request->job_title;
            $Recruitment->job_type = $request->job_type;
            $Recruitment->experience = $request->experience;
            $Recruitment->qualification = $request->qualification;
            $Recruitment->location = $request->location;
            $Recruitment->timing = $request->timing;
            $Recruitment->number_of_opening = $request->number_of_opening;
            $Recruitment->salary = $request->salary;


            $Recruitment->created_at = now();


            $Recruitment->strIP = $request->ip();
            $Recruitment->save();


            DB::commit();

            return redirect()->route('Recruitment.index')->with('success', 'Recruitment Created Successfully.');
        } catch (ValidationException $e) {

            DB::rollBack();

            $errors = $e->errors(); // Get the errors array

            $errorMessages = []; // Initialize an array to hold error messages



            // Loop through the errors array and flatten the error messages

            foreach ($errors as $field => $messages) {

                foreach ($messages as $message) {

                    $errorMessages[] = $message;
                }
            }



            // Join all error messages into a single string

            $errorMessageString = implode(', ', $errorMessages);



            Toastr::error($errorMessageString, 'Error');

            return redirect()->back()->withInput();
        } catch (\Throwable $th) {

            DB::rollBack();

            Toastr::error('Failed to create offer: ' . $th->getMessage(), 'Error');

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }



    public function edit(Request $request, $id)

    {
        $data = Recruitment::where('Recruitment_id', $request->id)->first();
        return view('Recruitment.edit', compact('data'));
    }



    public function update(Request $request)

    {

        DB::beginTransaction();



        try {

            $update = DB::table('Recruitment')
                ->where(['iStatus' => 1, 'isDelete' => 0, 'Recruitment_id' => $request->Recruitment_id])
                ->update([
                    'job_title' => $request->job_title,
                    'job_type' => $request->job_type,
                    'experience' => $request->experience,
                    'qualification' => $request->qualification,
                    'location' => $request->location,
                    'timing' => $request->timing,
                    'number_of_opening' => $request->number_of_opening,
                    'salary' => $request->salary,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            DB::commit();
            return redirect()->route('Recruitment.index')->with('success', 'Recruitment Updated Successfully.');
        } catch (ValidationException $e) {

            DB::rollBack();

            $errors = $e->errors(); // Get the errors array

            $errorMessages = []; // Initialize an array to hold error messages



            // Loop through the errors array and flatten the error messages

            foreach ($errors as $field => $messages) {

                foreach ($messages as $message) {

                    $errorMessages[] = $message;
                }
            }



            // Join all error messages into a single string

            $errorMessageString = implode(', ', $errorMessages);



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

            Recruitment::where([

                'iStatus' => 1,

                'isDelete' => 0,

                'Recruitment_id' => $request->id

            ])->delete();



            DB::commit();



            Toastr::success('Recruitment deleted successfully :)', 'Success');

            return response()->json(['success' => true]);

            //return back();

        } catch (ValidationException $e) {

            DB::rollBack();

            Toastr::error(implode(', ', $e->errors()));

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

            $ids = $request->input('Recruitment_ids', []);

            Recruitment::whereIn('Recruitment_id', $ids)->delete();

            Toastr::success('Recruitment deleted successfully :)', 'Success');

            return back();
        } catch (ValidationException $e) {

            DB::rollBack();

            Toastr::error(implode(', ', $e->errors()));

            return redirect()->back()->withInput();
        } catch (\Throwable $th) {

            DB::rollBack();

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }
}
