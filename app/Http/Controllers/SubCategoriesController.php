<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;



class SubCategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = SubCategories::with('category')->orderBy('strSubCategoryName', 'asc');
            // Search by Category if the 'category_id' parameter is provided
            if ($request->has('categoryid') && $request->categoryid != '') {
                $query->where('iCategoryId', $request->categoryid);
            }
             // Search by Category if the 'category_id' parameter is provided
            if ($request->has('subcat_id') && $request->subcat_id != '') {
                $query->where('iSubCategoryId', $request->subcat_id);
            }

            // Paginate the results (use per_page from the config file)
            $subcategories = $query->paginate(config('app.per_page'));

            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();

            return view('sub_categories.index', compact('subcategories', 'categories','query'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add(Request $request)
    {
        try {
            $categories = Categories::orderBy('Category_name', 'asc')
                ->select('Categories_id', 'Category_name')
                ->where(['iStatus' => 1])
                ->get();
            return view('sub_categories.add', compact('categories'));
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
                'SubCategoryname' => 'required|unique:subcategory,strSubCategoryName',
                // 'SubCategoryimage' => [
                //     'required',
                //     'file',
                // ],
            ]);
         
            // Get the category name by Categoryid
            $category = Categories::where('Categories_id', $request->Categoryid)->first();

            // Check if category exists
            if (!$category) {
                Toastr::error('Category not found', 'Error');
                return redirect()->back()->withInput();
            }

            $categoriesname = $category->Category_name;  // Retrieve Category_name

            // Format the subcategory slug name
            $lowercase = Str::lower($request->SubCategoryname);
            $slugname = str_replace(' ', '-', $lowercase);
            
            $icon = '';
            // Handle the Category icon upload
            if ($request->hasFile('SubCategoryimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];

                $iconFile = $request->file('SubCategoryimage');
                $iconExtension = $iconFile->getClientOriginalExtension();

                $icon = time() . '_' . date('dmYHis') . '.' . $iconExtension;
                $destinationPath = $root . '/upload/subcategory-images/';

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $iconFile->move($destinationPath, $icon);
            }

            // Create a new SubCategory record
            SubCategories::create([
                'strCategoryName' => $categoriesname,
                'strSubCategoryName' => $request->SubCategoryname,
                'title' => $request->title,
                'sub_title' => $request->SubTitle,
                'iCategoryId' => $request->Categoryid,
                'strSlugName' => $slugname,
                'SubCategories_img' => $icon,
                'sub_rat_flag' => $request->sub_rat_flag, // <— store 1/0
                'meta_keyword' => $request->metaKeyword,
                'meta_description' => $request->metaDescription,
                'meta_head' => $request->head,
                'meta_title' => $request->meta_title,
                'meta_body' => $request->body,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            // Commit the transaction
            DB::commit();

            Toastr::success('SubCategory created successfully!', 'Success');
            return redirect()->route('sub_categories.index')->with('success', 'SubCategory Created Successfully.');
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
    
    public function service_subservice_mapping(Request $request)
    {

        $catid = $request->service;


        if ($catid) {

            $SubService =  SubCategories::orderBy('strSubCategoryName', 'asc')->where(['iCategoryId' => $catid])->get();
            if ($SubService) {

                $html = "";

                $html .= "<option value=''>Select Sub Category</option>";

                foreach ($SubService as $SubSer) {

                    $html .= "<option value='" . $SubSer->iSubCategoryId . "'>" . $SubSer->strSubCategoryName . "</option>";
                }



                return $html;
            }
        }
    }


    public function edit(Request $request)
    {

        $subcategory = SubCategories::where('iSubCategoryId', $request->id)->first();
        $categories = Categories::orderBy('Category_name', 'asc')
            ->select('Categories_id', 'Category_name')
            ->where(['iStatus' => 1])
            ->get();
        return view('sub_categories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validate Request
          $request->validate([
                'SubCategoryname' => [
                    'required',
                    Rule::unique('subcategory', 'strSubCategoryName')->ignore($request->SubCategoriesid, 'iSubCategoryId')
                ],
                'EditSubCategoryimage' => ['file'],
            ]);

          
            // Retrieve the category
            $category = Categories::where('Categories_id', $request->Categoryid)->first();
            if (!$category) {
                throw new \Exception('Category not found.');
            }
            $categoriesname = $category->Category_name;

            // Generate slug name
            $slugname = Str::slug($request->SubCategoryname);

            // Get the existing image from the database
            $subcategory = SubCategories::where("iSubCategoryId", $request->SubCategoriesid)->first();
            $existingImage = $subcategory->SubCategories_img ?? null;

            // Handle Image Upload
            if ($request->hasFile('EditSubCategoryimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $iconFile = $request->file('EditSubCategoryimage');
                $iconExtension = $iconFile->getClientOriginalExtension();

                // New image name
                $icon = time() . '_' . date('dmYHis') . '.' . $iconExtension;
                $destinationPath = $root . '/upload/subcategory-images/';

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $iconFile->move($destinationPath, $icon);

                // Delete old image if exists
                if ($existingImage && file_exists($destinationPath . '/' . $existingImage)) {
                    unlink($destinationPath . '/' . $existingImage);
                }
            } else {
                // Retain the existing image if no new image is uploaded
                $icon = $existingImage;
            }
          
            // Update SubCategory
            SubCategories::where("iSubCategoryId", $request->SubCategoriesid)->update([
                'iCategoryId' => $request->Categoryid,
                'strCategoryName' => $categoriesname,
                'strSubCategoryName' => $request->SubCategoryname,
                'title' => $request->Title,
                'sub_title' => $request->SubTitle,
                'SubCategories_img' => $icon, // Keeps the existing image if no new image is uploaded
                'strSlugName' => $slugname,
                'sub_rat_flag' => $request->edit_flag,
                'meta_keyword' => $request->metaKeyword,
                'meta_description' => $request->metaDescription,
                'meta_head' => $request->head,
                'meta_title' => $request->meta_title,
                'meta_body' => $request->body,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit();

            Toastr::success('SubCategory updated successfully :)', 'Success');
            return redirect()->route('sub_categories.index');
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
            $state = SubCategories::where([

                'isDelete' => 0,
                'iSubCategoryId' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('SubCategory deleted successfully :)', 'Success');
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
            $ids = $request->input('iSubCategoryIds', []);
            SubCategories::whereIn('iSubCategoryId', $ids)->delete();

            Toastr::success('SubCategory deleted successfully :)', 'Success');
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
