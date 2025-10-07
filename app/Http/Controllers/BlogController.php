<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Brian2694\Toastr\Facades\Toastr;

use function Symfony\Component\Clock\now;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $Data = Blog::orderBy('blogId', 'DESC')->where(['blogs.isDelete' => 0, 'blogs.iStatus' => 1])->paginate(25);
        
        return view('blogs.index', compact('Data'));
    }

    public function createview()
    {
        return view('blogs.add');
    }

    public function store(Request $request)
    {
        // dd($request);
        
        try {
            $img = "";
            if ($request->hasFile('blogImage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('blogImage');
                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/Blog/';
                if (!file_exists($destinationpath)) {
                    mkdir($destinationpath, 0755, true);
                }
                $image->move($destinationpath, $img);
            }

            $SlugName = Str::slug($request->blogTitle);


            Blog::create([
                'blogTitle' => $request->blogTitle,
                'slugname' => $SlugName,
                'blogImage' => $img,
                'blogDescription' => $request->blogDescription,
                'metaTitle' => $request->metaTitle,
                'metaKeyword' => $request->metaKeyword,
                'metaDescription' => $request->metaDescription,
                'blogDate' => date('Y-m-d'),
                'head' => $request->head,
                'body' => $request->body,
                'strIP' => $request->ip(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->route('blog.index')->with('success', 'Blog Created Successfully.');
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
            Toastr::error('Failed to create Job Category: ' . $th->getMessage(), 'Error');
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }


    public function editview(Request $request, $id)
    {
        $Data = Blog::where(['iStatus' => 1, 'isDelete' => 0, 'blogId' => $id])->first();

        return view('blogs.edit', compact('Data'));
    }


    public function update(Request $request)
    {
     
        try {
            $img = "";
            if ($request->hasFile('blogImage')) {
                $root = $_SERVER['DOCUMENT_ROOT'];
                $image = $request->file('blogImage');
                $img = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                $destinationpath = $root . '/upload/Blog/';
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

            $SlugName = Str::slug($request->blogTitle);
            
            Blog::where(['iStatus' => 1, 'isDelete' => 0, 'blogId' => $request->blogId])
                ->update([
                    'blogTitle' => $request->blogTitle,
                    'slugname' => $SlugName,
                    'blogImage' => $img,
                    'blogDescription' => $request->blogDescription,
                    'metaTitle' => $request->metaTitle,
                    'metaKeyword' => $request->metaKeyword,
                    'metaDescription' => $request->metaDescription,
                    'head' => $request->head,
                    'body' => $request->body,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            return redirect()->route('blog.index')->with('success', 'Blog Updated Successfully.');
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

    public function delete($Id)
    {
        $delete = DB::table('blogs')->where(['iStatus' => 1, 'isDelete' => 0, 'blogId' => $Id])->first();
        $root = $_SERVER['DOCUMENT_ROOT'];
        $destinationpath = $root . '/upload/Blog/';

        if (file_exists($destinationpath . $delete->blogImage)) {
            unlink($destinationpath . $delete->blogImage);
        }

        DB::table('blogs')->where(['iStatus' => 1, 'isDelete' => 0, 'blogId' => $Id])->delete();
        return redirect()->route('blog.index')->with('success', 'Blog Deleted Successfully!.');
    }
    
    public function deleteselected(Request $request)
    {

        try {

            $ids = $request->input('blog_ids', []);
            $blogs = Blog::whereIn('blogId', $ids)->get();

            $root = $_SERVER['DOCUMENT_ROOT'];
            $destinationPath = $root . '/upload/Blog/';

            foreach ($blogs as $blog) {
                if ($blog->blogImage && file_exists($destinationPath . $blog->blogImage)) {
                    unlink($destinationPath . $blog->blogImage);
                }
                DB::table('blogs')->where(['iStatus' => 1, 'isDelete' => 0, 'blogId' => $blog->blogId])->delete();
            }

            Toastr::success('Blog deleted successfully :)', 'Success');

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
