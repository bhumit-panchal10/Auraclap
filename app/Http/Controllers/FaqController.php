<?php

namespace App\Http\Controllers;

use App\Models\FaqMaster;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $faqs = FaqMaster::orderBy('id', 'desc')->paginate(config('app.per_page'));
            return view('faq.index', compact('faqs'));
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
                'question' => 'required',
                'answer' => 'required',
            ]);

            FaqMaster::create([
                'question' => $request->question,
                'answer' => $request->answer,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit();

            Toastr::success('Faq created successfully :)', 'Success');
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
        $faq = FaqMaster::where('id', $request->id)->first();

        return json_encode($faq);
    }

    public function view($id)
    {
        $faq = FaqMaster::find($id); // Replace with your actual model and query logic
        if ($faq) {
            return response()->json([
                'question' => $faq->question,
                'answer' => $faq->answer,
            ]);
        }

        return response()->json(['error' => 'FAQ not found'], 404);
    }



    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'question' => 'required',
                'answer' => 'required',
            ]);

            $data = [
                'question' => $request->question,
                'answer' => $request->answer,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            FaqMaster::where("id", "=", $request->faqid)->update($data);

            DB::commit();

            Toastr::success('Faq updated successfully :)', 'Success');
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
            FaqMaster::where([
                'id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Faq deleted successfully :)', 'Success');
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
            $ids = $request->input('faq_ids', []);
            FaqMaster::whereIn('id', $ids)->delete();

            Toastr::success('Faq deleted successfully :)', 'Success');
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

    public function updateStatus(Request $request)
    {
      
        try {
            if ($request->status == 1) {
                FaqMaster::where(['id' => $request->faqId])->update(['isActive' => 0]);
                Toastr::success('Faq inactive successfully :)', 'Success');
            } else {
                FaqMaster::where(['id' => $request->faqId])->update(['isActive' => 1]);
                Toastr::success('Faq active successfully :)', 'Success');
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
