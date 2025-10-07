<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StateMaster;
use App\Models\CityMaster;
use App\Models\Categories;
use App\Models\Pincode;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class PincodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $pincode = Pincode::with('state', 'city')->orderBy('pin_id', 'desc')
                ->paginate(config('app.per_page'));



            return view('Pincode.index', compact('pincode'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {

            $states = StateMaster::orderBy('stateName', 'asc')->get();
            $cities = CityMaster::orderBy('cityName', 'asc')->get();
            return view('Pincode.add', compact('states', 'cities'));
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
                'stateid' => 'required',
                'city' => 'required',
                'pincode' => 'required',
            ]);

            // Split the pincodes and remove empty values
            $pincodes = array_filter(array_map('trim', explode(',', $request->input('pincode'))));

            // Initialize an array to store valid pincodes
            $processedPincodes = [];

            foreach ($pincodes as $pincode) {
                if (strlen($pincode) == 6 && is_numeric($pincode)) {
                    $processedPincodes[] = $pincode; // Add valid 6-digit pincodes
                } else {
                    Toastr::error('Pincode should be exactly 6 digits: ' . $pincode, 'Error');
                    return redirect()->back()->withInput();
                }
            }

            // Insert each valid pincode as a separate record
            foreach ($processedPincodes as $pincode) {
                Pincode::create([
                    'state_id' => $request->stateid,
                    'city_id' => $request->city,
                    'pincode' => $pincode,
                    'created_at' => now(),
                    'strIP' => $request->ip(),
                ]);
            }

            // Commit the transaction
            DB::commit();

            Toastr::success('Pincode(s) created successfully!', 'Success');
            return redirect()->route('pincode.index')->with('success', 'Pincode(s) Created Successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->errors();
            $errorMessages = [];

            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }

            Toastr::error(implode(', ', $errorMessages), 'Error');
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Failed to create Pincode: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }



    public function edit(Request $request, $id)
    {

        try {
            $states = StateMaster::orderBy('stateName', 'asc')->get();
            $cities = CityMaster::orderBy('cityName', 'asc')->get();
            $pincode = Pincode::where('pin_id', $id)->first();


            return view('Pincode.edit', compact('pincode', 'states', 'cities'));
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {
            $data = [

                'state_id' => $request->stateid,
                'city_id' => $request->city,
                'pincode' => $request->pincode,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];


            Pincode::where("pin_id", "=", $request->pinid)->update($data);
            DB::commit();

            Toastr::success('Pincode updated successfully :)', 'Success');
            return redirect()->route('pincode.index')->with('success', 'Pincode Update Successfully.');
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
            $pincode = Pincode::where([

                'isDelete' => 0,
                'pin_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('pincode deleted successfully :)', 'Success');
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
            $ids = $request->input('pincode_ids', []);
            Pincode::whereIn('pin_id', $ids)->delete();

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
