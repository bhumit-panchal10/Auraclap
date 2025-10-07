<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StateMaster;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $states = StateMaster::orderBy('stateName', 'asc')->paginate(config('app.per_page'));

            // dd($states);

            return view('state.index', compact('states'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'stateName' => 'required|unique:state-masters,stateName',
            ]);

            // Create a new StateMaster record
            $state = StateMaster::create([
                'stateName' => $request->stateName,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit();

            Toastr::success('State created successfully :)', 'Success');
            return redirect()->route('state.index')->with('success', 'State Created Successfully.');
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
            Toastr::error('Failed to create state: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $state = StateMaster::where('stateId', $request->id)->first();

        return json_encode($state);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'stateName' => 'required|unique:state-masters,stateName,' . $request->stateId . ',stateId',
            ]);

            $data = [
                'stateName' => $request->stateName,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            StateMaster::where("stateId", "=", $request->stateId)->update($data);

            DB::commit();

            Toastr::success('State updated successfully :)', 'Success');
            return back();
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
            $state = StateMaster::where([
                'iStatus' => 1,
                'isDelete' => 0,
                'stateId' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('State deleted successfully :)', 'Success');
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
        // dd($request->all());
        try {
            $ids = $request->input('state_ids', []);
            StateMaster::whereIn('stateId', $ids)->delete();

            Toastr::success('State deleted successfully :)', 'Success');
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

    public function updateStatus(Request $request)
    {
        try {
            if ($request->status == 1) {
                StateMaster::where(['stateId' => $request->stateId])->update(['iStatus' => 0]);
                Toastr::success('State inactive successfully :)', 'Success');
            } else {
                StateMaster::where(['stateId' => $request->stateId])->update(['iStatus' => 1]);
                Toastr::success('State active successfully :)', 'Success');
            }
            echo 1;
        } catch (ValidationException $e) {
            DB::rollBack();
            Toastr::error('Validation Error: ' . implode(', ', $e->errors()));
            return 0;
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Error: ' . $th->getMessage());
            return 0;
        }
    }
}
