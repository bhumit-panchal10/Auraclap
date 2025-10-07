<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use App\Models\HowItWorks;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class HowItWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $image = HowItWorks::select('*')
                ->orderBy('id', 'asc')
                ->paginate(config('app.per_page'));

            return view('howitworks.index', compact('image'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {

            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();

            return view('howitworks.add', compact('categories'));
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


            ]);

            // Get the category name by Categoryid
            $category = Categories::where('Categories_id', $request->Categoryid)->first();


            if (!$category) {
                Toastr::error('Category not found', 'Error');
                return redirect()->back()->withInput();
            }

            $categoriesname = $category->Category_name;  // Retrieve Category_name


            // Handle image upload
            $img = "";
            if ($request->hasFile('image')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('image');

                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/Image/';

                // Create the directory if it doesn't exist
                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                // Move the image to the destination
                $image->move($destinationpath, $img);
            }
            // Create a new SubCategory record
            HowItWorks::create([

                'category_id' => $request->Categoryid,
                'catname' => $categoriesname,
                'title' => $request->title,
                'description' => $request->description,
                'image' => $img,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            // Commit the transaction
            DB::commit();

            Toastr::success('how it works created successfully!', 'Success');
            return redirect()->route('howitworks.index')->with('success', 'how it works Created Successfully.');
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
            Toastr::error('Failed to create SubCategory: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }


    public function edit(Request $request, $id)
    {

        try {

            $image = HowItWorks::where('id', $id)->first();

            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();

            return view('howitworks.edit', compact('image', 'categories'));
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        $category = Categories::where('Categories_id', $request->Categoryid)->first();


        $categoriesname = $category->Category_name;  // Retrieve Category_name


        try {
            $request->validate([
                'Categoryid' => 'required',

            ]);
            $img = "";

            if ($request->hasFile('editmain_img')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('editmain_img');

                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/Image/';


                // Create directory if it doesn't exist
                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                // Move the image to the destination path
                $image->move($destinationpath, $img);

                // Delete the old image if it exists
                $oldImg = $request->input('hiddenPhoto');
                if ($oldImg && file_exists($destinationpath . '/' . $oldImg)) {
                    unlink($destinationpath . '/' . $oldImg);
                }
            } else {
                // If no image is uploaded, keep the old image name
                $img = $request->input('hiddenPhoto');
            }
            $data = [

                'catname' => $categoriesname,
                'category_id' => $request->Categoryid,
                'title' => $request->edittitle,
                'description' => $request->editdes,
                'image' => $img,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            HowItWorks::where("id", "=", $request->imageid)->update($data);
            DB::commit();

            Toastr::success('updated successfully :)', 'Success');
            return redirect()->route('howitworks.index')->with('success', 'Update Successfully.');
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
        // dd($request);


        if ($categoryid) {

            $subcategory =  SubCategories::orderBy('strSubCategoryName', 'asc')->where(['iCategoryId' => $categoryid])->get();

            // dd($cities);

            if ($subcategory) {

                $html = "";

                $html .= "<option value=''>Select Subcategory</option>";

                foreach ($subcategory as $subcat) {

                    $html .= "<option value='" . $subcat->iSubCategoryId . "'>" . $subcat->strSubCategoryName . "</option>";
                }



                return $html;
            }
        }
    }




    public function delete(Request $request)
    {

        DB::beginTransaction();

        try {
            $Image = HowItWorks::where([

                'isDelete' => 0,
                'id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Image deleted successfully :)', 'Success');
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
            $ids = $request->input('image_ids', []);
            HowItWorks::whereIn('id', $ids)->delete();

            Toastr::success('Image deleted successfully :)', 'Success');
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
