<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CityMaster;
use App\Models\StateMaster;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PushNotificationController;
use App\Models\Customer;
use App\Models\Technicial;
use App\Models\AreaMaster;
use App\Models\CMSMaster;
use App\Models\TechnicialLedger;
use App\Models\Order;
use App\Models\Managerate;
use App\Models\OrderDetail;
use App\Models\TechnicialPincode;
use App\Models\Pincode;
use App\Models\CustomerReview;
use App\Models\OrderInvoice;
use App\Models\Technicialwallet;
use App\Models\TechnicialWalletPayment;
use App\Models\VideoMaster;
use GuzzleHttp\Client;
use App\Models\Vendor;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class TechnicialApiController extends PushNotificationController

// class DriverApiController extends PushNotificationController
{

    public function statelist(Request $request)
    {
        try {

            $listOfStates = StateMaster::select(
                "stateId",
                "stateName"
            )->orderBy('stateName', 'asc')->where(['iStatus' => 1, 'isDelete' => 0])->get();

            return response()->json([
                'success' => true,
                'message' => "successfully fetched StateList...",
                'data' => $listOfStates,
            ], 200);
        } catch (\Throwable $th) {

            // If there's an error, rollback any database transactions and return an error response.

            DB::rollBack();

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function video_list(Request $request)
    {
        try {
            $listOfVideo = VideoMaster::select(
                "id",
                "title",
                "link"
            )->orderBy('id', 'asc')->get();

            return response()->json([
                'success' => true,
                'message' => "successfully fetched videoList...",
                'data' => $listOfVideo,
            ], 200);
        } catch (\Throwable $th) {

            // If there's an error, rollback any database transactions and return an error response.

            DB::rollBack();

            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function login(Request $request)
    {
        try {

            $request->validate([
                'mobile_no' => 'nullable|digits_between:10,15',
                'password' => 'required',
                'firebaseDeviceToken' => 'required',
            ]);

            if (!$request->mobile_no) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide mobile number.',
                ], 422);
            }

            if ($request->mobile_no) {
                $input = $request->mobile_no;
                $fieldType = 'mobile_no';
            }

            $credentials = [
                $fieldType => $input,
                'password' => $request->password,
            ];

            // Fetch the vendor by email or mobile
            $Technicial = Technicial::where($fieldType, $input)->first();


            if (!$Technicial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial not found.',
                ], 404);
            }

            // Attempt to authenticate using the provided credentials
            if (Auth::guard('technicialapi')->attempt($credentials)) {
                $authenticatedVendor = Auth::guard('technicialapi')->user();

                $authenticatedVendor->firebaseDeviceToken = $request->firebaseDeviceToken;
                $authenticatedVendor->save();
                $data = [
                    "Technicial_id" => $authenticatedVendor->Technicial_id,
                    "name" => $authenticatedVendor->name,
                    "email" => $authenticatedVendor->email,
                    "mobile_no" => $authenticatedVendor->mobile_no,
                    "stateid" => $authenticatedVendor->stateid,
                    "city" => $authenticatedVendor->city,
                    "iStatus" => $authenticatedVendor->iStatus,
                    "strIP" => $authenticatedVendor->strIP,
                    "created_at" => $authenticatedVendor->created_at,
                    "updated_at" => $authenticatedVendor->updated_at,
                ];

                // Generate JWT token
                $token = JWTAuth::fromUser($authenticatedVendor);

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'Technicialdetail' => $data,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials. Please check your input and password.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            // Handle unexpected errors
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function RefreshfirebaseDeviceToken(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'Technicial_id' => 'required|integer|exists:Technicial,Technicial_id',
                'firebaseDeviceToken' => 'required|string',
            ]);

            // Find the technicial
            $technicial = Technicial::find($request->Technicial_id);

            if (!$technicial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial not found.',
                ], 404);
            }

            // Update firebase token
            $technicial->firebaseDeviceToken = $request->firebaseDeviceToken;
            $technicial->save();

            return response()->json([
                'success' => true,
                'message' => 'Firebase Device Token updated successfully.',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function change_password(Request $request)
    {
        try {

            if (Auth::guard('technicialapi')->check()) {

                $request->validate(
                    [
                        "Technicial_id" => 'required',
                        "old_password" => 'required',
                        "new_password" => 'required',
                        "confirm_new_password" => 'required|same:new_password'
                    ],
                    [
                        'Technicial_id.required' => 'Technicial ID is required.',
                        'old_password.required' => 'Old Password is required.',
                        'new_password.required' => 'New Password is required.',
                        'new_password.same' => 'New password and confirmation password must match.'
                    ]
                );

                $Technicial =  Technicial::where(['iStatus' => 1, 'isDelete' => 0, 'Technicial_id' => $request->Technicial_id])->first();
                if (!$Technicial) {
                    return response()->json([
                        'success' => false,
                        'message' => "Technicial not found."
                    ]);
                }

                if (Hash::check($request->old_password, $Technicial->password)) {

                    $newpassword = $request->new_password;
                    $confirmpassword = $request->confirm_new_password;

                    if ($newpassword == $confirmpassword) {

                        $Technicial->update([
                            'password' => Hash::make($confirmpassword),
                            'is_changepasswordfirsttime' => 1
                        ]);
                        return response()->json([
                            'success' => true,
                            'message' => 'Password updated successfully...',
                        ], 200);
                    } else {
                        return response()->json([
                            'success' => false,
                            'message' => 'password and confirm password does not match',
                        ], 200);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Current Password does not match',
                    ], 200);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function forgot_password(Request $request)
    {
        try {
           
                $request->validate([
                    'mobile_no' => 'required',
                ]);

                // Find the vendor by email
                $Technicial = Technicial::where(['iStatus' => 1, 'isDelete' => 0])
                    ->where('mobile_no', $request->mobile_no)
                    ->first();

                if (!$Technicial) {
                    return response()->json([
                        'success' => false,
                        'message' => "Technicial not found."
                    ], 404);
                }

                //$otp = rand(1000, 9999);
                $otp = '1234';
                $expiry_date = now()->addMinutes(5);

                // Update the OTP and expiry in the database
                $Technicial->update([
                    'otp' => $otp,
                    'expiry_time' => $expiry_date,
                ]);

                // // Send the email
                // $sendEmailDetails = DB::table('sendemaildetails')->where(['id' => 9])->first();
                // $msg = [
                //     'FromMail' => $sendEmailDetails->strFromMail,
                //     'Title' => $sendEmailDetails->strTitle,
                //     'ToEmail' => $Technicial->email,
                //     'Subject' => $sendEmailDetails->strSubject,
                // ];

                // $data = array(
                //     'otp' => $otp,
                //     "name" => $Technicial->name
                // );


                // Mail::send('emails.forgotPassword', ['data' => $data], function ($message) use ($msg) {
                //     $message->from($msg['FromMail'], $msg['Title']);
                //     $message->to($msg['ToEmail'])->subject($msg['Subject']);
                // });

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully.'
                ], 200);
           
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function forgot_password_verifyOTP(Request $request)
    {
        try {
            
                $request->validate([

                    'otp' => 'required'
                ]);

                $password = mt_rand(100000, 999999);


                $Technicial = Technicial::where([

                    'otp' => $request->otp
                ])->first();

                if (!$Technicial) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP is invalid. Please enter a valid OTP.',
                    ], 400);
                }

                // Check if the OTP has expired
                $expiryTime = Carbon::parse($Technicial->expiry_time);
                if (now()->greaterThan($expiryTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'OTP has expired.',
                    ], 400);
                }

                // Mark the OTP as verified and update the last login time
                $Technicial->update([
                    // 'isOtpVerified' => 1,
                    'password' =>  Hash::make($password),
                    'last_login' => now(),
                ]);

                $data = array(
                    'password' => $password,
                    "name" =>  $Technicial->name,
                    "mobile_no" =>  $Technicial->mobile_no


                );

                $sendEmailDetails = DB::table('sendemaildetails')->where(['id' => 9])->first();
                $msg = array(
                    'FromMail' => $sendEmailDetails->strFromMail,
                    'Title' => $sendEmailDetails->strTitle,
                    'ToEmail' => $Technicial->email,
                    'Subject' => $sendEmailDetails->strSubject
                );

                Mail::send('emails.forgotpasswordmail', ['data' => $data], function ($message) use ($msg) {
                    $message->from($msg['FromMail'], $msg['Title']);
                    $message->to($msg['ToEmail'])->subject($msg['Subject']);
                });
                // $vendorDetails = $vendor->only(['vendor_id','vendorname', 'isOtpVerified', 'login_id', 'vendormobile', 'email', 'businessname', 'businessaddress','vendorsocialpage','businesscategory','businessubcategory','is_changepasswordfirsttime']);
                return response()->json([
                    'success' => true,
                    'message' => 'OTP is valid.',
                    // 'vendor_details' => $vendorDetails,

                ], 200);
           
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function logout(Request $request)
    {

        try {
            // Validate the vendorid passed in the request
            $request->validate([
                'Technicial_id' => 'required|integer'
            ]);
            // Optionally, fetch the vendor by vendorid (if you need to check or log something)
            $Technicial = Technicial::find($request->Technicial_id);
            if (!$Technicial) {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial not found.'
                ], 404);
            }
            Auth::logout();
            session()->flush();
            // Optional: If you want to send the vendor details in the response
            return response()->json([
                'success' => true,
                'message' => 'Successfully logged out.',
                'Technicial_id' => $Technicial->Technicial_id,  // Including the vendorid in the response
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token. Unable to logout.',
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function technicialdashboard(Request $request)
    {

        try {
            if (Auth::guard('technicialapi')->check()) {

                $request->validate([
                    'Technicial_id' => 'required|integer'
                ]);
                $Technicialbal = TechnicialLedger::where('Technicial_id', $request->Technicial_id)
                    ->orderBy('Technicial_ledger_id', 'DESC')
                    ->first();

                $balance = $Technicialbal->closing_bal ?? 0;

                $assignedPincodes = Pincode::whereIn('pin_id', function ($query) use ($request) {
                    $query->select('Pincode_id')
                        ->from('Technicial_Pincode')
                        ->where('Technicial_id', $request->Technicial_id);
                })->pluck('pincode')->toArray();


                // Fetch available orders based on assigned pincodes
                $Availableorder = Order::where('order_status', 0)
                    ->whereIn('Pincode', $assignedPincodes)
                    ->count();
                $Ongoingorder = Order::where('order_status', 1)
                    ->where('Technicial_id', $request->Technicial_id)
                    ->count();

                return response()->json([
                    'success' => true,
                    'message' => 'data fetch Successfully.',
                    'Balance' => $balance,
                    'Availableorder' => $Availableorder,
                    'Ongoingorder' => $Ongoingorder
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token. Unable to logout.',
            ], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function Technicial_startwork_photoUpload(Request $request)
    {
        try {
            if (Auth::guard('technicialapi')->check()) {
                // Validate input
                $request->validate([
                    'iOrderId' => 'required|exists:order,iOrderId',
                    'Technicial_id' => 'required',
                    'photo' => 'required', // max 5MB
                ]);

                // Fetch the order
                $Technicialorder = Order::where('iOrderId', $request->iOrderId)->first();

                if ($request->hasFile('photo')) {
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    $image = $request->file('photo');
                    $imgName = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                    $destinationPath = $root . '/upload/photo/';

                    // Create directory if not exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    // Delete the old photo if exists
                    if ($Technicialorder->photo && file_exists($destinationPath . $Technicialorder->photo)) {
                        unlink($destinationPath . $Technicialorder->photo);
                    }

                    // Move new photo
                    $image->move($destinationPath, $imgName);

                    // Update order with new technician and photo
                    $Technicialorder->update([
                        'Technicial_id' => $request->Technicial_id,
                        'photo' => $imgName,
                        'start_work' => 1,

                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploaded and work started successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function Technicial_endwork_photoUpload(Request $request)
    {
        try {
            if (Auth::guard('technicialapi')->check()) {
                // Validate input
                $request->validate([
                    'iOrderId' => 'required|exists:order,iOrderId',
                    'Technicial_id' => 'required',
                    'service_photo_1' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                    'service_photo_2' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
                ]);
    
                // Fetch the order
                $Technicialorder = Order::where('iOrderId', $request->iOrderId)->first();
    
                // Handle service_photo_1
                if ($request->hasFile('service_photo_1')) {
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    $image = $request->file('service_photo_1');
                    $imgName1 = time() . '_1_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = $root . '/upload/servicephoto1/';
    
                    // Create directory if not exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
    
                    // Delete old photo
                    if ($Technicialorder->service_photo_1 && file_exists($destinationPath . $Technicialorder->service_photo_1)) {
                        unlink($destinationPath . $Technicialorder->service_photo_1);
                    }
    
                    // Move and save new photo
                    $image->move($destinationPath, $imgName1);
                    $Technicialorder->service_photo_1 = $imgName1;
                }
    
                // Handle service_photo_2
                if ($request->hasFile('service_photo_2')) {
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    $image = $request->file('service_photo_2');
                    $imgName2 = time() . '_2_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = $root . '/upload/servicephoto2/';
    
                    // Create directory if not exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }
    
                    // Delete old photo
                    if ($Technicialorder->service_photo_2 && file_exists($destinationPath . $Technicialorder->service_photo_2)) {
                        unlink($destinationPath . $Technicialorder->service_photo_2);
                    }
    
                    // Move and save new photo
                    $image->move($destinationPath, $imgName2);
                    $Technicialorder->service_photo_2 = $imgName2;
                }
    
                // Save changes
                $Technicialorder->save();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Photo uploaded and work end successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    // public function available_or_complete_order_list(Request $request)
    // {
    //         try {
    //             $request->validate([
    //                 'Technicial_id' => 'required|integer',
    //                 'status' => 'required'
    //             ]);
        
    //             // Get assigned pincodes
    //             $assignedPincodes = Pincode::whereIn('pin_id', function ($query) use ($request) {
    //                 $query->select('Pincode_id')
    //                     ->from('Technicial_Pincode')
    //                     ->where('Technicial_id', $request->Technicial_id);
    //             })->pluck('pincode')->toArray();
        
    //             // Get allowed category IDs
    //             $technicialCategoryIds = \App\Models\TechnicialService::where('Technicial_id', $request->Technicial_id)
    //                 ->where('isDelete', 0)
    //                 ->pluck('Category_id')
    //                 ->toArray();
        
    //             // Get orders
    //             if ($request->status == 0) {
    //                 $order = OrderDetail::with('order.slot', 'category', 'subcategory', 'order')
    //                     ->where('order_status', 0)
    //                     ->whereHas('order', function ($query) use ($assignedPincodes) {
    //                         $query->whereIn('isPayment', [0, 1])
    //                               ->whereIn('Pincode', $assignedPincodes);
    //                     })
    //                     ->whereHas('category', function ($query) use ($technicialCategoryIds) {
    //                         $query->whereIn('Categories_id', $technicialCategoryIds);
    //                     })
    //                     ->get();
    //             } else {
    //                 $order = OrderDetail::with('order.slot', 'category', 'subcategory', 'order')
    //                     ->where('order_status', 1)
    //                      ->whereHas('order', function ($query) {
    //                             $query->whereIn('isPayment', [0, 1]);
    //                         })
    //                     ->where('Technicial_id', $request->Technicial_id)
    //                     ->get();
    //             }
        
    //             // Append full image URLs
    //           $order->transform(function ($detail) {
    //               if ($detail) {
    //                     if (!empty($detail->service_photo_1) && !str_starts_with($detail->service_photo_1, 'http')) {
    //                         $detail->service_photo_1 = "https://admin.auraclap.com/upload/servicephoto1/" . $detail->service_photo_1;
    //                     }
    //                 }
                    
    //                 if ($detail) {
    //                     if (!empty($detail->service_photo_2) && !str_starts_with($detail->service_photo_2, 'http')) {
    //                         $detail->service_photo_2 = "https://admin.auraclap.com/upload/servicephoto2/" . $detail->service_photo_2;
    //                     }
    //                 }
    //                 // Handle subcategory image
    //                 if ($detail->subcategory) {
    //                     if (!empty($detail->subcategory->SubCategories_img) && !str_starts_with($detail->subcategory->SubCategories_img, 'http')) {
    //                         $detail->subcategory->SubCategories_img = "https://admin.auraclap.com/upload/subcategory-images/" . $detail->subcategory->SubCategories_img;
    //                     }
    //                 }
                
    //                 // Handle category image
    //                 if ($detail->category) {
    //                     if (!empty($detail->category->Categories_img) && !str_starts_with($detail->category->Categories_img, 'http')) {
    //                         $detail->category->Categories_img = "https://admin.auraclap.com/upload/category-image/" . $detail->category->Categories_img;
    //                     }
    //                 }
                
    //                 return $detail;
    //             });


        
    //             // Return response
    //             if ($order->isEmpty()) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'order not found.',
    //                 ], 400);
    //             }
        
    //             return response()->json([
    //                 'success' => true,
    //                 'data' => $order,
    //                 'message' => 'order fetched successfully.',
    //             ], 200);
        
    //         } catch (ValidationException $e) {
    //             return response()->json(['errors' => $e->errors()], 422);
    //         } catch (\Throwable $th) {
    //             return response()->json(['error' => $th->getMessage()], 500);
    //         }
    //     }
    
    
    public function Technicial_endverifyOTP(Request $request)
    {
        try {
            if (!Auth::guard('technicialapi')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
    
            // ✅ Validate input
            $request->validate([
                'iOrderId'                    => 'required|exists:order,iOrderId',         // change to orders if needed
                'Technicial_id'               => 'required|exists:Technicial,Technicial_id', // change to technicials if needed
                'end_otp'                     => 'required',
                'extra_add_by_tech_gst_amt'   => 'required|numeric|min:0',
            ]);
    
            // ✅ Load order (with required relations)
            $Technicialorder = Order::with('orderdetail', 'primaryorder')
                ->where('iOrderId', $request->iOrderId)
                ->first();
    
            if (!$Technicialorder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Enter valid order.',
                ], 400);
            }
    
            // ✅ OTP check
            if ($Technicialorder->end_otp != $request->end_otp) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP is invalid. Please enter a valid OTP.',
                ], 400);
            }
    
            DB::beginTransaction();
    
            // ✅ Mark order complete
            $Technicialorder->update([
                'Technicial_id' => $request->Technicial_id,
                'order_status'  => 2,
                'start_work'    => 2,
            ]);
    
            // ✅ Create invoice record
            OrderInvoice::create([
                'order_id' => $Technicialorder->iOrderId,
                'date'     => now(),
            ]);
    
            // ─────────────────────────────────────────────────────────────
            // WALLET / LEDGER
            // ─────────────────────────────────────────────────────────────
    
            // Start from last known closing balance
            $lastLedger  = TechnicialLedger::where('Technicial_id', $request->Technicial_id)
                ->orderBy('created_at', 'desc')
                ->first();
    
            $openingBal  = $lastLedger ? (float) $lastLedger->closing_bal : 0.0;
            $closingBal  = $openingBal;
    
            $walletCredited   = false;
            $creditedAmount   = 0.0;
            $gstDebited       = false;
            $debitedGSTAmount = 0.0;
    
            // 1) CREDIT only for online (1) / hotel-hostel (3)
            $paymentMode = (int) ($Technicialorder->primaryorder->payment_mode ?? 0);
            if (in_array($paymentMode, [1, 3], true)) {
                $orderAmount = (float) ($Technicialorder->iAmount   ?? 0);
                $iDiscount   = (float) ($Technicialorder->iDiscount ?? 0);
                $earnings    = $orderAmount - $iDiscount;
    
                $cr         = max(0, $earnings); // guard negative
                $closingBal = $openingBal + $cr;
    
                TechnicialLedger::create([
                    'Technicial_id' => $request->Technicial_id,
                    'comments'      => 'Order completed (credit) - ID: ' . $Technicialorder->iOrderId,
                    'opening_bal'   => $openingBal,
                    'Cr'            => $cr,
                    'Dr'            => 0,
                    'closing_bal'   => $closingBal,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                    'strIP'         => $request->ip(),
                ]);
    
                $walletCredited = true;
                $creditedAmount = $cr;
                // next operations will continue from this new closingBal
            }
    
            // 2) Always DEBIT GST extra (cash or online)
            $gstExtra = (float) ($request->extra_add_by_tech_gst_amt ?? 0);
            if ($gstExtra > 0) {
                $debitOpening = $closingBal;
                $dr           = $gstExtra;
                $closingBal   = $debitOpening - $dr;
    
                TechnicialLedger::create([
                    'Technicial_id' => $request->Technicial_id,
                    'comments'      => 'GST extra (debit) for order ID: ' . $Technicialorder->iOrderId,
                    'opening_bal'   => $debitOpening,
                    'Cr'            => 0,
                    'Dr'            => $dr,
                    'closing_bal'   => $closingBal,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                    'strIP'         => $request->ip(),
                ]);
    
                $gstDebited       = true;
                $debitedGSTAmount = $gstExtra;
            }
    
            DB::commit();
    
            // ✅ Push notification
            $this->notification([
                "Technicial_id" => $request->Technicial_id ?? '0',
                "customer_id"   => $Technicialorder->iCustomerId ?? '0',
                "title"         => "AuraClap",
                "body"          => "Thank you for purchasing service from AURACLAP.we are happy to have you as our member.",
                "order_id"      => $request->iOrderId ?? '0',
                "type"          => "order_complete",
                "service"       => "Customer",
                "flag"          => 1,
            ]);
    
            return response()->json([
                'success'            => true,
                'message'            => 'OTP is valid. Order marked as complete.',
                'wallet_credited'    => $walletCredited,
                'credited_amount'    => $creditedAmount,
                'gst_debited'        => $gstDebited,
                'debited_gst_amount' => $debitedGSTAmount,
                'new_wallet_balance' => $closingBal, // reflects credit (if any) minus GST debit
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    
    
     public function add_by_tech_service(Request $request)
     {
        // dd($request);
        try {


            $request->validate([
                "iOrderId" => 'required',
                "order_primary_id" => 'required',
                "iCustomerId" => 'required',
                "category_id" => 'required',
                "subcategory_id" => 'required',
                "qty" => 'required',
                "rate" => 'required',
                "amount" => 'required',
                "net_amount" => 'required',
                "GSTAmount" => 'required',


            ]);
            $managerate = Managerate::where('subcate_id', $request->subcategory_id)->first();
           
            $rate_card_id = $managerate->rate_id ?? 0; 
            
            if (!$managerate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate not found',
                ], 409); 
            }
            $techservicedata = array(

                "iOrderId" => $request->iOrderId,
                "order_primary_id" => $request->order_primary_id,
                "iCustomerId" => $request->iCustomerId,
                "category_id" => $request->category_id,
                "subcategory_id" => $request->subcategory_id,
                "qty" => $request->qty,
                "Ratecard_id" => $rate_card_id ?? 0,
                "rate" => $request->rate,
                "technicial_add_extra_service" => 1,
                "amount" => $request->amount,
                "net_amount" => $request->net_amount,
                "GSTAmount" => $request->GSTAmount,
                'strIP' => $request->ip(),
            );

            $OrderDetail = OrderDetail::create($techservicedata);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Service Add Successfully.',
            ], 200);
        } catch (ValidationException $e) {
            DB::rollBack();
            // Format validation errors as a single string
            $errorMessage = implode(', ', Arr::flatten($e->errors()));

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    
    

    public function available_or_complete_order_list(Request $request)
    {
        try {
            $request->validate([
                'Technicial_id' => 'required|integer',
                'status'        => 'required|in:0,1', // 0=available, 1=ongoing
            ]);
    
            // Tech's pincodes (if your Pincode lives on primary_orders, we filter in whereHas('primaryorder'))
            $assignedPincodes = Pincode::whereIn('pin_id', function ($q) use ($request) {
                    $q->select('Pincode_id')
                      ->from('Technicial_Pincode')
                      ->where('Technicial_id', $request->Technicial_id);
                })->pluck('pincode')->toArray();
    
            // Tech's allowed categories
            $technicialCategoryIds = \App\Models\TechnicialService::where('Technicial_id', $request->Technicial_id)
                ->where('isDelete', 0)
                ->pluck('Category_id')
                ->toArray();
    
            // Base query on ORDER so we can read service_photo_1/2
            $ordersQuery = Order::query()
                ->with([
                    // slot & isPayment now come from PRIMARY ORDER
                    'primaryorder.slot',
                    // details hold category/subcategory
                    'orderdetail.subcategory',
                    'orderdetail.category',
                ])
                ->orderBy('iOrderId', 'desc');
    
            if ((int)$request->status === 0) {
                // AVAILABLE: unassigned, primary isPayment ok, in tech area, has a detail in served categories
                $ordersQuery
                    ->where('order_status', 0)
                    ->where(function ($q) {
                        $q->whereNull('Technicial_id')->orWhere('Technicial_id', 0);
                    })
                    ->whereHas('primaryorder', function ($q) use ($assignedPincodes) {
                        $q->whereIn('isPayment', [0, 1])
                          ->whereIn('Pincode', $assignedPincodes); // if Pincode is stored on primary_orders
                    })
                    ->whereHas('orderdetail', function ($q) use ($technicialCategoryIds) {
                        $q->whereIn('category_id', $technicialCategoryIds);
                    })
                    // (optional) only load the details in the tech's categories for available view
                    ->with(['orderdetail' => function ($q) use ($technicialCategoryIds) {
                        $q->whereIn('category_id', $technicialCategoryIds);
                    }]);
            } else {
                // ONGOING: assigned to this tech and in progress; primary isPayment ok
                $ordersQuery
                    ->where('order_status', 1)
                    ->where('Technicial_id', $request->Technicial_id)
                    ->whereHas('primaryorder', function ($q) {
                        $q->whereIn('isPayment', [0, 1]);
                    });
            }
    
            // Get results first, THEN map
            $orders = $ordersQuery->get();
    
            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'order not found.',
                ], 400);
            }
            
            DB::transaction(function () use ($orders) {
                foreach ($orders as $order) {
                    $flagged = $order->orderdetail->filter(function ($d) use ($order) {
                       
                        return (int)($d->technicial_add_extra_service ?? 0) === 1
                            && (int)($d->iOrderId ?? $order->iOrderId) === $order->iOrderId;
                    });
            
                    // Compute even if empty (will be zero)
                    $extraBase = $flagged->sum(function ($d) {
                        $qty  = (int)($d->qty ?? 0); $rate = (float)($d->rate ?? 0);
                        return $qty * $rate;
                    });
            
                    $extraGST  = $flagged->sum(function ($d) {
                        return (float)($d->GSTAmount ?? 0);
                    });
            
                    // ===== choose one of the two behaviors =====
                    // (1) Idempotent "set to computed totals":
                    // $order->extra_technicial_amount = round($extraBase, 2);
                    // $order->extra_gst_amount        = round($extraGST, 2);
            
                    // (2) Incremental "+=" (beware double counting on repeated calls):
                    $order->extra_technicial_amount = round((float)($order->extra_technicial_amount ?? 0) + $extraBase, 2);
                    $order->extra_gst_amount        = round((float)($order->extra_gst_amount ?? 0) + $extraGST, 2);
            
                    $order->save();
                }
            });
    
            // Build the shortened response + normalize URLs inside the map
            $data = $orders->map(function ($order) {
                // normalize order-level service photos
                $service_photo_1 = $order->service_photo_1;
                $service_photo_2 = $order->service_photo_2;
                if (!empty($service_photo_1) && !Str::startsWith($service_photo_1, ['http://','https://'])) {
                    $service_photo_1 = "https://admin.auraclap.com/upload/servicephoto1/" . $service_photo_1;
                }
                if (!empty($service_photo_2) && !Str::startsWith($service_photo_2, ['http://','https://'])) {
                    $service_photo_2 = "https://admin.auraclap.com/upload/servicephoto2/" . $service_photo_2;
                }
                
                 $flaggedDetails = $order->orderdetail->filter(function ($d) {
                    return (int)($d->technicial_add_extra_service ?? 0) === 1;
                });
              
                $extraamount = $flaggedDetails->sum(function ($d) {
                    $qty  = (int)   ($d->qty  ?? 0);
                    $rate = (float) ($d->rate ?? 0);
                    return $qty * $rate;
                });
                
               $extraGST = $flaggedDetails->sum(function ($d) {
                    return $d->GSTAmount ?? 0;
               });
               $order->extra_technicial_amount   = round($extraamount, 2);
               $order->extra_gst_amount   = $extraGST ?? 0;
               $order->save();
               return [
                    "iOrderId"         => $order->iOrderId,
                    "order_primary_id" => $order->order_primary_id,
                    "iCustomerId"      => $order->iCustomerId,
                    "iAmount"          => $order->iAmount ?? '',
                    "iNetAmount"       => $order->iNetAmount ?? '',
                    "gst_amount"       => $order->gst_amount ?? '',
                    "iDiscount"        => $order->iDiscount,
                    "start_work"        => $order->start_work,
                    "order_status"     => (int) $order->order_status,
                    
                    // expose what you just saved
                    "extra_technicial_amount" => $order->extra_technicial_amount ?? 0,
                    "extra_gst_amount"        => number_format($order->extra_gst_amount,2),
    
                    // primary order fields
                    "isPayment"        => (int) ($order->primaryorder->isPayment ?? 0),
                    "payment_mode"     => (int) ($order->primaryorder->payment_mode ?? 0),
                    "order_date"       => $order->primaryorder->order_date ?? null,
                    "Customer_Address" => $order->primaryorder->Customer_Address ?? null,
                    "Customer_name" => $order->primaryorder->Customer_name ?? null,
                    "Customer_phone" => $order->primaryorder->Customer_phone ?? null,
                    "city_id" => $order->primaryorder->city_id ?? null,

                    "slot_strtime"     => $order->primaryorder->slot->strtime ?? null,
    
                    // order-level images (from order table)
                    "service_photo_1"  => $service_photo_1,
                    "service_photo_2"  => $service_photo_2,
    
                    // details
                    "orderdetail"      => $order->orderdetail->map(function ($detail) {
                        // normalize subcategory & category images
                        $sub_img = $detail->subcategory->SubCategories_img ?? null;
                        if (!empty($sub_img) && !Str::startsWith($sub_img, ['http://','https://'])) {
                            $sub_img = "https://admin.auraclap.com/upload/subcategory-images/" . $sub_img;
                        }
                        $cat_img = $detail->category->Categories_img ?? null;
                        if (!empty($cat_img) && !Str::startsWith($cat_img, ['http://','https://'])) {
                            $cat_img = "https://admin.auraclap.com/upload/category-image/" . $cat_img;
                        }
    
                        return [
                            "iOrderDetailId"   => $detail->iOrderDetailId,
                            "subcategory_id"   => $detail->subcategory_id,
                            "qty"              => $detail->qty,
                            "technicial_add_extra_service" => $detail->technicial_add_extra_service,
                            "rate"             => (float) $detail->rate,
                            "amount"           => (float) $detail->amount,
                            "net_amount"       =>  $detail->net_amount,
                            "GSTAmount"        =>  $detail->GSTAmount,
                            "discount_amount"  => (float) $detail->discount_amount,
    
                            "subcategory" => [
                                "id"    => $detail->subcategory->iSubCategoryId ?? null,
                                "name"  => $detail->subcategory->strSubCategoryName ?? null,
                                "title" => $detail->subcategory->title ?? null,
                                "sub_img"   => $sub_img,
                            ],
                            "category" => [
                                "id"    => $detail->category->Categories_id ?? null,
                                "name"  => $detail->category->Category_name ?? null,
                                "cat_img"   => $cat_img,
                            ],
                        ];
                    })->values(),
                ];
            })->values();
    
            return response()->json([
                'success' => true,
                'data'    => $data,
                'message' => 'order fetched successfully.',
            ], 200);
    
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function wallet_load(Request $request)
    {

        try {
            if (Auth::guard('technicialapi')->check()) {

                $request->validate([
                    "Technicial_id" => 'required',
                    "Amount" => 'required|numeric|min:1',

                ]);
                DB::beginTransaction();
                $Technicial = Technicial::where('Technicial_id', $request->Technicial_id)->first();
                if (!$Technicial) {
                    return response()->json([
                        'message' => 'Technicial not found',
                        'success' => false,
                    ], 404);
                }
                $orderdata = array(

                    "Technicial_id" => $request->Technicial_id,
                    "Amount" => $request->Amount,
                    "strIP" => $request->ip(),
                    "created_at" => now(),
                );
                $Order = Technicialwallet::create($orderdata);
                $orderid = 'Tech' . $Order->Technicial_wallet_id;

                $api = new Api(
                    config('services.razorpay.key'),
                    config('services.razorpay.secret')
                );
                $OrderAmount = $Order->Amount * 100;
                $orderData = [
                    'receipt'         => $orderid . '-' . date('dmYHis'),
                    'amount'          => $Order->Amount * 100,
                    'currency'        => 'INR',
                ];
                $razorpayOrder = $api->order->create($orderData);
                $orderId = $razorpayOrder['id'];
                $razorpayResponse = array(
                    'order_id' => $orderId,
                    'oid' => $orderid,
                    'amount' => $Order->Amount,
                    'currency' => 'INR',
                    'receipt' => $razorpayOrder['receipt'],
                );
                DB::commit();
                return [
                    'success' => true,
                    "message" => "order created successfully !",
                    "data" => $orderdata,
                    "razorpayResponse" => $razorpayResponse
                ];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            // Format validation errors as a single string
            $errorMessage = implode(', ', Arr::flatten($e->errors()));

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function paymentstatus(Request $request)
    {
        try {
            if (Auth::guard('technicialapi')->check()) {
                DB::beginTransaction();

                if ($request->status == 'Success') {

                    $request->validate([
                        'status' => 'required|string',
                        'razorpay_payment_id' => 'required',
                        'order_id' => 'required|string',
                        'Technicial_id' => 'required|exists:Technicial,Technicial_id',
                        'razorpay_order_id' => 'required|string',
                        'razorpay_signature' => 'nullable|string',
                        'amount' => 'required|numeric|min:1',
                        'currency' => 'required|string',
                        'json' => 'nullable',
                    ]);
                } else {
                    $request->validate([
                        'status' => 'required|string',
                        'razorpay_payment_id' => 'nullable',
                        'order_id' => 'required|string',
                        'Technicial_id' => 'required|exists:Technicial,Technicial_id',
                        'razorpay_order_id' => 'nullable|string',
                        'razorpay_signature' => 'nullable|string',
                        'amount' => 'required|numeric|min:1',
                        'currency' => 'required|string',
                        'json' => 'nullable',
                    ]);
                }


                $data = [
                    'order_id' => $request->razorpay_payment_id,
                    'oid' => $request->order_id,
                    'Technicial_id' => $request->Technicial_id,
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'receipt' => $request->order_id . '-' . date('dmYHis'),
                    'amount' => $request->amount,
                    'currency' => $request->currency,
                    'status' => $request->status,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'json' => $request->json,
                    'Remarks' => "Online Payment"
                ];

                DB::table('Technicial_wallet_payment')->insert($data);

                if ($request->status == "Success") {
                    // Get the latest balance of the technician
                    $technicialId = $request->Technicial_id;
                    $amount = $request->amount;

                    $lastLedger = TechnicialLedger::where('Technicial_id', $technicialId)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $openingBal = $lastLedger ? $lastLedger->closing_bal : 0;

                    $cr = $amount;

                    $closingBal = $openingBal + $cr;
                    // Insert new ledger entry
                    TechnicialLedger::create([
                        'Technicial_id' => $technicialId,
                        'comments' => 'Wallet Load',
                        'opening_bal' => $openingBal,
                        'Cr' => $cr,
                        'Dr' => 0,
                        'closing_bal' => $closingBal,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'strIP' => $request->ip(),
                    ]);
                } elseif ($request->status == "Fail") {  // Corrected this line
                    $data = [
                        'order_id' => $request->razorpay_payment_id,
                        'oid' => $request->order_id,
                        'Technicial_id' => $request->Technicial_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_signature' => $request->razorpay_signature,
                        'receipt' => $request->order_id . '-' . date('dmYHis'),
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                        'status' => $request->status,
                        'created_at' => now(),
                        'updated_at' => now(),
                        'json' => $request->json,
                        'Remarks' => "Online Payment"
                    ];

                    DB::table('Technicial_wallet_payment')->insert($data);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $request->status == "Success" ? "Payment status updated successfully." : "Payment Failed.",

                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => implode(', ', Arr::flatten($e->errors())),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function claimOrder(Request $request)
    {
        try {
            if (Auth::guard('technicialapi')->check()) {

                $request->validate([
                    "Technicial_id" => 'required|exists:Technicial,Technicial_id',
                    "Order_id" => 'required|exists:order,iOrderId',
                    //"Amount" => 'required',
                ]);

                DB::beginTransaction();

                $order = Order::with('orderdetail','primaryorder')->where('iOrderId', $request->Order_id)->first();
                
                $city_id = $order->primaryorder->city_id ?? 0;
              
                $customer_id = $order->iCustomerId ?? 0;


                $citydata = CityMaster::where('cityId', $city_id)->first();
                $companyper = $citydata->company_percentage ?? 0;
              

                if (!$order || $order->order_status == 3) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Order has already been cancelled by the customer.',
                    ], 403);
                }

                // Get Technician's latest balance
                $technicialId = $request->Technicial_id;
                $Technicial = Technicial::where('Technicial_id', $technicialId)->first();
                $technicialname = $Technicial->name;
                $orderAmount = $order->iAmount;
                $iDiscount = $order->iDiscount ?? 0;
                $gstAmount = $order->gst_amount ?? 0;
                $paymentMode = $order->primaryorder->payment_mode;


                $afterdiscount = $orderAmount - $iDiscount;

                $companyPercentage = $companyper;
              
                $companyAmount = round(($companyPercentage / 100) * $afterdiscount);
            
                $deductionAmount = $companyAmount;
                 
                if ($paymentMode == 2) { // Offline mode: company + GST
                    $deductionAmount += $gstAmount;
                }
                

                $lastLedger = TechnicialLedger::where('Technicial_id', $technicialId)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $availableBalance = $lastLedger ? $lastLedger->closing_bal : 0;

                if ($availableBalance < $deductionAmount) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Insufficient balance. Please load your wallet.',
                    ], 400);
                }

                // Deduct the amount from the technician's wallet
                $openingBal = $availableBalance;
                $dr = $deductionAmount;
                $closingBal = $openingBal - $dr;

                // Insert new ledger entry
                TechnicialLedger::create([
                    'Technicial_id' => $technicialId,
                    'comments' => 'Order claimed - ID: ' . $request->Order_id,
                    'opening_bal' => $openingBal,
                    'Cr' => 0,
                    'Dr' => $dr,
                    'closing_bal' => $closingBal,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'strIP' => $request->ip(),
                ]);

                // Mark order as claimed
                Order::where(
                    'iOrderId',
                    $request->Order_id
                )->update(['order_status' => 1, 'Technicial_id' => $request->Technicial_id]);

                DB::commit();
                
                $this->notification([
                    "Technicial_id" => $request->Technicial_id,
                    "customer_id" => $customer_id,
                    "title"   => "AuraClap",
                    "body"    => "Your Order has been assigned to {$technicialname}. Be ready to meet him.",
                    "order_id"    => $request->Order_id,
                    "type"    => "order_accepted",
                    "service" => "Customer",
                    "flag" => 1,
                    
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order claimed successfully!',
                    'deducted' => $deductionAmount,
                    'new_balance' => $closingBal,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => implode(', ', Arr::flatten($e->errors())),
            ], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function profiledetails(Request $request)
    {
        try {
            if (Auth::guard('technicialapi')->check()) {


                $request->validate([
                    'Technicial_id' => 'required|integer'

                ]);

                $Technicial = Technicial::with('state')->where('Technicial_id', $request->Technicial_id)
                    ->where('iStatus', 1)
                    ->where('isDelete', 0)
                    ->first();
                //dd($Technicial);


                if (!$Technicial) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Technicial not found.',
                    ], 404);
                }

                $average_rating_by_customer = CustomerReview::where('Technicial_id', $request->Technicial_id)
                    ->avg('rating');
                return response()->json([
                    'success' => true,
                    'data' => [
                        "name" => $Technicial->name,
                        "email" => $Technicial->email,
                        "mobile_no" => $Technicial->mobile_no,
                        "Technicial_image" => $Technicial->Technicial_image
                            ? asset('/upload/Technicial/' . $Technicial->Technicial_image)
                            : '',
                        "stateid" => $Technicial->stateid,
                        "stateName" => $Technicial->state->stateName,
                        "city" => $Technicial->city,
                        "rating" => $average_rating_by_customer,
                        "iStatus" => $Technicial->iStatus,
                        "strIP" => $Technicial->strIP,
                        "created_at" => $Technicial->created_at,
                        "updated_at" => $Technicial->updated_at,
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching profile details.',
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function profileUpdate(Request $request)
    {
        try {

            if (Auth::guard('technicialapi')->check()) {

                $customer = Auth::guard('technicialapi')->user();

                $request->validate([
                    'Technicial_id' => 'required'
                ]);

                $Technicial = Technicial::where(['iStatus' => 1, 'isDelete' => 0, 'Technicial_id' => $request->Technicial_id])->first();

                if (!$Technicial) {
                    return response()->json([
                        'success' => false,
                        'message' => "Technicial not found."
                    ]);
                }

                // Start building the Vendor data
                $TechnicialData = [];

                // Add fields conditionally
                if ($request->has('name')) {
                    $TechnicialData["name"] = $request->name;
                }
                if ($request->has('email')) {
                    $TechnicialData["email"] = $request->email;
                }
                if ($request->has('mobile_no')) {
                    $TechnicialData["mobile_no"] = $request->mobile_no;
                }
                if ($request->has('stateid')) {
                    $TechnicialData["stateid"] = $request->stateid;
                }
                if ($request->has('city')) {
                    $TechnicialData["city"] = $request->city;
                }



                if ($request->hasFile('Technicial_image')) {
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    $image = $request->file('Technicial_image');
                    $imgName = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                    $destinationPath = $root . '/upload/Technicial/';

                    // Ensure the directory exists
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    // Move the uploaded image to the destination path
                    $image->move($destinationPath, $imgName);

                    // Delete the old image if it exists
                    if ($customer->Customerimg && file_exists($destinationPath . $customer->Customerimg)) {
                        unlink($destinationPath . $customer->Customerimg);
                    }

                    // Update the image name
                    $TechnicialData['Technicial_image'] = $imgName;
                }

                // Always update 'updated_at'
                $TechnicialData['updated_at'] = now();

                DB::beginTransaction();

                try {

                    Technicial::where(['Technicial_id' => $request->Technicial_id])->update($TechnicialData);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => "Technicial Profile updated successfully.",

                    ], 200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    //12-06-2025 Mihir code
    public function ledger_list(Request $request)
    {
        try {

            if (Auth::guard('technicialapi')->check()) {

                $request->validate([
                    "Technicial_id" => "required"
                ]);

                $Technicial = Technicial::where(['iStatus' => 1, 'isDelete' => 0, 'Technicial_id' => $request->Technicial_id])->first();

                if (!$Technicial) {
                    return response()->json([
                        'success' => false,
                        'message' => "Technicial not found."
                    ]);
                }


                $ledger = TechnicialLedger::select(
                    'Technicial_ledger.*',
                    'Technicial.name'
                )
                    ->orderBy('Technicial_ledger_id', 'asc')
                    ->where(['Technicial_ledger.iStatus' => 1, 'Technicial_ledger.isDelete' => 0, 'Technicial_ledger.Technicial_id' => $request->Technicial_id])
                    ->leftjoin('Technicial', 'Technicial.Technicial_id', '=', 'Technicial_ledger.Technicial_id')
                    ->get();
                // dd($ledger);

                $data = $ledger->map(function ($item) {
                    return [
                        "Technicial_ledger_id" => $item->Technicial_ledger_id,
                        "Technicial_id" => $item->Technicial_id,
                        "name" => $item->name,
                        "opening_bal" => $item->opening_bal,
                        "Cr" => $item->Cr,
                        "Dr" => $item->Dr,
                        "closing_bal" => $item->closing_bal,
                        "comments" => $item->comments,
                        "date" => date('d-m-Y', strtoTime($item->created_at)),
                    ];
                });

                return response()->json([
                    'success' => true,
                    'message' => "Successfully fetched data.",
                    'data' => $data,
                ], 200);
            } else {

                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not Authorised.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
}
