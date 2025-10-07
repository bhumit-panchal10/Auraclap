<?php

namespace App\Http\Controllers;

use App\Models\VideoMaster;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VideoListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $VideoMaster = VideoMaster::orderBy('id', 'desc')->paginate(config('app.per_page'));
            return view('VideoList.index', compact('VideoMaster'));
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
                'link' => 'required',
            ]);

            VideoMaster::create([
                'title' => $request->title,
                'link' => $request->link,
                'created_at' => now(),
            ]);

            DB::commit();

            Toastr::success('Video link created successfully :)', 'Success');
            return redirect()->back();
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
            Toastr::error('Failed to create faq: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $VideoMaster = VideoMaster::where('id', $request->id)->first();
       

        return json_encode($VideoMaster);
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'editlink' => 'required',
            ]);

            $data = [
                'title' => $request->edittitle,
                'link' => $request->editlink,
                'updated_at' => now(),

            ];

            VideoMaster::where("id", "=", $request->videoid)->update($data);

            DB::commit();

            Toastr::success('Video link Update successfully :)', 'Success');
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
           
            VideoMaster::where([
                'id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Booking Cancel Reason deleted successfully :)', 'Success');
            //return back();
            return response()->json(['success' => true]);
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
        // dd($request->all());
        try {
            $ids = $request->input('video_ids', []);
            VideoMaster::whereIn('id', $ids)->delete();

            Toastr::success('Booking Cancel Reason deleted successfully :)', 'Success');
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
