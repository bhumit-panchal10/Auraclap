<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use App\Models\CityMaster;
use App\Models\Managerate;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class ManageRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
     { 
        try {
            // Initialize query
        
            $query = Managerate::with('category', 'subcategory', 'city')->orderBy('rate_id', 'desc');

            // Search by SubCategory ID if the 'subcategory_id' parameter is provided
            if ($request->has('subcat_id') && $request->subcat_id != '') {
                $query->where('subcate_id', $request->subcat_id);
            }

            // Search by Category ID if the 'category_id' parameter is provided
            if ($request->has('categoryid') && $request->categoryid != '') {
                $query->where('cate_id', $request->categoryid);
            }

            // Paginate the results (use per_page from the config file)
            $rates = $query->paginate(config('app.per_page'));
        
            // For the categories and subcategories dropdowns
            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where('iStatus', 1)
                ->get();

            $subcategories = SubCategories::orderBy('strSubCategoryName', 'asc')
                ->select('iSubCategoryId', 'strSubCategoryName')
                ->get();

            return view('Managerate.index', compact('rates', 'categories', 'subcategories'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {
            $subcategories = SubCategories::orderBy('strSubCategoryName', 'asc')
                ->select('iSubCategoryId', 'strSubCategoryName');
            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();
            $cities = CityMaster::get();

            return view('Managerate.add', compact('categories', 'subcategories', 'cities'));
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
                'Categoryid' => 'required',
                'AreasubCategoryid' => 'required',


            ]);
            // Create a new Managerate record
            Managerate::create([
                'cate_id' => $request->Categoryid,
                'city_id' => $request->Cityid,
                'subcate_id' => $request->AreasubCategoryid,
                'title' => $request->title,
                'amount' => $request->amount,
                'time' => $request->time,
                'description' => $request->description,
                'how_it_work_description' => $request->how_it_work_description,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            // Commit the transaction
            DB::commit();

            Toastr::success('Rate created successfully!', 'Success');
            return redirect()->route('manage_rate.index')->with('success', 'Rate Created Successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->errors();  // Get the validation errors
            $errorMessages = [];
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $errorMessages[] = $message;
                }
            }

            $errorMessageString = implode(', ', $errorMessages);
            Toastr::error($errorMessageString, 'Error');
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Failed to create Rate: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }


    public function edit(Request $request, $id)
    {

        try {

            $rates = Managerate::where('rate_id', $id)->first();
            $subcategories = SubCategories::orderBy('strSubCategoryName', 'asc')
                ->select('iSubCategoryId', 'strSubCategoryName')->get();

            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();
            $cities = CityMaster::get();


            return view('Managerate.edit', compact('rates', 'categories', 'subcategories', 'cities'));
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {
            $request->validate([
                'Categoryid' => 'required',
                'AreasubCategoryid' => 'required',


            ]);

            $data = [

                'rate_id' => $request->rateid,
                'cate_id' => $request->Categoryid,
                'city_id' => $request->editCityid,
                'subcate_id' => $request->AreasubCategoryid,
                'title' => $request->title,
                'amount' => $request->amount,
                'time' => $request->time,
                'description' => $request->description,
                'how_it_work_description' => $request->how_it_work_description,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            Managerate::where("rate_id", "=", $request->rateid)->update($data);
            DB::commit();

            Toastr::success('Rate updated successfully :)', 'Success');
            return redirect()->route('manage_rate.index')->with('success', 'Rate Update Successfully.');
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

    public function category_subcategory_mapping(Request $request)

    {


        $categoryid = $request->categoryid;


        if ($categoryid) {

            $subcategory =  SubCategories::orderBy('strSubCategoryName', 'asc')->where(['iCategoryId' => $categoryid])->get();

            

            if ($subcategory) {

                $html = "";

                $html .= "<option value=''>Select Subcategory</option>";

                foreach ($subcategory as $subcat) {

                    $html .= "<option value='" . $subcat->iSubCategoryId . "'>" . $subcat->strSubCategoryName . "</option>";
                }

              

                echo $html;
            }
            
        }
    }




    public function delete(Request $request)
    {

        DB::beginTransaction();

        try {
            $rate = Managerate::where([

                'isDelete' => 0,
                'rate_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Rate deleted successfully :)', 'Success');
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
            $ids = $request->input('rate_ids', []);
            Managerate::whereIn('rate_id', $ids)->delete();

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

    public function updateStatus(Request $request)
    {
        // dd($request);
        try {
            if ($request->status == 1) {
                SubCategories::where(['iSubCategoryId' => $request->iSubCategoryId])->update(['iStatus' => 0]);
                Toastr::success('Subcategory inactive successfully :)', 'Success');
            } else {
                SubCategories::where(['iSubCategoryId' => $request->iSubCategoryId])->update(['iStatus' => 1]);
                Toastr::success('Subcategory active successfully :)', 'Success');
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
