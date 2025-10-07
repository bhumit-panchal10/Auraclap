<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StateMaster;
use App\Models\Testimonial;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;


class TestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $testimonials = Testimonial::orderBy('id', 'asc')
                ->where(['iStatus' => 1, 'isDelete' => 0])
                ->paginate(env('PER_PAGE'));
            return view('testimonial_master.index', compact('testimonials'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {
            return view('testimonial_master.add');
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
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required',
                'imageURL' => 'required',
                'cityName' => 'required',
                'rating' => 'required',
                'description' => 'required'
            ]);

            $img = "";
            if ($request->hasFile('imageURL')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('imageURL');
                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/vybecab/upload/testimonial/';
                // $destinationpath = $root . '/upload/images/';
                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }
                $image->move($destinationpath, $img);
            }

            Testimonial::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'imageURL' => $img,
                'cityName' => $request->cityName,
                'rating' => $request->rating,
                'description' => $request->description,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit();

            Toastr::success('Testimonial created successfully :)', 'Success');
            return redirect()->route('testimonial.index');
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
            Toastr::error('Failed to create Testimonial: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $testimonials = Testimonial::where('id', $request->id)->first();

        return view('testimonial_master.edit', compact('testimonials'));
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        DB::beginTransaction();

        try {
            $request->validate([
                'firstName' => 'required',
                'lastName' => 'required',
                'email' => 'required',
                // 'imageURL' => 'required',
                'cityName' => 'required',
                'rating' => 'required',
                'description' => 'required'
            ]);

            $img = "";
            if ($request->hasFile('imageURL')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('imageURL');
                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/vybecab/upload/testimonial/';
                // $destinationpath = $root . '/upload/images/';
                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }
                $image->move($destinationpath, $img);
                $oldImg = $request->input('hiddenPhoto') ? $request->input('hiddenPhoto') : null;

                if ($oldImg != null || $oldImg != "") {
                    if (file_exists($destinationpath . $oldImg)) {
                        unlink($destinationpath . $oldImg);
                    }
                }
            } else {
                $oldImg = $request->input('hiddenPhoto');
                $img = $oldImg;
            }

            $data = [
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'email' => $request->email,
                'imageURL' => $img,
                'cityName' => $request->cityName,
                'rating' => $request->rating,
                'description' => $request->description,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            Testimonial::where("id", "=", $id)->update($data);

            DB::commit();

            Toastr::success('Testimonial updated successfully :)', 'Success');
            return redirect()->route('testimonial.index');
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
            $testimonial = Testimonial::where(['id' => $request->id])->first();

            $root = $_SERVER['DOCUMENT_ROOT'];
            $destinationPath = $root . '/vybecab/upload/testimonial/';
            // $destinationpath = $root . '/upload/images/';

            // Check if the testimonial has an image and delete it if exists
            if ($testimonial->imageURL && file_exists($destinationPath . $testimonial->imageURL)) {
                unlink($destinationPath . $testimonial->imageURL);
            }

            Testimonial::where([
                'iStatus' => 1,
                'isDelete' => 0,
                'id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Testimonial deleted successfully :)', 'Success');
            // return back();
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
            $ids = $request->input('testimonial_ids', []);
            $testimonials = Testimonial::whereIn('id', $ids)->get();
            //Testimonial::whereIn('id', $ids)->delete();
            $root = $_SERVER['DOCUMENT_ROOT'];
            $destinationPath = $root . '/vybecab/upload/testimonial/';
            // $destinationpath = $root . '/upload/images/';
            foreach ($testimonials as $testimonial) {
                // Check if the testimonial has an image and delete it if exists
                if ($testimonial->imageURL && file_exists($destinationPath . $testimonial->imageURL)) {
                    unlink($destinationPath . $testimonial->imageURL);
                }
                Testimonial::whereIn('id', $testimonial->id)->delete();
            }

            Toastr::success('Testimonial deleted successfully :)', 'Success');
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
