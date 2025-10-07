<?php

namespace App\Http\Controllers;

use App\Models\DealsOption;
use App\Models\Deals;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        try {
            $dealid = session('deal_id');
            $dealoption = DealsOption::where('deal_id', $dealid)
                ->orderBy('deal_option_id', 'desc')
                ->paginate(config('app.per_page'));

            return view('vendor.Deals.option.index', compact('dealoption', 'id', 'dealid'));
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
                'option_title' => 'required',
                'regular_price' => 'required',
                'pricecut_price' => 'required',
                'monthly_voucher_cap' => 'required',
            ]);

            DealsOption::create([
                'option_title' => $request->option_title,
                'regular_price' => $request->regular_price,
                'pricecut_price' => $request->pricecut_price,
                'monthly_voucher_cap' => $request->monthly_voucher_cap,
                'deal_id' => $request->dealid,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit();

            Toastr::success('Deal Option created successfully :)', 'Success');
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
        $faq = DealsOption::where('deal_option_id', $request->id)->first();

        return json_encode($faq);
    }

    public function view($id)
    {
        $faq = DealsOption::find($id); // Replace with your actual model and query logic
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
                'Editoptiontitle' => 'required',
                'Editregular_price' => 'required',
                'Editpricecut_price' => 'required',
                'Editmonthly_voucher_cap' => 'required',
            ]);

            $data = [
                'option_title' => $request->Editoptiontitle,
                'regular_price' => $request->Editregular_price,
                'pricecut_price' => $request->Editpricecut_price,
                'monthly_voucher_cap' => $request->Editmonthly_voucher_cap,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            DealsOption::where("deal_option_id", "=", $request->dealoptionid)->update($data);

            DB::commit();

            Toastr::success('Deal Option updated successfully :)', 'Success');
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
            DealsOption::where([
                'deal_option_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Deal Option deleted successfully :)', 'Success');
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
            DealsOption::whereIn('id', $ids)->delete();

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
        // dd($request);
        try {
            if ($request->status == 1) {
                DealsOption::where(['id' => $request->faqId])->update(['iStatus' => 0]);
                Toastr::success('Faq inactive successfully :)', 'Success');
            } else {
                DealsOption::where(['id' => $request->faqId])->update(['iStatus' => 1]);
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
