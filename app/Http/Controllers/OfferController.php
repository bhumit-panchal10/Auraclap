<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use App\Models\CategoryOffer;
use App\Models\StateMaster;
use App\Models\CityMaster;
use App\Models\Offer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class OfferController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        try {

            $offers = Offer::orderBy('id', 'desc')
                ->paginate(config('app.per_page'));
            return view('offer.index', compact('offers'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }
    public function promocodelist()
    {

        try {

            $offers = Promocode::with('vendor')->paginate(config('app.per_page')); // Default to 10 if PER_PAGE is not set

            return view('vendor.Promocode.promocodelist', compact('offers'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }
    public function create()
    {

        $categories = Categories::orderBy('Category_name', 'asc')
            ->select('Categories_id', 'Category_name')
            ->get();

        return view('offer.add', compact('categories'));
    }



    public function store(Request $request)

    {



        DB::beginTransaction();



        try {

            $request->validate([
                'text' => 'required|unique:offer,text',
            ]);

            $category = implode(',', $request->Categoryid);


            // Create a new StateMaster record

            $Offer = new Offer();

            $Offer->text = $request->text;
            $Offer->value = $request->value;

            $Offer->startdate = date('Y-m-d', strtotime($request->startdate));

            $Offer->enddate = date('Y-m-d', strtotime($request->enddate));
            $Offer->created_at = now();


            $Offer->strIP = $request->ip();
            $Offer->save();

            if ($request->Categoryid != null) {
                foreach ($request->Categoryid as $key => $value) {

                    $CategoryOffer = new CategoryOffer();
                    $CategoryOffer->categoryId = $value;
                    $CategoryOffer->offerId = $Offer->id;
                    $CategoryOffer->save();
                }
            }
            DB::commit();

            return redirect()->route('offer.index')->with('success', 'Offer Created Successfully.');
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


        $categories = Categories::orderBy('Category_name', 'asc')
            ->select('Categories_id', 'Category_name')
            ->get();

        $CategoryOffer = CategoryOffer::where(['offerId' => $id])->get();
        $data = Offer::where('id', $request->id)->first();
        return view('offer.edit', compact('data', 'categories', 'CategoryOffer'));
    }



    public function update(Request $request)

    {

        DB::beginTransaction();



        try {

            $request->validate([
                'text' => 'required|unique:offer,text,' . $request->id . ',id',
            ]);



            $update = DB::table('offer')
                ->where(['iStatus' => 1, 'isDelete' => 0, 'id' => $request->id])
                ->update([
                    'text' => $request->text,
                    'value' => $request->value,
                    'startdate' => date('Y-m-d', strtotime($request->startdate)),
                    'enddate' => date('Y-m-d', strtotime($request->enddate)),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            if ($request->Categoryid != null) {
                if ($request->Categoryid != null) {
                    $delete1 = CategoryOffer::where(['offerId' => $request->id])->delete();
                    foreach ($request->Categoryid as $key => $value) {
                        $CategoryOffer = new CategoryOffer();
                        $CategoryOffer->Categoryid = $value;
                        $CategoryOffer->offerId = $request->id;
                        $CategoryOffer->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('offer.index')->with('success', 'Offer Updated Successfully.');
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

            Offer::where([

                'iStatus' => 1,

                'isDelete' => 0,

                'id' => $request->id

            ])->delete();



            DB::commit();



            Toastr::success('Offer deleted successfully :)', 'Success');

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

            $ids = $request->input('offer_ids', []);

            Offer::whereIn('id', $ids)->delete();



            Toastr::success('Offer deleted successfully :)', 'Success');

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
                Offer::where(['id' => $request->offerId])->update(['iStatus' => 0]);
                Toastr::success('Offer inactive successfully :)', 'Success');
            } else {
                Offer::where(['id' => $request->offerId])->update(['iStatus' => 1]);
                Toastr::success('Offer active successfully :)', 'Success');
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
