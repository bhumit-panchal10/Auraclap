<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use App\Models\Customer;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;



class ManageHotelHostelsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $managehotelhostal = Customer::select('*')
                ->where('role',1)
                ->orderBy('Customer_id', 'asc')
                ->paginate(config('app.per_page'));

            return view('managehotelhostels.index', compact('managehotelhostal'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function add()
    {
        try {
            return view('managehotelhostels.add');
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
                'contactname' => 'required',
                'email' => 'required|email|unique:Customer,email',
                'hotelhostal_id' => 'required',
                'mobile_no' => 'required|digits:10|unique:Customer,Customer_phone'

            ]);


            $managehotelhostal = Customer::create([

                'Customer_name' => $request->contactname,
                'hotelhostel_name' => $request->hotelhostal_id,
                'Customer_phone' => $request->mobile_no,
                'email' => $request->email,
                'role' => 1,
                'company_name' => $request->company_name,
                'Gst_no' => $request->Gst_no,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);
            $sendEmailDetails = DB::table('sendemaildetails')->where(['id' => 9])->first();

            // Prepare email data
            $msg = [
                'FromMail' => $sendEmailDetails->strFromMail,
                'Title' => $sendEmailDetails->strTitle,
                'ToEmail' => $managehotelhostal->email,
                'Subject' => $sendEmailDetails->strSubject,
            ];
            $data = ["contactname" => $managehotelhostal->Customer_name];

            // Send email
            Mail::send('emails.Technicialemail', ['data' => $data], function ($message) use ($msg) {
                $message->from($msg['FromMail'], $msg['Title']);
                $message->to($msg['ToEmail'])->subject($msg['Subject']);
            });

            // Commit the transaction
            DB::commit();

            Toastr::success('Hotel/Hostels created successfully and Email Sent!', 'Success');

            return redirect()->route('manage_hotel_hostels.index')->with('success', 'Hotel/Hostels Created Successfully.');
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

            $managehotelhostal = Customer::where('Customer_id', $id)->first();


            return view('managehotelhostels.edit', compact('managehotelhostal'));
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function Technicialmailsend(Request $request, $id)
    {

        try {

            $managehotelhostal = Customer::where('Customer_id', $id)->first();

            $sendEmailDetails = DB::table('sendemaildetails')->where(['id' => 9])->first();

            $msg = [
                'FromMail' => $sendEmailDetails->strFromMail,
                'Title' => $sendEmailDetails->strTitle,
                'ToEmail' => $managehotelhostal->email,
                'Subject' => $sendEmailDetails->strSubject,
            ];
            $data = array(
                "contactname" => $managehotelhostal->Customer_name
            );
            Mail::send('emails.Technicialemail', ['data' => $data], function ($message) use ($msg) {
                $message->from($msg['FromMail'], $msg['Title']);
                $message->to($msg['ToEmail'])->subject($msg['Subject']);
            });
            Toastr::success('Mail Send Successfully', 'Success');
            return redirect()->back();
        } catch (\Throwable $th) {

            DB::rollBack();

            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    public function update(Request $request)
    {


        try {
            $request->validate([
                'Customer_name' => 'required',
                'Customer_id' => 'required',
                'Customer_phone' => 'required',
                'email' => 'required'


            ]);

            $data = [

                'Customer_name' => $request->Customer_name,
                'hotelhostel_name' => $request->Customer_id,
                'Customer_phone' => $request->Customer_phone,
                'email' => $request->email,
                'company_name' => $request->company_name,
                'Gst_no' => $request->Gst_no,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];


            Customer::where("Customer_id", "=", $request->Customer_id)->update($data);

            DB::commit();

            Toastr::success('Hotel/Hostels updated successfully :)', 'Success');
            return redirect()->route('manage_hotel_hostels.index')->with('success', 'Hotel/Hostels Update Successfully.');
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
            $Image = Customer::where([

                'isDelete' => 0,
                'Customer_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Hotel/Hostels deleted successfully :)', 'Success');
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
            Customer::whereIn('Customer_id', $ids)->delete();

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
                Customer::where(['Customer_id' => $request->managehothosId])->update(['iStatus' => 0]);
                Toastr::success('Hotel/Hostel inactive successfully :)', 'Success');
            } else {
                Customer::where(['Customer_id' => $request->managehothosId])->update(['iStatus' => 1]);
                Toastr::success('Hotel/Hostel active successfully :)', 'Success');
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
