<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Technicial;
use App\Models\TechnicialService;
use App\Models\TechnicialPincode;
use App\Models\Categories;
use App\Models\JoinAsTechnicial;
use App\Models\StateMaster;
use App\Models\CityMaster;
use App\Models\Pincode;
use App\Models\TechnicialPaymentHistory;
use App\Models\TechnicialLedger;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class TechnicialController extends Controller
{

    public function register(Request $request)
    {

        try {

            $states = StateMaster::orderBy('stateName', 'asc')->get();

            return view('register', compact('states'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function JoinAsTechniciallist()
    {
        try {

            $technicial = JoinAsTechnicial::with('state')
                ->where('onboard', 0)
                ->orderBy('joinastec_id', 'desc')
                ->paginate(config('app.per_page'));
            return view('Technicial.index', compact('technicial'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function onboardtechnicialadd(Request $request)
    {
        try {
            $Categories = Categories::orderBy('Category_name', 'asc')->get();
            $states = StateMaster::orderBy('stateName', 'asc')->get();

            return view('Technicial.technicial_onboard_add', compact('states', 'Categories'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $Categories = Categories::orderBy('Category_name', 'asc')->get();
        $states = StateMaster::orderBy('stateName', 'asc')->get();

        $Technicial = Technicial::where('Technicial_id', $id)->first();
        $TechnicialService = TechnicialService::where('Technicial_id', $id)->get();

        return view('Technicial.edit', compact('Technicial', 'Categories', 'states', 'TechnicialService'));
    }

    public function update(Request $request)
    {

        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required',
                'mobile_no' => 'required',
                'stateid' => 'required',
                'city' => 'required',
            ]);

            $data = [

                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'stateid' => $request->stateid,
                'city' => $request->city,
                'updated_at' => now(),
                'strIP' => $request->ip(),
            ];
            TechnicialService::where('Technicial_id', $request->Technicialid)->delete();

            foreach ($request->categoryid as $category) {
                TechnicialService::create([
                    'Technicial_id' => $request->Technicialid,
                    'Category_id' => $category,
                    'strIP' => $request->ip(),
                ]);
            }

            Technicial::where("Technicial_id", "=", $request->Technicialid)->update($data);
            DB::commit();

            Toastr::success('Technicial updated successfully :)', 'Success');
            return redirect()->route('OnboardTechniciallist')->with('success', 'Technicial Update Successfully.');
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

    public function OnboardTechdelete(Request $request)
    {
        DB::beginTransaction();

        try {
            $Technicial = Technicial::where([
                'iStatus' => 1,
                'isDelete' => 0,
                'Technicial_id' => $request->id
            ])->delete();

            $TechnicialService = TechnicialService::where([
                'iStatus' => 1,
                'isDelete' => 0,
                'Technicial_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('Technicial deleted successfully :)', 'Success');
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

    public function OnboardTechniciallist()
    {
        try {

            $technicial = Technicial::with('state')
                ->orderBy('Technicial_id', 'desc')
                ->paginate(config('app.per_page'));
            return view('Technicial.Onboardtechniciallist', compact('technicial'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function technicialonboard(Request $request, $id)
    {
        try {
            $Categories = Categories::orderBy('Category_name', 'asc')->get();
            $states = StateMaster::orderBy('stateName', 'asc')->get();
            $technicial = JoinAsTechnicial::with('state')->where('joinastec_id', $id)
                ->first();

            return view('Technicial.technicialonboard', compact('technicial', 'states', 'Categories'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }


    public function getPincodesByStateCity(Request $request)
    {
        try {
            $stateId = $request->state_id;
            $cityId = $request->city_id;

            // Fetch pincodes based on the selected state and city
            $pincodes = Pincode::where('state_id', $stateId)
                ->where('city_id', $cityId)
                ->pluck('pincode', 'pin_id');

            return response()->json($pincodes);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function technicialpincode(Request $request, $id)
    {
        try {
            $Pincodes = TechnicialPincode::with('state', 'city', 'Technicial', 'pincodes')
                ->where('Technicial_id', $id)
                ->orderBy('Technicial_Pincode_id', 'desc')
                ->paginate(config('app.per_page'));
            // dd($Pincodes);

            $Categories = Categories::orderBy('Category_name', 'asc')->get();
            $states = StateMaster::orderBy('stateName', 'asc')->get();
            $cities = CityMaster::orderBy('cityName', 'asc')->get();
            $Technicial = Technicial::where('Technicial_id', $id)->first();

            $techServices = TechnicialService::with('category')->where('Technicial_id', $id)->get();
            $techcatid = [];
            foreach ($techServices as $techService) {
                $techcatid[] = [
                    'id' => $techService->Category_id,
                    'name' => optional($techService->category)->Category_name, // Use optional() to avoid errors if category is null
                ];
            }




            return view('Technicial.technicialpincode', compact('Pincodes', 'cities', 'states', 'id', 'techcatid', 'Categories', 'Technicial'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
    public function techadd_pincodeadd(Request $request, $id)
    {


        DB::beginTransaction();

        try {



            foreach ($request->pincode as $pin) {

                TechnicialPincode::create([
                    'state_id' => $request->stateid,
                    'city_id' => $request->areacityId,
                    'Technicial_id' => $id,
                    'Pincode_id' => $pin,
                    'created_at' => now(),
                    'strIP' => $request->ip(),
                ]);
            }


            DB::commit();
            Toastr::success('Pincode Add successfully :)', 'Success');
            return redirect()->back()->with('success', 'Pincode Add Successfully.');
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

    public function techadd_serviceadd(Request $request, $id = null)
    {


        DB::beginTransaction();

        try {

            $technician = new Technicial();
            $technician->name = $request->name;
            $technician->email = $request->email;
            $technician->mobile_no = $request->mobile_no;
            $technician->password = Hash::make($request->password);
            $technician->stateid = $request->stateid;
            $technician->city = $request->city;
            $technician->created_at = now();
            $technician->strIP = $request->ip();
            $technician->save();

            if ($id != "") {
                $onboardtechnician = JoinAsTechnicial::where('joinastec_id', $id)->update(['onboard' => 1]);
                $joinastech_del = JoinAsTechnicial::where('joinastec_id', $id)->delete();
            }
            // $joinastech_del = JoinAsTechnicial::destroy($id);

            foreach ($request->categoryid as $category) {

                TechnicialService::create([
                    'Technicial_id' => $technician->Technicial_id,
                    'Category_id' => $category,
                    'created_at' => now(),
                    'strIP' => $request->ip(),
                ]);
            }
            
            $message = "Dear Customer, your login ID is {$request->mobile_no} and your password is {$request->password}. Please keep this information confidential. Download our app: https://auraclap.com/app. Regards, Team The Auraclap";
            $mobile = $request->mobile_no;
            $technician->WhatsappMessage($mobile, $message);
            
            // $sendEmailDetails = DB::table('sendemaildetails')->where(['id' => 9])->first();

           
            // $msg = [
            //     'FromMail' => $sendEmailDetails->strFromMail,
            //     'Title' => $sendEmailDetails->strTitle,
            //     'ToEmail' => $request->email,
            //     'Subject' => $sendEmailDetails->strSubject,
            // ];
            // $data = [
            //     'name' => $request->name,
            //     'email' => $request->email,
            //     'password' => $request->password, // plain password (if needed)
            // ];

          
            // Mail::send('emails.techloginmail', ['data' => $data], function ($message) use ($msg) {
            //     $message->from($msg['FromMail'], $msg['Title']);
            //     $message->to($msg['ToEmail'])->subject($msg['Subject']);
            // });



            DB::commit();
            Toastr::success('Technicial or service Add successfully :)', 'Success');
            return redirect()->route('OnboardTechniciallist')->with('success', 'Technicial or service Add Successfully.');
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


    public function registerstore(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([

                'name' => 'required',
                'email' => 'required|email|unique:join_as_technicial',
                'mobile_no' => 'required|numeric|digits:10|unique:join_as_technicial',

            ]);


            JoinAsTechnicial::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_no' => $request->mobile_no,
                'stateid' => $request->stateid,
                'city' => $request->city,
                'created_at' => now(),
                'strIP' => $request->ip(),
            ]);

            DB::commit();

            Toastr::success('Registration successfully :)', 'Success');
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

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $technicial = TechnicialPincode::where([
                'iStatus' => 1,
                'isDelete' => 0,
                'Technicial_Pincode_id' => $request->id
            ])->delete();

            DB::commit();

            Toastr::success('State deleted successfully :)', 'Success');
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

    public function store(Request $request)
    {

        // $request->validate([
        //     'Technicial_id' => 'required|exists:Technicial,Technicial_id',
        //     'Transaction_reference_id' => 'required|string|unique:Technicial_payment_history,Transaction_reference_id',
        //     'amount' => 'required|numeric|min:1',
        // ]);

        $technicialId = $request->Technicial_id;
        $amount = $request->amount;

        // Step 1: Save payment in Technicial_payment_history
        $payment = TechnicialPaymentHistory::create([
            'Technical_id' => $technicialId,
            'Transaction_reference_id' => $request->transactionrefid,
            'amount' => $amount,
            'strIP' => $request->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Step 2: Get the latest ledger record
        $lastLedger = TechnicialLedger::where('Technicial_id', $technicialId)
            ->orderBy('created_at', 'desc')
            ->first();

        // Step 3: Calculate ledger balances
        if ($lastLedger) {
            $openingBal = $lastLedger->closing_bal;
        } else {
            $openingBal = 0;
        }

        $cr = 0; // Credit amount (since it's a payment, we assume no credit)
        $dr = $amount; // Debit amount
        $closingBal = $openingBal - $dr; // New closing balance

        // Step 4: Insert new record in Technicial_ledger
        TechnicialLedger::create([
            'Technicial_id' => $technicialId,
            'comments' => $request->comments,
            'opening_bal' => $openingBal,
            'Cr' => $cr,
            'Dr' => $dr,
            'closing_bal' => $closingBal,
            'created_at' => now(),
            'updated_at' => now(),
            'strIP' => request()->ip(),
        ]);

        Toastr::success('Technicial ledger updated successfully :)', 'Success');
        return redirect()->back();
    }

    public function technicialledger(Request $request, $id)
    {
        try {

            $ledgers = TechnicialLedger::with('technician')
                ->where('Technicial_id', $id)
                ->orderBy('Technicial_ledger_id', 'desc')
                ->paginate(config('app.per_page'));
            // dd($ledgers);
            return view('Technicial.technicialledger', compact('ledgers'));
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
}
