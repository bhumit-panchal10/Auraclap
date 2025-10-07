<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\CityMaster;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $categories = Categories::orderBy('Category_name', 'asc')->paginate(config('app.per_page'));
            return view('categories.index', compact('categories'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {
            $cities = CityMaster::all();
            return view('categories.add', compact('cities'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction(); // Start a database transaction


        try {
            $request->validate([
                'Categoryname' => 'required|unique:Categories,Category_name',
                'Categoryimage' => [
                    'required',
                    'file',
                    'max:512',
                ],
                'homeCategoryimage' => [
                    'required',
                    'file',
                    'max:512', // 500KB
                ],
                'carousel_image' => [
                    'file',
                    'max:512', // 500KB
                    //'dimensions:width=409,height=192',
                ],
                'ratecard_pdf' => [
                    'file',
                    'mimes:pdf',
                ],
            ], [    
                'ratecard_pdf.mimes' => 'Only PDF files are allowed for Rate Card.',
                'homeCategoryimage.max' => 'Home Category Image size cannot exceed 500KB.',
                'Categoryimage.max' => 'Category Image size cannot exceed 500KB.',
                //'carousel_image.dimensions' => 'Carousel image dimensions must be 409x192 pixels.',
            ]);
            $slugname = Str::slug($request->Categoryname);
            $img = "";
            $homeimg = "";
            $icon = "";
            $carouselimg = "";

            if ($request->hasFile('Category_icon')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $categoryicon = $request->file('Category_icon');
                $icon = time() . '_' . date('dmYHis') . '.' . $categoryicon->getClientOriginalExtension();
                $destinationpath = $root . '/upload/category-icon/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $categoryicon->move($destinationpath, $icon);
            }

            if ($request->hasFile('carousel_image')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $carousel_image = $request->file('carousel_image');
                $carouselimg = time() . '_' . date('dmYHis') . '.' . $carousel_image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/carousel-icon/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $carousel_image->move($destinationpath, $carouselimg);
            }

            // Handle the Category icon upload
            if ($request->hasFile('Categoryimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('Categoryimage');
                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/category-image/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $image->move($destinationpath, $img);
            }

            if ($request->hasFile('homeCategoryimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $homeimage = $request->file('homeCategoryimage');
                $homeimg = time() . '_' . date('dmYHis') . '.' . $homeimage->getClientOriginalExtension();


                $destinationpath = $root . '/upload/Home-category-image/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $homeimage->move($destinationpath, $homeimg);
            }

            if ($request->hasFile('ratecard_pdf')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $ratecardpdf = $request->file('ratecard_pdf');
                $rateimg = time() . '_' . date('dmYHis') . '.' . $ratecardpdf->getClientOriginalExtension();


                $destinationpath = $root . '/upload/RateCardPdf/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $ratecardpdf->move($destinationpath, $rateimg);
            }
           

            // Create a new Categories record
            $Categories = Categories::create([
                'Category_name' => $request->Categoryname,
                'caption' => $request->caption,
                'rating' => $request->rating,
                'city_id' => $request->CityId,
                'hours' => $request->hours,
                'onwards_amount' => $request->onwards_amount,
                'warranty' => $request->warranty,
                'Categories_slug' => $slugname,
                'Categories_img' => $img,
                'home_cate_image' => $homeimg,
                'Categories_icon' => $icon,
                'carousel_image' => $carouselimg,
                'ratecard_pdf' => $rateimg,
                'meta_title' => $request->meta_title,
                'meta_keyword' => $request->metaKeyword,
                'meta_description' => $request->metaDescription,
                'meta_head' => $request->head,
                'meta_body' => $request->body,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit(); // Commit the transaction

            Toastr::success('Categories created successfully :)', 'Success');
            return redirect()->route('categories.index')->with('success', 'Categories Created Successfully.');
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->errors();
            $errorMessageString = implode(', ', Arr::flatten($errors));

            Toastr::error($errorMessageString, 'Error');
            return redirect()->back()->withInput();
        } catch (\Throwable $th) {
            DB::rollBack();
            Toastr::error('Failed to create Categories: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function edit(Request $request)
    {
        $cities = CityMaster::all();    
        $categories = Categories::where('Categories_id', $request->id)->first();

        return view('categories.edit', compact('cities', 'categories'));
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {
            $request->validate(
                [
                    'Category_name' => 'required|unique:Categories,Category_name,' . $request->Categoriesid . ',Categories_id',
                    'Categoryimage' => [
                        'file',
                        'max:512',
                    ],
                    'homeCategoryimage' => [
                        'file',
                        'max:512', // 500KB
                    ],
                    'carousel_image' => [
                        'file',
                        'max:512', // 500KB
                    ],
                ],
                [
                    'ratecard_pdf.mimes' => 'Only PDF files are allowed for Rate Card.',
                    'homeCategoryimage.max' => 'Home Category Image size cannot exceed 500KB.',
                    'Categoryimage.max' => 'Category Image size cannot exceed 500KB.',
                ]
            );
            $slugname = Str::slug($request->Category_name);
            //$slugname = str_replace(' ', '-', $lowercase);

            $Categories = Categories::where("Categories_id", $request->Categoriesid)->first();
            $existingImage = $Categories->Categories_img ?? null;
            $existinghomeImage = $Categories->home_cate_image ?? null;
            $existingratecardPdf = $Categories->ratecard_pdf ?? null;
            $existingicon = $Categories->Categories_icon ?? null;
            $existingcarousel = $Categories->carousel_image ?? null;


            $img = "";
            $homeimg = "";
            $icon = "";
            $carouselimg = "";
            $RateCardPdfimg = "";


            if ($request->hasFile('Categoryicon')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $categoryicon = $request->file('Categoryicon');
                $icon = time() . '_' . date('dmYHis') . '.' . $categoryicon->getClientOriginalExtension();
                $destinationpath = $root . '/upload/category-icon/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $categoryicon->move($destinationpath, $icon);
            }
            else {
                $icon = $existingicon;
            }

            if ($request->hasFile('Carouselimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $carousel_image = $request->file('Carouselimage');
                $carouselimg = time() . '_' . date('dmYHis') . '.' . $carousel_image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/carousel-icon/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $carousel_image->move($destinationpath, $carouselimg);
            }
            else {
                $carouselimg = $existingcarousel;
            }

            if ($request->hasFile('Categoryimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('Categoryimage');

                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/category-image/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $image->move($destinationpath, $img);

                $oldImg = $request->input('hiddenPhoto');
                if ($oldImg && file_exists($destinationpath . '/' . $oldImg)) {
                    unlink($destinationpath . '/' . $oldImg);
                }
            } else {
                $img = $existingImage;
            }

            if ($request->hasFile('homeCategoryimage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $homeimage = $request->file('homeCategoryimage');

                $homeimg = time() . '_' . date('dmYHis') . '.' . $homeimage->getClientOriginalExtension();
                $destinationpath = $root . '/upload/Home-category-image/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $homeimage->move($destinationpath, $homeimg);

                $oldImg = $request->input('hiddenimagePhoto');
                if ($oldImg && file_exists($destinationpath . '/' . $oldImg)) {
                    unlink($destinationpath . '/' . $oldImg);
                }
            } else {
                $homeimg = $existinghomeImage;
            }

            if ($request->hasFile('RateCardPdf')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $RateCardPdfimage = $request->file('RateCardPdf');

                $RateCardPdfimg = time() . '_' . date('dmYHis') . '.' . $RateCardPdfimage->getClientOriginalExtension();
                $destinationpath = $root . '/upload/RateCardPdf/';

                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }

                $RateCardPdfimage->move($destinationpath, $RateCardPdfimg);

                $oldImg = $request->input('hiddenRateCardPdf');
                if ($oldImg && file_exists($destinationpath . '/' . $oldImg)) {
                    unlink($destinationpath . '/' . $oldImg);
                }
            } else {
                $RateCardPdfimg = $existingratecardPdf;
            }
            // Update the category record
            $data = [
                'Category_name' => $request->Category_name,
                'caption' => $request->caption,
                'rating' => $request->rating,
                'city_id' => $request->CityId,
                'hours' => $request->hours,
                'onwards_amount' => $request->onwards_amount,
                'warranty' => $request->warranty,
                'Categories_img' => $img,
                'home_cate_image' => $homeimg,
                'ratecard_pdf' => $RateCardPdfimg,
                'Categories_icon' => $icon,
                'carousel_image' => $carouselimg,
                'Categories_slug' => $slugname,
                'meta_title' => $request->meta_title,
                'meta_keyword' => $request->metaKeyword,
                'meta_description' => $request->metaDescription,
                'meta_head' => $request->head,
                'meta_body' => $request->body,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];

            Categories::where("Categories_id", $request->Categoriesid)->update($data);

            DB::commit();

            Toastr::success('Category updated successfully :)', 'Success');
            return redirect()->route('categories.index');
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
            $state = Categories::where([

                'isDelete' => 0,
                'Categories_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Category deleted successfully :)', 'Success');
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
            $ids = $request->input('Categories_ids', []);
            Categories::whereIn('Categories_id', $ids)->delete();

            Toastr::success('Category deleted successfully :)', 'Success');
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
                Categories::where(['Categories_id' => $request->categoryId])->update(['iStatus' => 0]);
                Toastr::success('Category inactive successfully :)', 'Success');
            } else {
                Categories::where(['Categories_id' => $request->categoryId])->update(['iStatus' => 1]);
                Toastr::success('Category active successfully :)', 'Success');
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
