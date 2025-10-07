<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CMSMaster;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CMSController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $CMSMaster = CMSMaster::orderBy('id', 'asc')->paginate(config('app.per_page'));

            return view('cms_master.index', compact('CMSMaster'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit(Request $request)
    {
        $CMSMaster = CMSMaster::where('id', $request->id)->first();

        return view('cms_master.edit', compact('CMSMaster'));
        return json_encode($CMSMaster);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                //'strTitle' => 'required',
                'strDescription' => 'required'
            ]);

            $data = [
                //'strTitle' => $request->strTitle,
                'strDescription' => $request->strDescription,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            CMSMaster::where("id", "=", $id)->update($data);

            DB::commit();

            Toastr::success('CMS updated successfully :)', 'Success');
            return redirect()->route('cms.index');
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
}
