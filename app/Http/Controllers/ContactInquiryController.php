<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inquiry;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class ContactInquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {

            $contactinquiry = Inquiry::orderBy('id', 'desc')
                ->paginate(config('app.per_page'));
            return view('ContactInquiry.index', compact('contactinquiry'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }


    public function delete(Request $request)

    {

        DB::beginTransaction();



        try {

            Inquiry::where([

                'id' => $request->id

            ])->delete();



            DB::commit();



            Toastr::success('contact inquiry deleted successfully :)', 'Success');

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
