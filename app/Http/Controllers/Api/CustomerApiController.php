<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CityMaster;
use App\Models\StateMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PushNotificationController;
use App\Models\Cart;
use App\Models\Technicial;
use App\Models\Notification;
use App\Models\FaqMaster;
use App\Models\Customer;
use App\Models\Categories;
use App\Models\TechnicialPincode;
use App\Models\SubCategories;
use App\Models\Managerate;
use App\Models\CustomerReview;
use App\Models\AreaMaster;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\AddressMaster;

use App\Models\OrderInvoice;
use App\Models\PrimaryOrder;

use App\Models\Pincode;
use App\Models\CustomerCouponApplyed;
use GuzzleHttp\Client;
use App\Models\Timeslot;
use App\Models\Slider;
use App\Models\TechnicialSlider;
use App\Models\BookingCancelReason;

use App\Models\Recruitment;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\BaseURL;
use App\Models\TechnicialService;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


class CustomerApiController extends PushNotificationController

{


    public function cityupdate(Request $request)
    {


        $request->validate([
            "Customer_id" => 'required',
            "state_id" => 'required',
            "city_id" => 'required'
        ]);

        try {
            if (Auth::guard('customerapi')->check()) {
                // Find the cart item
                $Customer = Customer::where('Customer_id', $request->Customer_id)->first();

                if (!$Customer) {
                    return response()->json([
                        'message' => 'Customer not found.',
                        'success' => false,
                    ], 404);
                }

                $StateMaster = StateMaster::where('stateId', $request->state_id)->first();

                if (!$StateMaster) {
                    return response()->json([
                        'message' => 'State not found.',
                        'success' => false,
                    ], 404);
                }

                $CityMaster = CityMaster::where('cityId', $request->city_id)->first();

                if (!$CityMaster) {
                    return response()->json([
                        'message' => 'City not found.',
                        'success' => false,
                    ], 404);
                }

                // Update the cart
                $Customer->update([
                    'state_id' => $request->state_id,
                    'city_id' => $request->city_id,
                ]);

                // Fetch the updated cart item
                $updatedCustomer = Customer::where('Customer_id', $request->Customer_id)->first();

                return response()->json([
                    'message' => 'City updated successfully',
                    'success' => true,
                    'data' => $updatedCustomer, // Return updated cart details
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function category_subcategory()
    {


        $baseCategoryImgUrl = 'http://admin.auraclap.com/upload/category-image/';
        $baseSubCategoryImgUrl = 'http://admin.auraclap.com/upload/subcategory-images/';
        $baseratcardImgUrl = 'http://admin.auraclap.com/upload/RateCardPdf/';

        $categories = Categories::with([

            'subcategories' => function ($q) {
                $q->where('isDelete', 0)
                    ->where('iStatus', 1)
                    ->where('sub_rat_flag', 0)
                    ->select('iSubCategoryId', 'iCategoryId', 'strSubCategoryName', 'strSlugName', 'SubCategories_img');
            },
            'subcategories.rates' => function ($q) {
                $q->where('isDelete', 0)->where('iStatus', 1);
            }
        ])
            ->where('isDelete', 0)
            ->where('iStatus', 1)
            ->get();

        $categories->transform(function ($category) use ($baseCategoryImgUrl, $baseSubCategoryImgUrl, $baseratcardImgUrl) {
            $category->Categories_img = $category->Categories_img ? $baseCategoryImgUrl . $category->Categories_img : null;
            $category->ratecard_pdf = $category->ratecard_pdf ? $baseratcardImgUrl . $category->ratecard_pdf : null;



            // Transform subcategories
            if ($category->subcategories) {
                $category->subcategories->transform(function ($sub) use ($baseSubCategoryImgUrl) {
                    $sub->SubCategories_img = $sub->SubCategories_img ? $baseSubCategoryImgUrl . $sub->SubCategories_img : null;

                    // Transform subcategory rates
                    if ($sub->rates) {
                        $sub->rates->transform(function ($rate) {
                            return [
                                'rate_id' => $rate->rate_id,
                                'title' => $rate->title,
                                'description' => $rate->description,
                                'amount' => $rate->amount,
                                'time' => $rate->time,
                            ];
                        });
                    }

                    return $sub;
                });
            }

            return $category;
        });


        return response()->json([
            'status' => true,
            'message' => 'Categories with subcategories and rates fetched successfully',
            'data' => $categories
        ]);
    }

    public function ratecardlist(Request $request)
    {
        $request->validate([
            'Categories_id' => 'required'
        ]);

        // Eager-load subcategories with rates
        $categories = Categories::with([
            'subcategories' => function ($q) {
                $q->where('sub_rat_flag', 1)
                    ->select('iSubCategoryId', 'iCategoryId', 'strSubCategoryName')
                    ->with(['rates' => function ($r) {
                        $r->select('rate_id', 'subcate_id', 'amount'); // keep only needed cols
                    }]);
            }
        ])
            ->where('Categories_id', $request->Categories_id)
            ->get();

        // Transform into minimal response
        $result = $categories->flatMap(function ($category) {
            return $category->subcategories->flatMap(function ($sub) {
                return $sub->rates->map(function ($rate) use ($sub) {
                    return [
                        'rate_id'           => $rate->rate_id,
                        'subcate_id'        => $rate->subcate_id,
                        'amount'            => $rate->amount,
                        'iCategoryId'       => $sub->iCategoryId,
                        'strSubCategoryName' => $sub->strSubCategoryName,
                    ];
                });
            });
        })->values();

        return response()->json([
            'status'  => true,
            'message' => 'Rates fetched successfully',
            'data'    => $result
        ]);
    }



    public function Addresslist(Request $request)
    {

        try {
            if (Auth::guard('customerapi')->check()) {
                $request->validate([
                    'customer_id' => 'required'
                ]);
                $Addresslist = AddressMaster::select(
                    "house_flat_no",
                    "house_flat_no",
                    "street_address",
                    "Type",
                    "Address_Master_id"
                )
                    ->where('Customer_id', $request->customer_id)
                    ->get();

                return response()->json([
                    'message' => 'successfully Address list fetched...',
                    'success' => true,
                    'data' => $Addresslist,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function bookingcancellist(Request $request)
    {

        try {
            if (Auth::guard('customerapi')->check()) {

                $BookingCancelReasonlist = BookingCancelReason::select(
                    "id",
                    "reason"

                )->get();

                return response()->json([
                    'message' => 'successfully Booking Cancel Reason list fetched...',
                    'success' => true,
                    'data' => $BookingCancelReasonlist,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function Add_Address(Request $request)
    {
        // dd($request);
        try {

            if (Auth::guard('customerapi')->check()) {
                $request->validate([
                    "house_flat_no" => 'required',
                    "street_address" => 'required',
                    "Type" => 'required',
                    "Customer_id" => 'required'


                ]);

                $CustomerAddressdata = array(

                    "house_flat_no" => $request->house_flat_no,
                    "street_address" => $request->street_address,
                    "Type" => $request->Type,
                    "Customer_id" => $request->Customer_id
                );

                $CustomerAddressdata = AddressMaster::create($CustomerAddressdata);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Customer Address Successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
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


    public function cat_sub_detail(Request $request)
    {
        try {
            // Validate the request, change to slug if required
            $request->validate([
                "Category_slug" => 'required|string',
                "Sub_Category_slug" => 'required|string',
                "city_id" => 'required|integer',
                "Customer_id" => 'nullable|integer',
            ]);

            // Get Category_id using Category_slug
            $category = Categories::where('Categories_slug', $request->Category_slug)->first();
            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category not found.',
                ], 404);
            }

            // Get Sub_Category_id using Sub_Category_slug
            $subcategory = SubCategories::where('strSlugName', $request->Sub_Category_slug)
                ->where('iCategoryId', $category->Categories_id)
                ->first();
            if (!$subcategory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subcategory not found.',
                ], 404);
            }

            // Get list of subcategory IDs in cart for the given customer
            $subcategoryid = Cart::where('Categories_id', $category->Categories_id)
                ->where('Customer_id', $request->Customer_id)
                ->pluck('subcate_id')
                ->toArray();

            // Fetch Managerate record
            $catsubdetail = Managerate::with('subcategory')
                ->where('city_id', $request->city_id)
                ->where('cate_id', $category->Categories_id)
                ->where('subcate_id', $subcategory->iSubCategoryId)
                ->first();

            if (!$catsubdetail) {
                return response()->json([
                    'success' => false,
                    'message' => 'No matching record found.',
                ], 404);
            }

            // Check if the subcategory exists in the cart
            $is_cart = in_array($subcategory->iSubCategoryId, $subcategoryid) ? "1" : "0";

            // Prepare the response data
            $response = [
                "title" => $catsubdetail->title,
                "rate_id" => $catsubdetail->rate_id,
                "Categories_id" => $category->Categories_id,
                "iSubCategoryId" => $subcategory->iSubCategoryId,
                "description" => $catsubdetail->description,
                "how_it_work_description" => $catsubdetail->how_it_work_description,
                "amount" => $catsubdetail->amount,
                "strCategoryName" => $subcategory->strCategoryName,
                "strSubCategoryName" => $subcategory->strSubCategoryName,
                "SubCategories_img" => "http://admin.auraclap.com/upload/subcategory-images/{$subcategory->SubCategories_img}",
                "is_cart" => $is_cart,
                "category" => [
                    "meta_keyword" => $category->meta_keyword,
                    "meta_description" => $category->meta_description,
                    "meta_title" => $category->meta_title,
                    "meta_head" => $category->meta_head,
                    "meta_body" => $category->meta_body,
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $response,
                'message' => 'Category or Subcategory fetched successfully.',
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', Arr::flatten($e->errors())),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function customer_new_registration(Request $request)
    {
        // dd($request);
        try {


            $request->validate([
                "Customer_name" => 'required',
                "Customer_Address" => 'nullable|string',
                "Customer_phone" => 'required|digits:10|unique:Customer,Customer_phone',
                "Pincode" => 'nullable',
                "state_id" => 'required',
                "city_id" => 'required',
                "area_id" => 'required',
                "email" => 'required'


            ]);
            $existingCustomer = Customer::where('Customer_phone', $request->Customer_phone)->first();
            if ($existingCustomer) {
                return response()->json([
                    'success' => false,
                    'message' => 'A customer with this mobile number already exists.',
                ], 409); // 409 Conflict HTTP status code
            }
            $Customerdata = array(

                "Customer_name" => $request->Customer_name,
                "Customer_phone" => $request->Customer_phone,
                "Customer_Address" => $request->Customer_Address,
                "Pincode" => $request->Pincode,
                "state_id" => $request->state_id,
                "city_id" => $request->city_id,
                "area_id" => $request->area_id,
                "email" => $request->email,
                'strIP' => $request->ip(),
            );

            $Customer = Customer::create($Customerdata);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Registration Successfully.',
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

    public function RefreshfirebaseDeviceToken(Request $request)
    {
        try {
            // Validate input
            $request->validate([
                'customer_id' => 'required|integer|exists:Customer,Customer_id',
                'firebaseDeviceToken' => 'required|string',
            ]);

            // Find the technicial
            $Customer = Customer::where('Customer_id', $request->customer_id)->first();

            if (!$Customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found.',
                ], 404);
            }

            // Update firebase token
            $Customer->firebaseDeviceToken = $request->firebaseDeviceToken;
            $Customer->save();

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


    public function login(Request $request)
    {
        try {
            // Validate the phone number
            $request->validate([
                'Customer_phone' => 'required|digits_between:10,15',
                'firebaseDeviceToken' => 'nullable',

            ]);
            $mobile = $request->Customer_phone;

            $otp = rand(1000, 9999);
            //$otp = '1234';
            $expiry_date = now()->addMinutes(5);



            // Check if customer exists
            $customer = Customer::where('Customer_phone', $request->Customer_phone)->first();


            if (!$customer) {
                // If not found, create new customer with phone and OTP
                $customer = Customer::create([
                    'Customer_phone' => $request->Customer_phone,
                    'firebaseDeviceToken' => $request->firebaseDeviceToken,
                    'otp' => $otp,
                    'expiry_time' => $expiry_date,
                    'iStatus' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // return response()->json([
                //     'success' => true,
                //     'message' => 'New customer created and OTP sent.',
                //     'otp' => $otp, // for testing; in production, send via SMS
                //     'customerdetail' => $customer
                // ], 201);
            }
            $message = "Dear Customer, your OTP is $otp. Please use this to verify your action. Do not share it with anyone. Regards, Team The Auraclap";
            //$customer = new Customer();

            $customer->WhatsappMessage($mobile, $message);

            // If found, update OTP and expiry

            $customer->update([
                'otp' => $otp,
                'firebaseDeviceToken' => $request->firebaseDeviceToken,
                'expiry_time' => $expiry_date
            ]);

            // Generate JWT token (optional here before OTP verification)
            $token = JWTAuth::fromUser($customer);


            return response()->json([
                'success' => true,
                'message' => 'Existing customer OTP sent.',
                //'otp' => $otp, // for testing; in production, send via SMS
                'customerdetail' => [
                    "Customer_id" => $customer->Customer_id,
                    "Customer_name" => $customer->Customer_name,
                    "hotelhostel_name" => $customer->hotelhostel_name,
                    "Customer_phone" => $customer->Customer_phone,
                    "city_id" => $customer->city_id,
                    "iStatus" => $customer->iStatus,
                    "strIP" => $customer->strIP,
                    "created_at" => $customer->created_at,
                    "updated_at" => $customer->updated_at,
                ],
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required',
                'otp' => 'required'
            ]);


            $Customer = Customer::where([
                'Customer_id' => $request->customer_id,
                'otp' => $request->otp
            ])->first();

            if (!$Customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP is invalid. Please enter a valid OTP.',
                ], 400);
            }

            //Check if the OTP has expired
            $expiryTime = Carbon::parse($Customer->expiry_time);
            if (now()->greaterThan($expiryTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OTP has expired.',
                ], 400);
            }
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


    public function startotp(Request $request)
    {
        try {
            if (Auth::guard('customerapi')->check()) {

                $request->validate([
                    "order_id" => 'required',
                    "Customer_id" => 'required',

                ]);
                $otp = mt_rand(100000, 999999);
                $existingCustomer = Order::where(function ($query) use ($request) {
                    $query->where('iOrderId', $request->iOrderId)
                        ->orWhere('iCustomerId', $request->Customer_id);
                })->first();

                if (empty($existingCustomer)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A customer with this order not found.',
                    ], 409);
                }
                $updateotp = array(
                    'start_otp' => $otp
                );
                DB::beginTransaction();
                Order::where("iOrderId", $request->order_id)->update($updateotp);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'start_otp' => $otp,
                    'message' => 'Start OTP sent Successfully',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
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

    public function endotp(Request $request)
    {
        try {

            if (Auth::guard('technicialapi')->check()) {

                $request->validate([
                    "order_id" => 'required',
                    "Customer_id" => 'required',

                ]);
                $otp = mt_rand(100000, 999999);
                $existingCustomer = Order::where(function ($query) use ($request) {
                    $query->where('iOrderId', $request->order_id)
                        ->orWhere('iCustomerId', $request->Customer_id);
                })->first();


                if (empty($existingCustomer)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'A customer with this order not found.',
                    ], 409);
                }
                $updateotp = array(
                    'end_otp' => $otp
                );

                DB::beginTransaction();
                Order::where("iOrderId", $request->order_id)->update($updateotp);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'end_otp' => $otp,
                    'message' => 'End OTP sent Successfully',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Technicial is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
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

    public function profiledetails(Request $request)
    {
        try {

            if (Auth::guard('customerapi')->check()) {

                $request->validate([
                    'Customer_id' => 'required|integer',
                ]);

                $Customer = Customer::with(['city', 'area', 'state'])->where('Customer_id', $request->Customer_id)
                    ->where('iStatus', 1)
                    ->where('isDelete', 0)
                    ->first();

                if (!$Customer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Customer not found.',
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        "Customer_id" => $Customer->Customer_id,
                        "Customer_name" => $Customer->Customer_name,
                        "Customer_Address" => $Customer->Customer_Address,
                        "Customer_phone" => $Customer->Customer_phone,
                        "Customerimg" => $Customer->Customerimg
                            ? asset('upload/Customer/' . $Customer->Customerimg)
                            : '',
                        "Pincode" => $Customer->Pincode,
                        "city_id" => $Customer->city_id,
                        "state_id" => $Customer->state_id,
                        "area_id" => $Customer->area_id,
                        "cityname" => $Customer->city->cityName ?? '',
                        "areaname" => $Customer->area->areaName ?? '',
                        "statename" => $Customer->state->stateName ?? '',
                        "email" => $Customer->email,
                        "iStatus" => $Customer->iStatus,
                        "strIP" => $Customer->strIP,
                        "created_at" => $Customer->created_at,
                        "updated_at" => $Customer->updated_at,
                    ],
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
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

            if (Auth::guard('customerapi')->check()) {

                $customer = Auth::guard('customerapi')->user();

                $request->validate([
                    'Customer_id' => 'required'
                ]);

                $customer = Customer::where(['iStatus' => 1, 'isDelete' => 0, 'Customer_id' => $request->Customer_id])->first();

                if (!$customer) {
                    return response()->json([
                        'success' => false,
                        'message' => "Customer not found."
                    ]);
                }

                // Start building the Vendor data
                $CustomerData = [];

                // Add fields conditionally
                if ($request->has('Customer_name')) {
                    $CustomerData["Customer_name"] = $request->Customer_name;
                }
                if ($request->has('Customer_Address')) {
                    $CustomerData["Customer_Address"] = $request->Customer_Address;
                }
                if ($request->has('Customer_phone')) {
                    $CustomerData["Customer_phone"] = $request->Customer_phone;
                }
                if ($request->has('Pincode')) {
                    $CustomerData["Pincode"] = $request->Pincode;
                }
                if ($request->has('state_id')) {
                    $CustomerData["state_id"] = $request->state_id;
                }
                if ($request->has('city_id')) {
                    $CustomerData["city_id"] = $request->city_id;
                }
                if ($request->has('area_id')) {
                    $CustomerData["area_id"] = $request->area_id;
                }
                if ($request->has('email')) {
                    $CustomerData["email"] = $request->email;
                }


                if ($request->hasFile('Customerimg')) {
                    $root = $_SERVER['DOCUMENT_ROOT'];
                    $image = $request->file('Customerimg');
                    $imgName = time() . '_' . date('dmYHis') . '.' . $image->getClientOriginalExtension();
                    $destinationPath = $root . '/upload/Customer/';

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
                    $CustomerData['Customerimg'] = $imgName;
                }

                // Always update 'updated_at'
                $CustomerData['updated_at'] = now();

                DB::beginTransaction();

                try {

                    Customer::where(['Customer_id' => $request->Customer_id])->update($CustomerData);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => "Customer Profile updated successfully.",
                        // 'data' => [
                        //     'vendorimg' => isset($CustomerData['Customerimg']) ? asset('upload/Customer/' . $CustomerData['Customerimg']) : null,
                        // ]
                    ], 200);
                } catch (\Throwable $th) {
                    DB::rollBack();
                    throw $th;
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function categories(Request $request)
    {
        try {

            $query = Categories::select(
                "Categories_id",
                "Category_name",
                "city_id",
                "caption",
                "rating",
                "hours",
                "onwards_amount",
                "Categories_img",
                "home_cate_image",
                "ratecard_pdf",
                "warranty",
                "Categories_slug",
                "carousel_image",
                "Categories_icon",
                "meta_keyword",
                "meta_description",
                "meta_title",
                "meta_head",
                "meta_body"

            );

            // If search param exists, apply filter
            if ($request->has('Category_name') && !empty($request->Category_name)) {
                $query->where('Category_name', 'LIKE', '%' . $request->Category_name . '%');
            }

            // If city param exists, apply filter by city_id
            if ($request->has('city_id') && !empty($request->city_id)) {
                $query->where('city_id', '=', $request->city_id);
            }

            $categories = $query->get();

            // Add full image URL
            $categories->each(function ($category) {
                $category->Categories_img = $category->Categories_img
                    ? "http://admin.auraclap.com/upload/category-image/{$category->Categories_img}"
                    : null;

                $category->home_cate_image = $category->home_cate_image
                    ? "http://admin.auraclap.com/upload/Home-category-image/{$category->home_cate_image}"
                    : null;

                $category->ratecard_pdf = $category->ratecard_pdf
                    ? "http://admin.auraclap.com/upload/RateCardPdf/{$category->ratecard_pdf}"
                    : null;

                $category->carousel_image = $category->carousel_image
                    ? "http://admin.auraclap.com/upload/carousel-icon/{$category->carousel_image}"
                    : null;

                $category->Categories_icon = $category->Categories_icon
                    ? "http://admin.auraclap.com/upload/category-icon/{$category->Categories_icon}"
                    : null;
            });

            return response()->json([
                'message' => 'Successfully fetched categories.',
                'success' => true,
                'data' => $categories,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }

    public function HomeSliderImage(Request $request)
    {
        try {
            if (Auth::guard('customerapi')->check()) {
                $Sliders = Slider::select(
                    "link",
                    "image"
                )->get();

                // Transform each category to include the full image URL
                $Sliders = $Sliders->map(function ($Slider) {
                    $Slider->image = $Slider->image
                        ? "http://admin.auraclap.com/upload/Slider-images/" . $Slider->image
                        : null;
                    return $Slider;
                });

                return response()->json([
                    'message' => 'Successfully Slider fetched...',
                    'success' => true,
                    'data' => $Sliders,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function TechnicialSliderImage(Request $request)
    {
        try {
            if (Auth::guard('technicialapi')->check()) {
                $Sliders = TechnicialSlider::select(
                    "link",
                    "image"
                )->get();

                // Transform each category to include the full image URL
                $Sliders = $Sliders->map(function ($Slider) {
                    $Slider->image = $Slider->image
                        ? "http://admin.auraclap.com/upload/TechnicialSlider-images/" . $Slider->image
                        : null;
                    return $Slider;
                });

                return response()->json([
                    'message' => 'Successfully Technicial Slider fetched...',
                    'success' => true,
                    'data' => $Sliders,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function state(Request $request)
    {

        try {
            if (Auth::guard('customerapi')->check()) {
                $state = StateMaster::select(
                    "stateId",
                    "stateName"
                )->get();
                return response()->json([
                    'message' => 'successfully State fetched...',
                    'success' => true,
                    'data' => $state,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function city(Request $request)
    {

        try {
            // $request->validate([
            //     'state_id' => 'required'
            // ]);
            $City = CityMaster::select(
                "cityId",
                "cityName"
            )
                //->where('stateMasterStateId', $request->state_id)
                ->get();

            return response()->json([
                'message' => 'successfully City fetched...',
                'success' => true,
                'data' => $City,
            ], 200);
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function Area(Request $request)
    {

        try {
            if (Auth::guard('customerapi')->check()) {
                $request->validate([
                    'Cityid' => 'required'
                ]);
                $Area = AreaMaster::select(
                    "areaId",
                    "areaName"
                )
                    ->where('areacityId', $request->Cityid)
                    ->get();
                return response()->json([
                    'message' => 'successfully Area fetched...',
                    'success' => true,
                    'data' => $Area,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function subcat_or_rate(Request $request)
    {
        $request->validate([
            'Categories_id' => 'required',
            'Customer_id' => 'required',
            'city_id' => 'required',
        ]);
        $subcategoryid = Cart::where('Categories_id', $request->Categories_id)
            ->where('Customer_id', $request->Customer_id)
            ->pluck('subcate_id')
            ->toArray();

        try {

            // if (Auth::guard('customerapi')->check()) {

            $category = Categories::with([

                'subcategories' => function ($q) {
                    $q->where('sub_rat_flag', 0);
                },

                // rates: city filter + only those whose subcategory has sub_rat_flag = 0
                'rates' => function ($q) use ($request) {
                    $q->where('city_id', $request->city_id)
                        ->whereHas('subcategory', function ($subq) {
                            $subq->where('sub_rat_flag', 0);
                        })
                        ->with(['subcategory' => function ($subq) {
                            $subq->where('sub_rat_flag', 0);
                        }]);
                },
            ])->where('Categories_id', $request->Categories_id)->first();



            if (!$category) {
                return response()->json([
                    'message' => 'No category found.',
                    'success' => false,
                ], 404);
            }

            return response()->json([
                'message' => 'Successfully fetched category with subcategories and rates.',
                'success' => true,
                'data'    => [
                    'Categories_id'  => $category->Categories_id,
                    'Category_name'  => $category->Category_name,
                    'subcategories'  => $category->subcategories->map(function ($sub) {

                        return [
                            'iSubCategoryId'   => $sub->iSubCategoryId,
                            'strSubCategoryName' => $sub->strSubCategoryName,
                            'SubCategories_img'  => "http://admin.auraclap.com/upload/subcategory-images/{$sub->SubCategories_img}",
                        ];
                    }),
                    'rates' => $category->rates->map(function ($rate) use ($subcategoryid) {

                        //$is_cart = in_array($rate->subcategory->iSubCategoryId, $subcategoryid) ? "1" : "0";
                        $is_cart = in_array($rate->subcategory->iSubCategoryId ?? '', $subcategoryid) ? "1" : "0";


                        return [
                            'rate_id'      => $rate->rate_id,
                            'title'        => $rate->title,
                            'description'  => $rate->description,
                            'how_it_work_description'  => $rate->how_it_work_description,
                            'amount'       => $rate->amount,
                            'time'       => $rate->time,
                            'is_cart'      => $is_cart,
                            'subcategory'  => $rate->subcategory ? [
                                'iSubCategoryId'   => $rate->subcategory->iSubCategoryId ?? '',
                                'strSubCategoryName' => $rate->subcategory->strSubCategoryName ?? '',
                                'SubCategories_img'  => "http://admin.auraclap.com/upload/subcategory-images/{$rate->subcategory->SubCategories_img}",
                            ] : null,
                        ];
                    }),
                ],
            ], 200);
            // } else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Customer is not authorized.',
            //     ], 401);
            // }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function rating(Request $request)
    {
        DB::beginTransaction(); // Start DB transaction

        try {
            // Validate required fields
            if (Auth::guard('customerapi')->check()) {
                $request->validate([
                    "Technicial_id" => 'required|integer',
                    "customer_id" => 'required|integer',
                    "order_id" => 'required|integer',
                    "rating" => 'nullable|numeric|min:1|max:5',
                    "review" => 'nullable|string',
                ]);

                // Check if the review already exists
                $existingReview = CustomerReview::where('customer_id', $request->customer_id)
                    ->where('order_id', $request->order_id)
                    ->first();

                if ($existingReview) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already submitted a review for this order.',
                    ], 409); // Conflict
                }

                // Create the review
                CustomerReview::create([
                    "customer_id" => $request->customer_id,
                    "order_id" => $request->order_id,
                    "rating" => $request->rating,
                    "review" => $request->review,
                    "Technicial_id" => $request->Technicial_id,
                    "strIP" => $request->ip(),
                ]);

                // Update order's is_review flag
                OrderDetail::where('iOrderDetailId', $request->order_id)
                    ->where('iCustomerId', $request->customer_id)
                    ->update(['is_review' => 1]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Thank you for your review!',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            DB::rollBack();
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

    // public function ongoingorder(Request $request)
    // {
    //     $request->validate([
    //         'Customer_id' => 'required'
    //     ]);


    //     try {
    //         if (!Auth::guard('customerapi')->check()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Customer is not authorized.',
    //             ], 401);
    //         }

    //         $ongoingorders = Order::query()
    //         ->orderBy('created_at', 'desc')
    //             ->with([
    //                 'orderdetail.subcategory',
    //                 'orderdetail.category',
    //                 'slot',
    //                 'Technicial',
    //                 'customerReviews',
    //                 'primaryorder'
    //             ])
    //             ->where('iCustomerId', $request->Customer_id)
    //             ->whereIn('order_status', [0, 1])
    //           ->whereHas('primaryorder', function ($q) {
    //                 $q->where('payment_mode', 2)
    //                   ->orWhere(function ($q2) {
    //                       $q2->whereIn('payment_mode', [1, 3])
    //                          ->whereIn('isPayment', [0, 1]);
    //                   });
    //             })
    //             ->get();


    //         if ($ongoingorders->isEmpty()) {
    //             return response()->json([
    //                 'message' => 'Order not found.',
    //                 'success' => false,
    //             ], 404);
    //         }

    //         $orderArr = [];

    //         foreach ($ongoingorders as $order) {
    //             $technician = $order->Technicial;
    //             $average_rating_by_customer = $technician && $technician->Technicial_id
    //                 ? CustomerReview::where('customer_id', $request->Customer_id)
    //                     ->where('Technicial_id', $technician->Technicial_id)
    //                     ->avg('rating')
    //                 : null;

    //             $orderArr[] = [
    //                 "iOrderId"        => $order->iOrderId,
    //                 "iCustomerId"     => $order->iCustomerId,
    //                 "iAmount"         => $order->iAmount,
    //                 "iNetAmount"      => $order->iNetAmount,
    //                 "gst_amount"      => $order->gst_amount,
    //                 "iDiscount"       => $order->iDiscount,
    //                 "order_date"      => $order->primaryorder->order_date ?? '',
    //                 "payment_mode"    => $order->primaryorder->payment_mode ?? '',
    //                 "isPayment"       => $order->primaryorder->isPayment ?? '',
    //                 "Customer_Address"=> $order->primaryorder->Customer_Address ?? '',
    //                 "slot_id"         => $order->primaryorder->slot_id ?? '',
    //                 "strtime"         => $order->primaryorder->slot->strtime ?? null,
    //                 "order_status"    => $order->primaryorder->order_status ?? '',

    //                 // Technician
    //                 "technician" => $technician ? [
    //                     "Technicial_id"   => $technician->Technicial_id,
    //                     "name"            => $technician->name,
    //                     "email"           => $technician->email,
    //                     "mobile_no"       => $technician->mobile_no,
    //                     "city"            => $technician->city,
    //                     "Technicial_image"=> $technician->Technicial_image
    //                         ? 'http://admin.auraclap.com/upload/Technicial/' . $technician->Technicial_image
    //                         : null,
    //                 ] : null,

    //                 "technician_avg_rating" => $average_rating_by_customer
    //                     ? round($average_rating_by_customer)
    //                     : null,

    //                 // Child order details (subcategory wise)
    //                 "details" => $order->orderdetail->map(function ($detail) {
    //                     $subcategory = $detail->subcategory;
    //                     $category = $detail->category ?? $subcategory?->category;

    //                     return [
    //                         "iOrderDetailId"   => $detail->iOrderDetailId,
    //                         "primaryiOrderId"   => $order->primaryorder->primaryiOrderId ?? '',
    //                         "subcategory_id"   => $detail->subcategory_id,
    //                         "strSubCategoryName"=> $subcategory->strSubCategoryName ?? null,
    //                         "SubCategories_img" => $subcategory && $subcategory->SubCategories_img
    //                         ? 'http://admin.auraclap.com/upload/subcategory-images/' . $subcategory->SubCategories_img
    //                         : null,
    //                         "title"            => $subcategory->title ?? null,
    //                         "sub_title" => $subcategory->sub_title ?? null,
    //                         "qty"              => $detail->qty,
    //                         "rate"             => $detail->rate,
    //                         "amount"           => $detail->amount,
    //                         "net_amount"       => $detail->net_amount,
    //                         "GSTAmount"        => $detail->GSTAmount,
    //                         "discount_amount"  => $detail->discount_amount,
    //                         "service_photo_1"  => $detail->service_photo_1
    //                             ? 'http://admin.auraclap.com/upload/servicephoto1/' . $detail->service_photo_1
    //                             : null,
    //                         "service_photo_2"  => $detail->service_photo_2
    //                             ? 'http://admin.auraclap.com/upload/servicephoto2/' . $detail->service_photo_2
    //                             : null,

    //                         // Category info
    //                         "Category_name"    => $category->Category_name ?? null,
    //                         "Categories_img"   => $category && $category->Categories_img
    //                             ? 'http://admin.auraclap.com/upload/category-image/' . $category->Categories_img
    //                             : null,
    //                     ];
    //                 })
    //             ];
    //         }

    //         return response()->json([
    //             'message' => 'Ongoing Orders Fetched Successfully',
    //             'success' => true,
    //             'data' => $orderArr,
    //         ], 200);

    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }

    public function ongoingorder(Request $request)
    {
        $request->validate(['Customer_id' => 'required|integer']);

        try {
            if (!Auth::guard('customerapi')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }

            $primaryOrders = PrimaryOrder::query()
                ->where('iCustomerId', $request->Customer_id)
                ->whereIn('isPayment', [0, 1])
                ->whereIn('payment_mode', [0, 1, 2, 3])

                ->whereHas('orders', function ($q) {
                    $q->whereIn('order_status', [0, 1]);
                    //->whereIn('payment_mode', [0, 1,2,3]);

                })
                ->with([
                    'slot',
                    'orders' => function ($q) use ($request) {
                        $q->where('iCustomerId', $request->Customer_id)
                            ->whereIn('order_status', [0, 1])
                            ->orderBy('iOrderId', 'desc');
                    },
                    'orders.orderdetail' => function ($q) {
                        // if you also store detail-level statuses and want only ongoing details, filter here
                        // $q->whereIn('order_status', [0, 1]);
                    },
                    'orders.orderdetail.subcategory',
                    'orders.orderdetail.category',
                    'orders.Technicial',
                    'orders.customerReviews',
                ])
                ->orderBy('primaryiOrderId', 'desc')
                ->get();

            if ($primaryOrders->isEmpty()) {
                return response()->json([
                    'message' => 'Order not found.',
                    'success' => false,
                ], 404);
            }

            // Build the response you asked for
            // Build the response you asked for
            $payload = [
                'primaryOrder' => $primaryOrders->map(function ($p) use ($request) {



                    return [
                        'primaryiOrderId'  => $p->primaryiOrderId,
                        'iCustomerId'      => $p->iCustomerId,
                        'iAmount'          => $p->iAmount,
                        'iNetAmount'       => $p->iNetAmount,
                        'gst_amount'       => $p->gst_amount,
                        'iDiscount'        => $p->iDiscount,
                        'order_date'       => $p->order_date,
                        'payment_mode'     => (string) $p->payment_mode,
                        'isPayment'        => (int) $p->isPayment,
                        'Customer_Address' => $p->Customer_Address,
                        'slot_id'          => $p->slot_id,
                        'strtime'          => $p->slot->strtime ?? null,

                        // If you still want a primary-level technician, you can compute a rollup here.
                        // I’m omitting it since you want it per-order below.

                        // CHILD ORDERS
                        'order' => $p->orders->map(function ($o) use ($p, $request) {

                            // Per-order technician
                            $tech = $o->Technicial;

                            $avgByCustomer = $tech && $tech->Technicial_id
                                ? CustomerReview::where('customer_id', $request->Customer_id)
                                ->where('Technicial_id', $tech->Technicial_id)
                                ->avg('rating')
                                : null;

                            // Category visual from first detail
                            $firstDetail = $o->orderdetail->first();
                            $cat = $firstDetail?->category;

                            return [
                                'iOrderId'        => $o->iOrderId,
                                'primaryiOrderId' => (string) $p->primaryiOrderId,
                                'iAmount'         => (string) $o->iAmount,
                                'iDiscount'    => (int) ($o->iDiscount ?? 0),
                                'gst_amount'      => (string) $o->gst_amount,
                                'iNetAmount'      => (string) $o->iNetAmount,
                                'order_status'    => (int) ($o->order_status ?? 0),  // ✅ scalar
                                'end_otp'    => (int) ($o->end_otp ?? 0),
                                'extra_technicial_amount'    => (int) ($o->extra_technicial_amount ?? 0),
                                'extra_gst_amount'    => (int) ($o->extra_gst_amount ?? 0),

                                // Per-order technician + rating ✅
                                'technician' => $tech ? [
                                    'Technicial_id'    => $tech->Technicial_id,
                                    'name'             => $tech->name,
                                    'email'            => $tech->email,
                                    'mobile_no'        => $tech->mobile_no,
                                    'city'             => $tech->city,
                                    'Technicial_image' => $tech->Technicial_image
                                        ? 'http://admin.auraclap.com/upload/Technicial/' . $tech->Technicial_image
                                        : null,
                                ] : null,
                                'technician_avg_rating' => $avgByCustomer !== null ? $avgByCustomer : null,

                                // order-level photos derived from details (optional)
                                'service_photo_1' => $firstDetail?->service_photo_1
                                    ? 'http://admin.auraclap.com/upload/servicephoto1/' . $firstDetail->service_photo_1
                                    : null,
                                'service_photo_2' => $firstDetail?->service_photo_2
                                    ? 'http://admin.auraclap.com/upload/servicephoto2/' . $firstDetail->service_photo_2
                                    : null,

                                'Category_name'   => $cat->Category_name ?? null,
                                'Categories_img'  => ($cat && $cat->Categories_img)
                                    ? 'http://admin.auraclap.com/upload/category-image/' . $cat->Categories_img
                                    : null,

                                // ORDER DETAILS
                                'orderdetail' => $o->orderdetail->map(function ($d) {
                                    $sub = $d->subcategory;

                                    return [
                                        'iOrderDetailId'     => $d->iOrderDetailId,
                                        'iOrderId'           => $d->iOrderId,
                                        'title'              => $sub->title ?? null,
                                        'sub_title'          => $sub->sub_title ?? null,
                                        'subcategory_id'     => $d->subcategory_id,
                                        'qty'                => $d->qty,
                                        'rate'               => $d->rate,
                                        'amount'             => $d->amount,
                                        'strSubCategoryName' => $sub->strSubCategoryName ?? null,
                                        'SubCategories_img'  => ($sub && $sub->SubCategories_img)
                                            ? 'http://admin.auraclap.com/upload/subcategory-images/' . $sub->SubCategories_img
                                            : null,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];


            return response()->json([
                'message' => 'Ongoing Orders Fetched Successfully',
                'success' => true,
                'data'    => $payload,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    // public function completeorder(Request $request)
    // {
    //     $request->validate([
    //         'Customer_id' => 'required'
    //     ]);

    //     try {
    //         if (Auth::guard('customerapi')->check()) {

    //         $completeorders = OrderDetail::orderBy('created_at', 'desc')
    //             ->with([
    //                 'Technicial',
    //                 'order.slot',
    //                 'order',
    //                 'category',
    //                 'customerReviews',
    //                 'subcategory'
    //             ])
    //             ->where('iCustomerId', $request->Customer_id)
    //             ->whereIn('order_status', [2])
    //             ->whereHas('order', function ($query) {
    //                   $query->whereIn('payment_mode', [1, 2, 3])
    //                     ->whereIn('isPayment', [0, 1]);
    //             })
    //             ->orderBy('iOrderId', 'desc')
    //             ->limit(4)
    //             ->get();
    //             //->toArray();



    //         $orderArr = [];
    //         foreach ($completeorders as $detail) {
    //             $order = $detail->order;

    //             if (!$order) continue;

    //             $subcategory = $detail->subcategory;
    //             $category = $detail->category ?? $subcategory?->category;
    //             $technician = $detail->Technicial;

    //             $average_rating_by_customer = $technician && $technician->Technicial_id
    //                 ? CustomerReview::where('customer_id', $request->Customer_id)
    //                 ->where('Technicial_id', $technician->Technicial_id)
    //                 ->avg('rating')
    //                 : null;

    //             $orderArr[] = [
    //                 // Order Detail
    //                 "iOrderDetailId" => $detail->iOrderDetailId,
    //                 "iOrderId" => $detail->iOrderId,
    //                 "iCustomerId" => $detail->iCustomerId,
    //                 "category_id" => $detail->category_id,
    //                 "Ratecard_id" => $detail->Ratecard_id,
    //                 "qty" => $detail->qty,
    //                 "rate" => $detail->rate,
    //                 "amount" => $detail->amount,
    //                 "net_amount" => $detail->net_amount,
    //                 "GSTAmount" => $detail->GSTAmount,
    //                 "discount_amount" => $detail->discount_amount,
    //                 "subcategory_id" => $detail->subcategory_id,
    //                 "isRefund" => $detail->isRefund,
    //                 "service_photo_1" => $detail && $detail->service_photo_1
    //                     ? 'http://admin.auraclap.com/upload/servicephoto1/' . $detail->service_photo_1
    //                     : null,
    //                 "service_photo_2" => $detail && $detail->service_photo_2
    //                     ? 'http://admin.auraclap.com/upload/servicephoto2/' . $detail->service_photo_2
    //                     : null,
    //                 "order_date" => $order->order_date,

    //                 // Category
    //                 "Categories_id" => $category->Categories_id ?? null,
    //                 "Category_name" => $category->Category_name ?? null,
    //                 "Categories_slug" => $category->Categories_slug ?? null,
    //                 "title" => $subcategory->title ?? null,
    //                 "sub_title" => $subcategory->sub_title ?? null,
    //                 "Categories_img" => $category && $category->Categories_img
    //                     ? 'http://admin.auraclap.com/upload/category-image/' . $category->Categories_img
    //                     : null,

    //                 // Subcategory
    //                 "iSubCategoryId" => $subcategory->iSubCategoryId ?? null,
    //                 "iSequence" => $subcategory->iSequence ?? null,
    //                 "iCategoryId" => $subcategory->iCategoryId ?? null,
    //                 "strCategoryName" => $subcategory->strCategoryName ?? null,
    //                 "strSubCategoryName" => $subcategory->strSubCategoryName ?? null,
    //                 "strSlugName" => $subcategory->strSlugName ?? null,
    //                 "SubCategories_img" => $subcategory && $subcategory->SubCategories_img
    //                     ? 'http://admin.auraclap.com/upload/subcategory-images/' . $subcategory->SubCategories_img
    //                     : null,

    //                 // Technician
    //                 "technician" => $technician ? [
    //                     "Technicial_id" => $technician->Technicial_id,
    //                     "name" => $technician->name,
    //                     "email" => $technician->email,
    //                     "mobile_no" => $technician->mobile_no,
    //                     "city" => $technician->city,
    //                     "Technicial_image" => $technician->Technicial_image
    //                         ? 'http://admin.auraclap.com/upload/Technicial/' . $technician->Technicial_image
    //                         : null,
    //                 ] : null,

    //                 "technician_avg_rating" => $average_rating_by_customer
    //                     ? round($average_rating_by_customer, 1)
    //                     : null,

    //                 // Optional: Add order level fields if required
    //                 "order_status" => $detail->order_status,
    //                 "is_review" => $detail->is_review,
    //                 "Customer_Address" => $order->Customer_Address,
    //                 "payment_mode" => $order->payment_mode,
    //                 "isPayment" => $order->isPayment,
    //                 "strtime" => $order->slot->strtime ?? null,
    //                 "order_date" => $order->order_date ?? null,
    //                 "slot_id" => $order->slot_id ?? null,
    //                 "start_otp" => $detail->start_otp ?? null,
    //                 "end_otp" => $detail->end_otp ?? null,
    //             ];
    //         }

    //         if (empty($orderArr)) {
    //             return response()->json([
    //                 'message' => 'Order not found.',
    //                 'success' => false,
    //             ], 404);
    //         }

    //         unset($orderItem);
    //         return response()->json([
    //             'message' => 'Completed Order Fetch Sucessfully',
    //             'success' => true,
    //             'data'    => $orderArr,
    //         ], 200);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Customer is not authorized.',
    //             ], 401);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }


    public function completeorder(Request $request)
    {
        $request->validate(['Customer_id' => 'required|integer']);

        try {
            if (!Auth::guard('customerapi')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }

            // Primary as root:
            // - primary payment filter: isPayment in [0,1] (add payment_mode filter here too if needed)
            // - must have at least one child order with status=2 (completed)
            $primaryOrders = PrimaryOrder::query()
                ->where('iCustomerId', $request->Customer_id)
                ->whereIn('isPayment', [0, 1])
                ->whereHas('orders', function ($q) {
                    $q->where('order_status', 2);
                })
                ->with([
                    'slot',
                    // Child orders: completed only
                    'orders' => function ($q) use ($request) {
                        $q->where('iCustomerId', $request->Customer_id)
                            ->where('order_status', 2)
                            ->orderBy('iOrderId', 'desc');
                    },
                    // OrderDetails; if details also have a status and you want only completed lines, add a where here
                    'orders.orderdetail',
                    'orders.orderdetail.subcategory',
                    'orders.orderdetail.category',
                    'orders.Technicial',        // <- alias for your Technicial relation
                    'orders.customerReviews',
                ])
                ->orderBy('primaryiOrderId', 'desc')
                ->get();

            if ($primaryOrders->isEmpty()) {
                return response()->json([
                    'message' => 'Order not found.',
                    'success' => false,
                ], 404);
            }

            // Shape: primaryOrder -> order -> orderdetail
            $payload = [
                'primaryOrder' => $primaryOrders->map(function ($p) use ($request) {

                    // rollup: with completed filter, this will be 2
                    $primaryOrderStatus = (int) ($p->orders->min('order_status') ?? 2);

                    return [
                        'primaryiOrderId'  => $p->primaryiOrderId,
                        'iCustomerId'      => $p->iCustomerId,
                        'iAmount'          => $p->iAmount,
                        'iNetAmount'       => $p->iNetAmount,
                        'gst_amount'       => $p->gst_amount,
                        'iDiscount'        => $p->iDiscount,
                        'order_date'       => $p->order_date,
                        'payment_mode'     => (string) $p->payment_mode, // if stored on primary
                        'isPayment'        => (int) $p->isPayment,
                        'Customer_Address' => $p->Customer_Address,
                        'slot_id'          => $p->slot_id,
                        'strtime'          => $p->slot->strtime ?? null,
                        'order_status'     => $primaryOrderStatus,

                        'order' => $p->orders->map(function ($o) use ($p, $request) {

                            // per-order technician + rating
                            $tech = $o->technician; // eager-loaded alias
                            $avgByCustomer = $tech && $tech->Technicial_id
                                ? CustomerReview::where('customer_id', $request->Customer_id)
                                ->where('Technicial_id', $tech->Technicial_id)
                                ->avg('rating')
                                : null;

                            // category visuals from first detail
                            $firstDetail = $o->orderdetail->first();
                            $cat = $firstDetail?->category;

                            return [
                                'iOrderId'        => $o->iOrderId,
                                'primaryiOrderId' => (string) $p->primaryiOrderId,
                                'iAmount'         => (string) $o->iAmount,
                                'gst_amount'      => (string) $o->gst_amount,
                                'iNetAmount'      => (string) $o->iNetAmount,
                                'iDiscount'       => (int) ($o->iDiscount ?? 0),
                                'order_status'    => (int) ($o->order_status ?? 2),
                                'end_otp'         => (int) ($o->end_otp ?? 0),
                                'extra_technicial_amount'    => (int) ($o->extra_technicial_amount ?? 0),
                                'extra_gst_amount'    => (int) ($o->extra_gst_amount ?? 0),

                                'technician' => $tech ? [
                                    'Technicial_id'    => $tech->Technicial_id,
                                    'name'             => $tech->name,
                                    'email'            => $tech->email,
                                    'mobile_no'        => $tech->mobile_no,
                                    'city'             => $tech->city,
                                    'Technicial_image' => $tech->Technicial_image
                                        ? 'http://admin.auraclap.com/upload/Technicial/' . $tech->Technicial_image
                                        : null,
                                ] : null,
                                'technician_avg_rating' => $avgByCustomer !== null ? round($avgByCustomer, 1) : null,

                                // derived photos
                                'service_photo_1' => $o?->service_photo_1
                                    ? 'http://admin.auraclap.com/upload/servicephoto1/' . $o->service_photo_1
                                    : null,
                                'service_photo_2' => $o?->service_photo_2
                                    ? 'http://admin.auraclap.com/upload/servicephoto2/' . $o->service_photo_2
                                    : null,

                                'Category_name'   => $cat->Category_name ?? null,
                                'Categories_img'  => ($cat && $cat->Categories_img)
                                    ? 'http://admin.auraclap.com/upload/category-image/' . $cat->Categories_img
                                    : null,

                                'orderdetail' => $o->orderdetail->map(function ($d) {
                                    $sub = $d->subcategory;

                                    return [
                                        'iOrderDetailId'     => $d->iOrderDetailId,
                                        'iOrderId'           => $d->iOrderId,
                                        'title'              => $sub->title ?? null,
                                        'sub_title'          => $sub->sub_title ?? null,
                                        'subcategory_id'     => $d->subcategory_id,
                                        'qty'                => $d->qty,
                                        'rate'               => $d->rate,
                                        'amount'             => $d->amount,
                                        'strSubCategoryName' => $sub->strSubCategoryName ?? null,
                                        'SubCategories_img'  => ($sub && $sub->SubCategories_img)
                                            ? 'http://admin.auraclap.com/upload/subcategory-images/' . $sub->SubCategories_img
                                            : null,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];

            return response()->json([
                'message' => 'Completed Order Fetch Successfully',
                'success' => true,
                'data'    => $payload,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // public function orderHistory(Request $request)
    // {

    //     $request->validate([
    //         'Customer_id' => 'required'
    //     ]);

    //     try {
    //         if (Auth::guard('customerapi')->check()) {

    //             $orderhistory = OrderDetail::orderBy('created_at', 'desc')
    //             ->with([
    //                 'order.slot',
    //                 'order',
    //                 'category',
    //                 'subcategory'
    //             ])
    //             ->where('iCustomerId', $request->Customer_id)
    //             ->whereIn('order_status', [0, 1, 2,3])
    //             ->whereHas('order', function ($query) {
    //                   $query->whereIn('payment_mode', [1, 2, 3])
    //                     ->whereIn('isPayment', [0, 1]);
    //             })
    //             ->get();
    //         $orderArr = [];
    //         foreach ($orderhistory as $detail) {
    //             $order = $detail->order;

    //             if (!$order) continue;

    //             $subcategory = $detail->subcategory;
    //             $category = $detail->category ?? $subcategory?->category;
    //             $technician = $order->technicial;

    //             $average_rating_by_customer = $technician && $technician->Technicial_id
    //                 ? CustomerReview::where('customer_id', $request->Customer_id)
    //                 ->where('Technicial_id', $technician->Technicial_id)
    //                 ->avg('rating')
    //                 : null;

    //             $orderArr[] = [
    //                 // Order Detail
    //                 "iOrderDetailId" => $detail->iOrderDetailId,
    //                 "iOrderId" => $detail->iOrderId,
    //                 "iCustomerId" => $detail->iCustomerId,
    //                 "category_id" => $detail->category_id,
    //                 "Ratecard_id" => $detail->Ratecard_id,
    //                 "qty" => $detail->qty,
    //                 "rate" => $detail->rate,
    //                 "amount" => $detail->amount,
    //                 "net_amount" => $detail->net_amount,
    //                 "GSTAmount" => $detail->GSTAmount,
    //                 "discount_amount" => $detail->discount_amount,
    //                 "subcategory_id" => $detail->subcategory_id,
    //                 "isRefund" => $detail->isRefund,
    //                 "order_date" => $order->order_date,

    //                 // Category
    //                 "Categories_id" => $category->Categories_id ?? null,
    //                 "Category_name" => $category->Category_name ?? null,
    //                 "Categories_slug" => $category->Categories_slug ?? null,
    //                 "title" => $subcategory->title ?? null,
    //                 "sub_title" => $subcategory->sub_title ?? null,
    //                 "Categories_img" => $category && $category->Categories_img
    //                     ? 'http://admin.auraclap.com/upload/category-image/' . $category->Categories_img
    //                     : null,

    //                 // Subcategory
    //                 "iSubCategoryId" => $subcategory->iSubCategoryId ?? null,
    //                 "iSequence" => $subcategory->iSequence ?? null,
    //                 "iCategoryId" => $subcategory->iCategoryId ?? null,
    //                 "strCategoryName" => $subcategory->strCategoryName ?? null,
    //                 "strSubCategoryName" => $subcategory->strSubCategoryName ?? null,
    //                 "strSlugName" => $subcategory->strSlugName ?? null,
    //                 "SubCategories_img" => $subcategory && $subcategory->SubCategories_img
    //                     ? 'http://admin.auraclap.com/upload/subcategory-images/' . $subcategory->SubCategories_img
    //                     : null,

    //                 // Technician
    //                 "technician" => $technician ? [
    //                     "Technicial_id" => $technician->Technicial_id,
    //                     "name" => $technician->name,
    //                     "email" => $technician->email,
    //                     "mobile_no" => $technician->mobile_no,
    //                     "city" => $technician->city,
    //                     "Technicial_image" => $technician->Technicial_image
    //                         ? 'http://admin.auraclap.com/upload/Technicial/' . $technician->Technicial_image
    //                         : null,
    //                 ] : null,

    //                 "technician_avg_rating" => $average_rating_by_customer
    //                     ? round($average_rating_by_customer, 1)
    //                     : null,

    //                 // Optional: Add order level fields if required
    //                 "order_status" => $detail->order_status,
    //                 "payment_mode" => $order->payment_mode,
    //                 "Customer_Address" => $order->Customer_Address,
    //                 "isPayment" => $order->isPayment,
    //                 "strtime" => $order->slot->strtime ?? null,
    //                 "order_date" => $order->order_date ?? null,
    //                 "slot_id" => $order->slot_id ?? null,
    //                 "start_otp" => $detail->start_otp ?? null,
    //                 "end_otp" => $detail->end_otp ?? null,
    //             ];
    //         }

    //             // Check if there are no orders
    //             if (empty($orderArr)) {
    //                 return response()->json([
    //                     'message' => 'Order not found.',
    //                     'success' => false,
    //                 ], 404);
    //             }
    //             return response()->json([
    //                 'message' => 'Order History Fetch Sucessfully',
    //                 'success' => true,
    //                 'data'    => $orderArr,
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Customer is not authorized.',
    //             ], 401);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }


    public function orderHistory(Request $request)
    {
        $request->validate(['Customer_id' => 'required|integer']);

        try {
            if (!Auth::guard('customerapi')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }

            $primaryOrders = PrimaryOrder::query()
                ->where('iCustomerId', $request->Customer_id)
                ->whereIn('isPayment', [0, 1, 2])
                ->whereHas('orders', function ($q) {
                    $q->whereIn('order_status', [0, 1, 2, 3]);
                })
                ->with([
                    'slot',
                    'orders' => function ($q) use ($request) {
                        $q->where('iCustomerId', $request->Customer_id)
                            ->whereIn('order_status', [0, 1, 2, 3])
                            ->orderBy('iOrderId', 'desc');
                    },
                    'orders.orderdetail',
                    'orders.orderdetail.subcategory',
                    'orders.orderdetail.category',
                    'orders.Technicial',
                    'orders.customerReviews',
                ])
                ->orderBy('primaryiOrderId', 'desc')
                ->get();

            if ($primaryOrders->isEmpty()) {
                return response()->json([
                    'message' => 'Order not found.',
                    'success' => false,
                ], 404);
            }

            $payload = [
                'primaryOrder' => $primaryOrders->map(function ($p) use ($request) {
                    // roll up primary status (min across children; 0=available → 3=canceled)
                    $primaryOrderStatus = (int) ($p->orders->min('order_status') ?? 0);

                    return [
                        'primaryiOrderId'  => $p->primaryiOrderId,
                        'iCustomerId'      => $p->iCustomerId,
                        'iAmount'          => $p->iAmount,
                        'iNetAmount'       => $p->iNetAmount,
                        'gst_amount'       => $p->gst_amount,
                        'iDiscount'        => $p->iDiscount,
                        'order_date'       => $p->order_date,
                        // include primary-level payment fields if you store them
                        'payment_mode'     => isset($p->payment_mode) ? (string) $p->payment_mode : null,
                        'isPayment'        => isset($p->isPayment) ? (int) $p->isPayment : null,
                        'Customer_Address' => $p->Customer_Address,
                        'slot_id'          => $p->slot_id,
                        'strtime'          => $p->slot->strtime ?? null,
                        'order_status'     => $primaryOrderStatus,

                        'order' => $p->orders->map(function ($o) use ($p, $request) {
                            $tech = $o->technician;
                            $avgByCustomer = $tech && $tech->Technicial_id
                                ? CustomerReview::where('customer_id', $request->Customer_id)
                                ->where('Technicial_id', $tech->Technicial_id)
                                ->avg('rating')
                                : null;

                            $firstDetail = $o->orderdetail->first();
                            $cat = $firstDetail?->category;

                            return [
                                'iOrderId'        => $o->iOrderId,
                                'extra_technicial_amount'    => (int) ($o->extra_technicial_amount ?? 0),
                                'extra_gst_amount'    => (int) ($o->extra_gst_amount ?? 0),
                                'primaryiOrderId' => (string) $p->primaryiOrderId,
                                'iAmount'         => (string) $o->iAmount,
                                'gst_amount'      => (string) $o->gst_amount,
                                'iNetAmount'      => (string) $o->iNetAmount,
                                'iDiscount'       => (int) ($o->iDiscount ?? 0),
                                'order_status'    => (int) ($o->order_status ?? 0),
                                'payment_mode'    => (int) ($o->payment_mode ?? 0),
                                'isPayment'       => (int) ($o->isPayment ?? 0),
                                'order_date'      => $o->order_date,
                                'Customer_Address' => $o->Customer_Address,
                                'slot_id'         => $o->slot_id,
                                'strtime'         => $o->slot->strtime ?? null,

                                'technician' => $tech ? [
                                    'Technicial_id'    => $tech->Technicial_id,
                                    'name'             => $tech->name,
                                    'email'            => $tech->email,
                                    'mobile_no'        => $tech->mobile_no,
                                    'city'             => $tech->city,
                                    'Technicial_image' => $tech->Technicial_image
                                        ? 'http://admin.auraclap.com/upload/Technicial/' . $tech->Technicial_image
                                        : null,
                                ] : null,
                                'technician_avg_rating' => $avgByCustomer !== null ? round($avgByCustomer, 1) : null,

                                'service_photo_1' => $o?->service_photo_1
                                    ? 'http://admin.auraclap.com/upload/servicephoto1/' . $o->service_photo_1
                                    : null,
                                'service_photo_2' => $o?->service_photo_2
                                    ? 'http://admin.auraclap.com/upload/servicephoto2/' . $o->service_photo_2
                                    : null,

                                'Category_name'   => $cat->Category_name ?? null,
                                'Categories_img'  => ($cat && $cat->Categories_img)
                                    ? 'http://admin.auraclap.com/upload/category-image/' . $cat->Categories_img
                                    : null,

                                'orderdetail' => $o->orderdetail->map(function ($d) {
                                    $sub = $d->subcategory;

                                    return [
                                        'iOrderDetailId'     => $d->iOrderDetailId,
                                        'iOrderId'           => $d->iOrderId,
                                        'title'              => $sub->title ?? null,
                                        'sub_title'          => $sub->sub_title ?? null,
                                        'subcategory_id'     => $d->subcategory_id,
                                        'qty'                => $d->qty,
                                        'rate'               => $d->rate,
                                        'amount'             => $d->amount,
                                        'strSubCategoryName' => $sub->strSubCategoryName ?? null,
                                        'SubCategories_img'  => ($sub && $sub->SubCategories_img)
                                            ? 'http://admin.auraclap.com/upload/subcategory-images/' . $sub->SubCategories_img
                                            : null,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];

            return response()->json([
                'message' => 'Order History Fetch Successfully',
                'success' => true,
                'data'    => $payload,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function cancelorder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'Customer_id' => 'required',
            'reason_id' => 'required',
        ]);


        try {
            if (Auth::guard('customerapi')->check()) {

                $order = Order::where('iOrderId', $request->order_id)
                    ->where('iCustomerId', $request->Customer_id)
                    ->first();



                if (!$order) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found.',
                    ], 404);
                }

                if ($order->Technicial_id !== 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This order is already accepted by a technician and cannot be cancelled.',
                    ], 403);
                }

                $order->update(['order_status' => 3, 'reason_id' => $request->reason_id]);

                $this->notification([
                    "Technicial_id" => $request->Technicial_id ?? '0',
                    "customer_id" => $request->Customer_id ?? '0',
                    "title"   => "AuraClap",
                    "body"    => "Oops!! Your order has been cancelled.",
                    "order_id"    => $request->order_id ?? '0',
                    "type"    => "order_cancel",
                    "service" => "Customer",
                    "flag" => 1,

                ]);
                return response()->json([
                    'message' => 'Order Cancelled Successfully',
                    'success' => true,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    // public function cancelorderlist(Request $request)
    // {
    //     $request->validate([
    //         'Customer_id' => 'required'
    //     ]);

    //     try {
    //         if (Auth::guard('customerapi')->check()) {

    //             $cancelorderslist = Order::orderBy('created_at', 'desc')
    //             ->with([
    //                 'order.slot',
    //                 'order',
    //                 'Technicial',
    //                 'customerReviews',
    //                 'category',
    //                 'subcategory'
    //             ])
    //             ->where('iCustomerId', $request->Customer_id)
    //             ->whereIn('order_status', [3])
    //             ->whereHas('order', function ($query) {
    //                   $query->whereIn('payment_mode', [1, 2, 3])
    //                     ->whereIn('isPayment', [0, 1]);
    //             })
    //             ->orderBy('iOrderId', 'desc')
    //             ->get();

    //           $orderArr = [];
    //         foreach ($cancelorderslist as $detail) {
    //             $order = $detail->order;

    //             if (!$order) continue;

    //             $subcategory = $detail->subcategory;
    //             $category = $detail->category ?? $subcategory?->category;
    //             $technician = $order->technicial;

    //             $average_rating_by_customer = $technician && $technician->Technicial_id
    //                 ? CustomerReview::where('customer_id', $request->Customer_id)
    //                 ->where('Technicial_id', $technician->Technicial_id)
    //                 ->avg('rating')
    //                 : null;

    //             $orderArr[] = [
    //                 // Order Detail
    //                 "iOrderDetailId" => $detail->iOrderDetailId,
    //                 "iOrderId" => $detail->iOrderId,
    //                 "iCustomerId" => $detail->iCustomerId,
    //                 "category_id" => $detail->category_id,
    //                 "Ratecard_id" => $detail->Ratecard_id,
    //                 "qty" => $detail->qty,
    //                 "rate" => $detail->rate,
    //                 "amount" => $detail->amount,
    //                 "net_amount" => $detail->net_amount,
    //                 "GSTAmount" => $detail->GSTAmount,
    //                 "discount_amount" => $detail->discount_amount,
    //                 "subcategory_id" => $detail->subcategory_id,
    //                 "isRefund" => $detail->isRefund,
    //                 "order_date" => $order->order_date,

    //                 // Category
    //                 "Categories_id" => $category->Categories_id ?? null,
    //                 "Category_name" => $category->Category_name ?? null,
    //                 "Categories_slug" => $category->Categories_slug ?? null,
    //                 "title" => $subcategory->title ?? null,
    //                 "sub_title" => $subcategory->sub_title ?? null,
    //                 "Categories_img" => $category && $category->Categories_img
    //                     ? 'http://admin.auraclap.com/upload/category-image/' . $category->Categories_img
    //                     : null,

    //                 // Subcategory
    //                 "iSubCategoryId" => $subcategory->iSubCategoryId ?? null,
    //                 "iSequence" => $subcategory->iSequence ?? null,
    //                 "iCategoryId" => $subcategory->iCategoryId ?? null,
    //                 "strCategoryName" => $subcategory->strCategoryName ?? null,
    //                 "strSubCategoryName" => $subcategory->strSubCategoryName ?? null,
    //                 "strSlugName" => $subcategory->strSlugName ?? null,
    //                 "SubCategories_img" => $subcategory && $subcategory->SubCategories_img
    //                     ? 'http://admin.auraclap.com/upload/subcategory-images/' . $subcategory->SubCategories_img
    //                     : null,

    //                 // Technician
    //                 "technician" => $technician ? [
    //                     "Technicial_id" => $technician->Technicial_id,
    //                     "name" => $technician->name,
    //                     "email" => $technician->email,
    //                     "mobile_no" => $technician->mobile_no,
    //                     "city" => $technician->city,
    //                     "Technicial_image" => $technician->Technicial_image
    //                         ? 'http://admin.auraclap.com/upload/Technicial/' . $technician->Technicial_image
    //                         : null,
    //                 ] : null,

    //                 "technician_avg_rating" => $average_rating_by_customer
    //                     ? round($average_rating_by_customer, 1)
    //                     : null,

    //                 // Optional: Add order level fields if required
    //                 "order_status" => $detail->order_status,
    //                 "payment_mode" => $order->payment_mode,
    //                 "Customer_Address" => $order->Customer_Address,
    //                 "isPayment" => $order->isPayment,
    //                 "strtime" => $order->slot->strtime ?? null,
    //                 "order_date" => $order->order_date ?? null,
    //                 "slot_id" => $order->slot_id ?? null,
    //                 "start_otp" => $detail->start_otp ?? null,
    //                 "end_otp" => $detail->end_otp ?? null,
    //             ];
    //         }

    //             // Check if there are no orders
    //             if (empty($orderArr)) {
    //                 return response()->json([
    //                     'message' => 'Order not found.',
    //                     'success' => false,
    //                 ], 404);
    //             }
    //             return response()->json([
    //                 'message' => 'Cancel Order Fetch Sucessfully',
    //                 'success' => true,
    //                 'data'    => $orderArr,
    //             ], 200);
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Customer is not authorized.',
    //             ], 401);
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => $th->getMessage()], 500);
    //     }
    // }

    public function cancelorderlist(Request $request)
    {
        $request->validate(['Customer_id' => 'required|integer']);

        try {
            if (!Auth::guard('customerapi')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }

            // Primary as root:
            // - must have at least one child order with order_status = 3 (canceled)
            // - (optional) keep payment checks; here I kept them on ORDERS to mirror your current style
            $primaryOrders = PrimaryOrder::query()
                ->where('iCustomerId', $request->Customer_id)
                ->whereIn('isPayment', [0, 1])
                ->whereHas('orders', function ($q) {
                    $q->where('order_status', 3);
                })
                ->with([
                    'slot',
                    'orders' => function ($q) use ($request) {
                        $q->where('iCustomerId', $request->Customer_id)
                            ->where('order_status', 3)
                            ->orderBy('iOrderId', 'desc');
                    },
                    'orders.orderdetail',
                    'orders.orderdetail.subcategory',
                    'orders.orderdetail.category',
                    'orders.Technicial',
                    'orders.customerReviews',
                ])
                ->orderBy('primaryiOrderId', 'desc')
                ->get();

            if ($primaryOrders->isEmpty()) {
                return response()->json([
                    'message' => 'Order not found.',
                    'success' => false,
                ], 404);
            }

            // Shape: primaryOrder -> order -> orderdetail (all canceled)
            $payload = [
                'primaryOrder' => $primaryOrders->map(function ($p) use ($request) {

                    // rollup status (with filter above this will be 3)
                    $primaryOrderStatus = (int) ($p->orders->min('order_status') ?? 3);

                    return [
                        'primaryiOrderId'  => $p->primaryiOrderId,
                        'iCustomerId'      => $p->iCustomerId,
                        'iAmount'          => $p->iAmount,
                        'iNetAmount'       => $p->iNetAmount,
                        'gst_amount'       => $p->gst_amount,
                        'iDiscount'        => $p->iDiscount,
                        'order_date'       => $p->order_date,
                        // include if present on primary
                        'payment_mode'     => isset($p->payment_mode) ? (int) $p->payment_mode : null,
                        'isPayment'        => isset($p->isPayment) ? (int) $p->isPayment : null,
                        'Customer_Address' => $p->Customer_Address,
                        'slot_id'          => $p->slot_id,
                        'strtime'          => $p->slot->strtime ?? null,
                        'order_status'     => $primaryOrderStatus,

                        'order' => $p->orders->map(function ($o) use ($p, $request) {
                            $tech = $o->technician; // eager-loaded alias
                            $avgByCustomer = $tech && $tech->Technicial_id
                                ? CustomerReview::where('customer_id', $request->Customer_id)
                                ->where('Technicial_id', $tech->Technicial_id)
                                ->avg('rating')
                                : null;

                            $firstDetail = $o->orderdetail->first();
                            $cat = $firstDetail?->category;

                            return [
                                'iOrderId'        => $o->iOrderId,
                                'primaryiOrderId' => (string) $p->primaryiOrderId,
                                'iAmount'         => (string) $o->iAmount,
                                'gst_amount'      => (string) $o->gst_amount,
                                'iNetAmount'      => (string) $o->iNetAmount,
                                'iDiscount'       => (int) ($o->iDiscount ?? 0),
                                'order_status'    => (int) ($o->order_status ?? 3),
                                'payment_mode'    => (int) ($o->payment_mode ?? 0),
                                'isPayment'       => (int) ($o->isPayment ?? 0),
                                'order_date'      => $o->order_date,
                                'Customer_Address' => $o->Customer_Address,
                                'slot_id'         => $o->slot_id,
                                'strtime'         => $o->slot->strtime ?? null,

                                'technician' => $tech ? [
                                    'Technicial_id'    => $tech->Technicial_id,
                                    'name'             => $tech->name,
                                    'email'            => $tech->email,
                                    'mobile_no'        => $tech->mobile_no,
                                    'city'             => $tech->city,
                                    'Technicial_image' => $tech->Technicial_image
                                        ? 'http://admin.auraclap.com/upload/Technicial/' . $tech->Technicial_image
                                        : null,
                                ] : null,
                                'technician_avg_rating' => $avgByCustomer !== null ? round($avgByCustomer, 1) : null,

                                // derived photos (optional)
                                'service_photo_1' => $firstDetail?->service_photo_1
                                    ? 'http://admin.auraclap.com/upload/servicephoto1/' . $firstDetail->service_photo_1
                                    : null,
                                'service_photo_2' => $firstDetail?->service_photo_2
                                    ? 'http://admin.auraclap.com/upload/servicephoto2/' . $firstDetail->service_photo_2
                                    : null,

                                'Category_name'   => $cat->Category_name ?? null,
                                'Categories_img'  => ($cat && $cat->Categories_img)
                                    ? 'http://admin.auraclap.com/upload/category-image/' . $cat->Categories_img
                                    : null,

                                'orderdetail' => $o->orderdetail->map(function ($d) {
                                    $sub = $d->subcategory;

                                    return [
                                        'iOrderDetailId'     => $d->iOrderDetailId,
                                        'iOrderId'           => $d->iOrderId,
                                        'title'              => $sub->title ?? null,
                                        'sub_title'          => $sub->sub_title ?? null,
                                        'subcategory_id'     => $d->subcategory_id,
                                        'qty'                => $d->qty,
                                        'rate'               => $d->rate,
                                        'amount'             => $d->amount,
                                        'strSubCategoryName' => $sub->strSubCategoryName ?? null,
                                        'SubCategories_img'  => ($sub && $sub->SubCategories_img)
                                            ? 'http://admin.auraclap.com/upload/subcategory-images/' . $sub->SubCategories_img
                                            : null,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                })->values(),
            ];

            return response()->json([
                'message' => 'Cancel Order Fetch Successfully',
                'success' => true,
                'data'    => $payload,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function pdfgenerate(Request $request)
    {
        $request->validate([
            'Customer_id' => 'required',
            'order_id' => 'required'
        ]);

        try {
            if (Auth::guard('customerapi')->check()) {
                // Fetch invoice and order
                $existingInvoice = OrderInvoice::where('order_id', $request->order_id)->first();

                $Order = OrderDetail::with('subcategory', 'category', 'invoice')
                    ->where('iOrderDetailId', $request->order_id)
                    ->first();

                if (!$Order) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Order not found.',
                    ], 404);
                }

                // Prepare orderArr data
                $orderArr = [];

                if ($Order->subcategory) {
                    $orderArr[] = [
                        'iDiscount' => $Order->discount_amount ?? 0,
                        'list' => [[
                            'strSubCategoryName' => $Order->subcategory->strSubCategoryName ?? '',
                            'qty' => $Order->qty,
                            'rate' => $Order->rate,
                            'amount' => $Order->amount,
                        ]]
                    ];
                }

                // Generate filename
                $filename = 'completeorder_' . Str::random(10) . '.pdf';

                // Load PDF view
                $pdf = Pdf::loadView('pdf.completeorder_invoice', [
                    'existingInvoice' => $existingInvoice,
                    'Order' => $Order,
                    'orderArr' => $orderArr
                ]);

                // Save PDF to public folder
                $root = $_SERVER['DOCUMENT_ROOT'];
                $pdfPath = $root . '/upload/' . $filename;

                if (!File::isDirectory(dirname($pdfPath))) {
                    File::makeDirectory(dirname($pdfPath), 0755, true);
                }

                $pdf->save($pdfPath);

                $pdfUrl = 'http://admin.auraclap.com/upload/' . $filename;

                return response()->json([
                    'message' => 'Invoice generated successfully',
                    'success' => true,
                    'pdf_url' => $pdfUrl
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function AddCart(Request $request)
    {

        try {
            if (Auth::guard('customerapi')->check()) {

                $request->validate([
                    "Customer_id" => 'required',
                    "Qty" => 'required',
                    "rate_id" => 'required',
                    "Categories_id" => 'required',
                    "SubCategories_id" => 'required',

                ]);
                $ratedata = Managerate::where('rate_id', $request->rate_id)->first();
                $rate = $ratedata->amount;
                $Qty = $request->Qty;
                $amount = $rate * $Qty;

                $Cartdata = array(

                    "Customer_id" => $request->Customer_id,
                    "Categories_id" => $request->Categories_id,
                    "subcate_id" => $request->SubCategories_id,
                    "Qty" => $Qty,
                    "rate" => $rate,
                    "rate_id" => $request->rate_id,
                    "amount" => $amount,
                    "strIP" => $request->ip(),
                    "created_at" => now(),
                );

                $Cart = Cart::create($Cartdata);
                // $totalamount = Cart::where([
                //     ['rate_id', '=', $request->rate_id],
                //     ['Customer_id', '=', $request->Customer_id]
                // ])->sum('amount');

                $totalamount = Cart::where('Customer_id', $request->Customer_id)->sum('amount');
                DB::commit();
                return response()->json([
                    'success' => true,
                    'total_amount' => $totalamount,
                    'message' => 'Add to Cart Successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
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

    public function ViewCart(Request $request)
    {
        try {
            if (Auth::guard('customerapi')->check()) {
                $request->validate([
                    "Customer_id" => 'required',
                ]);


                // $cartdata = Cart::with('category.subcategories', 'customer')->where('Customer_id', $request->Customer_id)->get();
                $cartdata = Cart::with('subcategory.category', 'customer')->where('Customer_id', $request->Customer_id)->get();

                // dd($cartdata);
                $cartarr = [];
                $totalAmount = 0;
                $totalGST = 0;

                foreach ($cartdata as $cart) {
                    // $subcategories = $cart->category && $cart->category->subcategories
                    //     ? $cart->category->subcategories->first()
                    //     : null;

                    $subcategories = $cart->subcategory;

                    $gstAmount = round($cart->amount * 0.18, 2); // 18% GST
                    $itemTotal = round($cart->amount + $gstAmount, 2);

                    $cartarr[] = [
                        "Cart_id" => $cart->Cart_id,
                        "Customer_id" => $cart->Customer_id,
                        "Customer_name" => optional($cart->customer)->Customer_name,
                        "Customer_Address" => optional($cart->customer)->Customer_Address,
                        "Pincode" => optional($cart->customer)->Pincode,
                        "Customer_phone" => optional($cart->customer)->Customer_phone,

                        "Categories_id" => $cart->Categories_id,
                        "rate" => $cart->rate,
                        "amount" => $cart->amount,
                        "Qty" => $cart->Qty,
                        "GST_18_percent" => $gstAmount,
                        "Total_with_GST" => $itemTotal,
                        "created_at" => $cart->created_at,

                        "subcategories" => $subcategories ? [
                            "iSubCategoryId" => $subcategories->iSubCategoryId,
                            "strCategoryName" => optional($subcategories->category)->Category_name,
                            "strSubCategoryName" => $subcategories->strSubCategoryName,
                            "SubCategories_img" => "http://admin.auraclap.com/upload/subcategory-images/{$subcategories->SubCategories_img}",
                        ] : null,
                    ];

                    $totalAmount += $cart->amount;
                    $totalGST += $gstAmount;
                }

                return response()->json([
                    'success' => true,
                    'data' => $cartarr,
                    'total_amount' => round($totalAmount),
                    'total_gst' =>  number_format($totalGST, 2),
                    'grand_total' => round($totalAmount + $totalGST),
                    'message' => 'Cart fetched successfully.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => implode(', ', Arr::flatten($e->errors())),
            ], 422);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }
    public function qtyupdate(Request $request)
    {
        $request->validate([
            "Cart_id" => 'required',
            "Qty" => 'required|numeric|min:1', // Ensure quantity is a valid number
        ]);

        try {
            // Find the cart item
            if (Auth::guard('customerapi')->check()) {
                $cart = Cart::where('Cart_id', $request->Cart_id)->first();

                if (!$cart) {
                    return response()->json([
                        'message' => 'Cart item not found.',
                        'success' => false,
                    ], 404);
                }

                // Calculate new amount
                $amount = $cart->rate * $request->Qty;

                // Update the cart
                $cart->update([
                    'Qty' => $request->Qty,
                    'amount' => $amount,
                ]);

                // Fetch the updated cart item
                $updatedCart = Cart::where('Cart_id', $request->Cart_id)->first();

                return response()->json([
                    'message' => 'Quantity updated successfully',
                    'success' => true,
                    'data' => $updatedCart, // Return updated cart details
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function Removeqty_fromcart(Request $request)
    {
        $request->validate(
            [
                "Cart_id" => 'required'
            ]
        );

        try {
            if (Auth::guard('customerapi')->check()) {
                $data = Cart::where('Cart_id', $request->Cart_id)->delete();
                return response()->json([
                    'message' => 'Qty delete successfully',
                    'success' => true,

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function Remove_cart(Request $request)
    {
        $request->validate(
            [
                "Customer_id" => 'required'
            ]
        );

        try {
            if (Auth::guard('customerapi')->check()) {

                $data = Cart::where('Customer_id', $request->Customer_id)->delete();
                return response()->json([
                    'message' => 'Cart Remove successfully',
                    'success' => true,

                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function offerlist(Request $request)
    {
        try {
            if (Auth::guard('customerapi')->check()) {

                $Today = now()->toDateString();

                $list = Offer::select(
                    "id",
                    "text",
                    "value",
                    "startdate",
                    "enddate"
                )
                    ->where('iStatus', 1)
                    ->where('isDelete', 0)
                    ->whereDate('startdate', '<=', $Today)
                    ->whereDate('enddate', '>=', $Today)
                    ->orderBy('id', 'desc')
                    ->get();

                return response()->json([
                    'success' => true,
                    'message' => "Successfully fetched active Offer List...",
                    'data' => $list,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    // public function order(Request $request)
    // {

    //     try {

    //         if (Auth::guard('customerapi')->check()) {

    //             $request->validate([
    //                 "Customer_id" => 'required',
    //                 "city_id" => 'required',
    //                 "Amount" => 'required',
    //                 "gst_amount" => 'required',
    //                 "grand_amount" => 'required',
    //                 "payment_mode" => 'required',
    //                 "Customer_name" => 'nullable|string',
    //                 "Customer_Address" => 'nullable|string',
    //                 "Customer_phone" => 'required',
    //                 "Pincode" => 'required',
    //                 "order_date" => 'required|date',
    //                 "slot_id" => 'required',
    //                 "discount" => 'nullable',
    //                 "dis_per" => 'nullable',
    //             ]);

    //             $Pincode = Pincode::where('pincode', $request->Pincode)->first();

    //             if (!$Pincode) {
    //                 return response()->json([
    //                     'message' => 'Sorry, Orders are not available in this area',
    //                     'success' => false,
    //                 ], 404);
    //             }

    //             $Customer = Cart::where('Customer_id', $request->Customer_id)->first();

    //             if (!$Customer) {
    //                 return response()->json([
    //                     'message' => 'Item not available in cart',
    //                     'success' => false,
    //                 ], 404);
    //             }

    //             $Categoriesid = $Customer->Categories_id;

    //             $categoryNames = Cart::where('Customer_id', $request->Customer_id)
    //                 ->join('Categories', 'Cart.Categories_id', '=', 'Categories.Categories_id')
    //                 ->pluck('Categories.Category_name')
    //                 ->unique()
    //                 ->toArray();


    //             $categoryList = implode('/', $categoryNames);
    //             // Now get technicians for all those categories (instead of one category_id)
    //             $categoryIds = Cart::where('Customer_id', $request->Customer_id)
    //                 ->pluck('Categories_id')
    //                 ->unique()
    //                 ->toArray();


    //             $technicians = DB::table('Technicial')
    //                 ->join('Technicial_Pincode', 'Technicial.Technicial_id', '=', 'Technicial_Pincode.Technicial_id')
    //                 ->join('Technicial_Service', 'Technicial.Technicial_id', '=', 'Technicial_Service.Technicial_id')
    //                 ->where('Technicial_Pincode.Pincode_id', $Pincode->pin_id)
    //                 ->where('Technicial_Service.Category_id', $categoryIds)
    //                 ->select('Technicial.*')
    //                 ->distinct()
    //                 ->get();


    //             foreach ($technicians as $pinc) {
    //                 $array = array(
    //                     "Technicial_id" => $pinc->Technicial_id,
    //                     "order_id" => $request->order_id ?? '0',
    //                     "customer_id" => $request->Customer_id,
    //                     'title' => 'AuraClap',
    //                     'body' => "New Lead:\nYou have a new lead for {$categoryList} Repair.\nClaim it quick and start working.",
    //                     'guid' => '0',
    //                     'type' => "New Lead",
    //                     'service' => "Techincial"
    //                 );
    //                 $this->notification($array);
    //             }

    //             $orderdata = array(

    //                 "iCustomerId" => $request->Customer_id,
    //                 "iAmount" => $request->Amount,
    //                 "gst_amount" => $request->gst_amount,
    //                 "iDiscount" => $request->discount,
    //                 "iNetAmount" => $request->grand_amount,
    //                 "order_date" => $request->order_date,
    //                 "slot_id" => $request->slot_id,
    //                 "dis_per" => $request->dis_per,
    //                 "city_id" => $request->city_id,
    //                 "payment_mode" => $request->payment_mode,
    //                 "Customer_name" => $request->Customer_name,
    //                 "Customer_Address" => $request->Customer_Address,
    //                 "Customer_phone" => $request->Customer_phone,
    //                 "Pincode" => $request->Pincode,
    //                 "strIP" => $request->ip(),
    //                 "created_at" => now(),
    //             );


    //             $Order = Order::create($orderdata);
    //             $existingOrderCount = Order::where('iCustomerId', $request->Customer_id)->count();
    //             $customerInfo = Customer::where('Customer_id', $request->Customer_id)->first();

    //             //dd($existingOrderCount);
    //             if ($existingOrderCount) {
    //                 // First order: update customer profile
    //                 $customerInfo->update([
    //                     'Customer_name' => $request->Customer_name,
    //                     'Customer_Address' => $request->Customer_Address,
    //                     'Pincode' => $request->Pincode,
    //                 ]);
    //             }

    //             $Cartdata = Cart::where('Customer_id', $request->Customer_id)->get();

    //             foreach ($Cartdata as $cart) {
    //               $discount = round(($cart->amount * ($request->dis_per ?? 0)) / 100);
    //               $afterdiscountvalue = round($cart->amount - $discount);
    //               $gstAmount = round($afterdiscountvalue * 0.18, 2);
    //               $netAmount = $afterdiscountvalue + $gstAmount;

    //                 $orderdetaildata = [
    //                     "iOrderId" => $Order->iOrderId,
    //                     "Ratecard_id" => $cart->rate_id,
    //                     "iCustomerId" => $cart->Customer_id,
    //                     "category_id" => $cart->Categories_id,
    //                     "subcategory_id" => $cart->subcate_id,
    //                     "amount" => $cart->amount,
    //                     "rate" => $cart->rate,
    //                     "qty" => $cart->Qty,
    //                     "net_amount" => round($netAmount,2),
    //                     "GSTAmount" => round($gstAmount,2),
    //                     "discount_amount" => $discount,
    //                     "strIP" => $request->ip(),
    //                     "created_at" => now(),
    //                 ];

    //                 OrderDetail::create($orderdetaildata);
    //             }

    //             $this->notification([
    //                 "Technicial_id" => $request->Technicial_id ?? '0',
    //                 "customer_id" => $request->Customer_id ?? '0',
    //                 "title"   => "AuraClap",
    //                 "body"    => "Dear user, Thank you for booking service from AURACLAP. We will assign you our technician soon!!",
    //                 "order_id"    => $request->Order_id ?? '0',
    //                 "type"    => "order_confirm",
    //                 "service" => "Customer",
    //                 "flag" => 1,

    //             ]);

    //             $api = new Api(
    //                 config('services.razorpay.key'),
    //                 config('services.razorpay.secret')
    //             );
    //             $OrderAmount = $Order->iNetAmount * 100;
    //             $orderData = [
    //                 'receipt'         => $Order->iOrderId . '-' . date('dmYHis'),
    //                 'amount'          => $Order->iNetAmount * 100,
    //                 'currency'        => 'INR',
    //             ];
    //             // dd($orderData);
    //             $razorpayOrder = $api->order->create($orderData);
    //             $orderId = $razorpayOrder['id'];
    //             $razorpayResponse = array(
    //                 'order_id' => $orderId,
    //                 'oid' => $Order->iOrderId,
    //                 'amount' => $Order->iNetAmount,
    //                 'currency' => 'INR',
    //                 'receipt' => $razorpayOrder['receipt'],
    //             );
    //             Cart::where('Customer_id', $request->Customer_id)->delete();
    //             return [
    //                 'success' => true,
    //                 "message" => "order created successfully !",
    //                 "data" => $orderdetaildata,
    //                 "razorpayResponse" => $razorpayResponse
    //             ];
    //         } else {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Customer is not authorized.',
    //             ], 401);
    //         }
    //     } catch (ValidationException $e) {
    //         // Format validation errors as a single string
    //         $errorMessage = implode(', ', Arr::flatten($e->errors()));

    //         return response()->json([
    //             'success' => false,
    //             'message' => $errorMessage,
    //         ], 422);
    //     } catch (\Throwable $th) {

    //         return response()->json([
    //             'success' => false,
    //             'error' => $th->getMessage(),
    //         ], 500);
    //     }
    // }


    public function order(Request $request)
    {
        try {
            if (!Auth::guard('customerapi')->check()) {
                return response()->json(['success' => false, 'message' => 'Customer is not authorized.'], 401);
            }

            $request->validate([
                "Customer_id"     => 'required|integer',
                "city_id"         => 'required|integer',
                "Amount"          => 'required|numeric',
                "gst_amount"      => 'required|numeric',
                "grand_amount"    => 'required|numeric',
                "payment_mode"    => 'required|string',
                "Customer_name"   => 'nullable|string',
                "Customer_Address" => 'nullable|string',
                "Customer_phone"  => 'required|string',
                "Pincode"         => 'required|string',
                "order_date"      => 'required|date',
                "slot_id"         => 'required|integer',
                "discount"        => 'nullable|numeric',
                "dis_per"         => 'nullable|numeric',
            ]);

            // 1) serviceable pincode?
            $Pincode = Pincode::where('pincode', $request->Pincode)->first();
            if (!$Pincode) {
                return response()->json([
                    'message' => 'Sorry, Orders are not available in this area',
                    'success' => false,
                ], 404);
            }

            // 2) cart items
            $cartItems = Cart::where('Customer_id', $request->Customer_id)->get();
            if ($cartItems->isEmpty()) {
                return response()->json([
                    'message' => 'Item not available in cart',
                    'success' => false,
                ], 404);
            }

            // 3) group cart by category for category-wise orders
            $byCategory = $cartItems->groupBy('Categories_id');

            // 4) precompute totals (per line => after discount => GST 18%)
            $disPer = (float) ($request->dis_per ?? 0);
            $primaryGross = 0;    // sum of line amounts before discount
            $primaryDiscount = 0; // sum of discount amounts
            $primaryGst = 0;      // sum of gst
            $primaryNet = 0;      // sum of net

            // map lines with computed amounts; also accumulate per-category
            $lineMap = []; // [category_id][] = lineArr
            foreach ($cartItems as $row) {
                $lineGross = (float) $row->amount; // your cart->amount
                $lineDiscount = round(($lineGross * $disPer) / 100);
                $afterDiscount = max(0, $lineGross - $lineDiscount);
                $gstAmount = round($afterDiscount * 0.18, 2);
                $lineNet = round($afterDiscount + $gstAmount, 2);

                $primaryGross += $lineGross;
                $primaryDiscount += $lineDiscount;
                $primaryGst += $gstAmount;
                $primaryNet += $lineNet;

                $lineMap[$row->Categories_id][] = [
                    'Ratecard_id'    => $row->rate_id,
                    'iCustomerId'    => $row->Customer_id,
                    'category_id'    => $row->Categories_id,
                    'subcategory_id' => $row->subcate_id,
                    'amount'         => $row->amount,
                    'rate'           => $row->rate,
                    'qty'            => $row->Qty,
                    'discount_amount' => $lineDiscount,
                    'GSTAmount'      => $gstAmount,
                    'net_amount'     => $lineNet,
                ];
            }

            // 5) category names for tech notification body
            $categoryNames = Cart::where('Customer_id', $request->Customer_id)
                ->join('Categories', 'Cart.Categories_id', '=', 'Categories.Categories_id')
                ->pluck('Categories.Category_name')
                ->unique()
                ->toArray();
            $categoryList = implode('/', $categoryNames);
            $categoryIds = array_keys($lineMap);

            // 6) transaction: create Primary -> Orders(per category) -> OrderDetails(per subcategory)
            $razorpayResponse = null;
            $createdOrders = [];

            DB::beginTransaction();

            // (A) PRIMARY row (one per checkout)
            $primary = PrimaryOrder::create([
                "iCustomerId"     => $request->Customer_id,
                "iAmount"         => round($primaryGross, 2),
                "gst_amount"      => round($primaryGst, 2),
                "iDiscount"       => round($primaryDiscount, 2),
                "iNetAmount"      => round($primaryNet, 2),
                "order_date"      => $request->order_date,
                "slot_id"         => $request->slot_id,
                "payment_mode"    => $request->payment_mode,
                "dis_per"         => $disPer,
                "city_id"         => $request->city_id,
                "Customer_name"   => $request->Customer_name,
                "Customer_Address" => $request->Customer_Address,
                "Customer_phone"  => $request->Customer_phone,
                "Pincode"         => $request->Pincode,
                "strIP"           => $request->ip(),
            ]);

            // (B) ORDERS (per category)
            foreach ($lineMap as $catId => $lines) {
                $catGross = array_sum(array_column($lines, 'amount'));
                $catDiscount = array_sum(array_column($lines, 'discount_amount'));
                $catGst = array_sum(array_column($lines, 'GSTAmount'));
                $catNet = array_sum(array_column($lines, 'net_amount'));

                $order = Order::create([
                    "order_primary_id"  => $primary->primaryiOrderId,
                    "iCustomerId" => $request->Customer_id,
                    "payment_mode"    => $request->payment_mode,
                    "category_id" => $catId,
                    "iAmount"     => round($catGross, 2),
                    "gst_amount"  => round($catGst, 2),
                    "iDiscount"   => round($catDiscount, 2),
                    "iNetAmount"  => round($catNet, 2),
                    "strIP"       => $request->ip(),
                ]);

                // (C) ORDER DETAILS (per subcategory/line in this category)
                foreach ($lines as $l) {
                    OrderDetail::create([
                        "iOrderId"        => $order->iOrderId,
                        "order_primary_id"        => $order->order_primary_id,
                        "Ratecard_id"     => $l['Ratecard_id'],
                        "iCustomerId"     => $l['iCustomerId'],
                        "category_id"     => $l['category_id'],
                        "subcategory_id"  => $l['subcategory_id'],
                        "amount"          => $l['amount'],
                        "rate"            => $l['rate'],
                        "qty"             => $l['qty'],
                        "discount_amount" => $l['discount_amount'],
                        "GSTAmount"       => $l['GSTAmount'],
                        "net_amount"      => $l['net_amount'],
                        "strIP"           => $request->ip(),
                        "created_at"      => now(),
                    ]);
                }

                $createdOrders[] = $order->iOrderId;
            }

            // (D) update customer profile on FIRST order only
            $existingOrderCount = Order::where('iCustomerId', $request->Customer_id)->count();
            if ($existingOrderCount === 1) { // first ever (we just inserted the first categories)
                Customer::where('Customer_id', $request->Customer_id)->update([
                    'Customer_name'    => $request->Customer_name,
                    'Customer_Address' => $request->Customer_Address,
                    'Pincode'          => $request->Pincode,
                ]);
            }

            // (E) Notify available technicians in those categories & pincode
            $technicians = DB::table('Technicial')
                ->join('Technicial_Pincode', 'Technicial.Technicial_id', '=', 'Technicial_Pincode.Technicial_id')
                ->join('Technicial_Service', 'Technicial.Technicial_id', '=', 'Technicial_Service.Technicial_id')
                ->where('Technicial_Pincode.Pincode_id', $Pincode->pin_id)
                ->whereIn('Technicial_Service.Category_id', $categoryIds) // FIX: whereIn
                ->select('Technicial.*')
                ->distinct()
                ->get();

            foreach ($technicians as $tech) {
                $this->notification([
                    "Technicial_id" => $tech->Technicial_id,
                    "order_id"      => '0',
                    "customer_id"   => $request->Customer_id,
                    'title'         => 'AuraClap',
                    'body'          => "New Lead:\nYou have a new lead for {$categoryList} Repair.\nClaim it quick and start working.",
                    'guid'          => '0',
                    'type'          => "New Lead",
                    'service'       => "Techincial"
                ]);
            }

            // (F) Notify customer (order confirm against Primary)
            $this->notification([
                "Technicial_id" => '0',
                "customer_id"   => $request->Customer_id,
                "title"         => "AuraClap",
                "body"          => "Dear user, Thank you for booking service from AURACLAP. We will assign you our technician soon!!",
                "order_id"      => $primary->primaryiOrderId,
                "type"          => "order_confirm",
                "service"       => "Customer",
                "flag"          => 1,
            ]);

            // (G) Razorpay order against PRIMARY total
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $orderData = [
                'receipt'  => $primary->primaryiOrderId . '-' . date('dmYHis'),
                'amount'   => (int) round($primary->iNetAmount * 100), // in paise
                'currency' => 'INR',
            ];

            $rzpOrder = $api->order->create($orderData);
            $razorpayResponse = [
                'order_id' => $rzpOrder['id'],
                'order_primary_id' => $primary->primaryiOrderId,
                'amount'  => $primary->iNetAmount,
                'currency' => 'INR',
                'receipt' => $rzpOrder['receipt'],
            ];

            // (H) clear cart
            Cart::where('Customer_id', $request->Customer_id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully!',
                'primary' => [
                    'iPrimaryId'   => $primary->primaryiOrderId,
                    'total_net'    => $primary->iNetAmount,
                    'orders'       => $createdOrders,
                ],
                'razorpayResponse' => $razorpayResponse
            ], 200);
        } catch (ValidationException $e) {
            $errorMessage = implode(', ', Arr::flatten($e->errors()));
            return response()->json(['success' => false, 'message' => $errorMessage], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $th->getMessage()], 500);
        }
    }

    public function remove_category_form_cart(Request $request)
    {
        try {
            if (Auth::guard('customerapi')->check()) {
                $request->validate([
                    "rate_id" => 'required',
                    "Customer_id" => 'required'
                ]);

                $delcart = Cart::where([
                    ['rate_id', '=', $request->rate_id],
                    ['Customer_id', '=', $request->Customer_id]
                ]);

                if (!$delcart->exists()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Item not found',
                    ], 404);
                }
                $delcart->delete();

                $totalamount = Cart::where('Customer_id', $request->Customer_id)->sum('amount');
                return response()->json([
                    'success' => true,
                    'total_amount' => $totalamount,
                    'message' => 'Item Deleted Successfully',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th->getMessage(),
            ], 500);
        }
    }


    public function offer_apply(Request $request)
    {
        try {
            if (!Auth::guard('customerapi')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }

            $request->validate([
                'coupon' => 'required',
                'totalAmount' => 'required',
                'Customer_id' => 'required',
            ]);

            $Offer = Offer::where([
                'iStatus' => 1,
                'isDelete' => 0,
                'text' => $request->coupon
            ])->first();

            if (!$Offer) {
                return response()->json([
                    'success' => false,
                    'message' => "Offer not found."
                ]);
            }

            $Today = date('Y-m-d');
            $Coupon = $request->coupon;
            $OfferCode = $Offer->text;
            $Total = $request->totalAmount;

            if ($Coupon !== $OfferCode) {
                return response()->json([
                    'success' => false,
                    'message' => "Coupon Code Not Match!"
                ], 200);
            }

            // Check date validity
            if (!($Today >= $Offer->startdate && $Today <= $Offer->enddate)) {
                return response()->json([
                    'success' => false,
                    'message' => "Coupon is expired!"
                ], 200);
            }

            // Allow usage
            $Percentage = $Offer->value;
            $discountAmount = round(($Total * $Percentage) / 100);
            $afterdiscounttotalamount = $Total - $discountAmount;
            $gstAmount = round($afterdiscounttotalamount * 0.18, 2); // 18% GST
            $grandamount = round($afterdiscounttotalamount + $gstAmount);

            // Log this coupon use
            $data = [
                'offerId' => $Offer->id,
                'customerId' => $request->Customer_id,
                'result' => $discountAmount,
                'created_at' => now(),
                'strIP' => $request->ip()
            ];

            CustomerCouponApplyed::create($data);

            return response()->json([
                'success' => true,
                'message' => "Coupon Code Apply Successfully!",
                'data' => $data,
                'AfterDiscountAmount' => $afterdiscounttotalamount,
                'gstAmount' => $gstAmount,
                'grandamount' => $grandamount,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    public function Timeslot(Request $request)
    {

        try {
            //if (Auth::guard('customerapi')->check()) {
            $timeslot = Timeslot::select(
                "Time_slot_id",
                "strtime",
                "fromtime",
                "totime"
            )->get();
            return response()->json([
                'message' => 'successfully Timeslot fetched...',
                'success' => true,
                'data' => $timeslot,
            ], 200);
            // } else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Customer is not authorized.',
            //     ], 401);
            // }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function faqlist(Request $request)
    {

        try {
            // if (Auth::guard('customerapi')->check()) {
            $timeslot = FaqMaster::select(
                "id",
                "question",
                "answer"
            )->get();
            return response()->json([
                'message' => 'successfully FaqMaster fetched...',
                'success' => true,
                'data' => $timeslot,
            ], 200);
            // } else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Customer is not authorized.',
            //     ], 401);
            // }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function notificationlist(Request $request)
    {

        try {
            $request->validate([

                "Customer_id" => 'required'
            ]);
            if (Auth::guard('customerapi')->check()) {
                $Notification = Notification::select(
                    "id",
                    "title",
                    "body",
                    "created_at"
                )->where('customer_id', $request->Customer_id)->get();
                return response()->json([
                    'message' => 'successfully notificationlist fetched...',
                    'success' => true,
                    'data' => $Notification,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    public function paymentstatus(Request $request)
    {

        try {
            if (Auth::guard('customerapi')->check()) {
                if ($request->status == "Success") {

                    $data = array(
                        'order_id' => $request->razorpay_payment_id,
                        'oid' => $request->order_id,
                        'Customer_id' => $request->Customer_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_signature' => $request->razorpay_signature,
                        'receipt' => $request->order_id . '-' . date('dmYHis'),
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                        'status' => $request->status,
                        'json' => $request->json,
                        'iPaymentType' => 1,
                        "Remarks" => "Online Payment"
                    );
                    DB::table('card_payment')->insert($data);
                    $updateProfileData = array(
                        'isPayment' => 1
                    );
                    PrimaryOrder::where("primaryiOrderId", $request->order_id)->update($updateProfileData);
                    
                } elseif ($request->status == "Fail") {

                    $data = array(
                        'order_id' => $request->razorpay_payment_id,
                        'oid' => $request->order_id,
                        'Customer_id' => $request->Customer_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_signature' => $request->razorpay_signature,
                        'receipt' => $request->order_id . '-' . date('dmYHis'),
                        'amount' => $request->amount,
                        'currency' => $request->currency,
                        'status' => $request->status,
                        'json' => $request->json,
                        'iPaymentType' => 1,
                        "Remarks" => "Online Payment"
                    );
                    DB::table('card_payment')->insert($data);

                    $updateProfileData = array(
                        'isPayment' => 2
                    );
                    PrimaryOrder::where("primaryiOrderId", $request->order_id)->update($updateProfileData);
                }
                return [
                    'success' => true,
                    'message' => "payment status updated successfully."
                ];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function Recruitment(Request $request)
    {
        try {
            if (Auth::guard('customerapi')->check()) {
                $Recruitment = Recruitment::all();
                $data = [];

                foreach ($Recruitment as $recru) {
                    $jobType = '';
                    switch ($recru->job_type) {
                        case 1:
                            $jobType = 'Full Time';
                            break;
                        case 2:
                            $jobType = 'Part Time';
                            break;
                        case 3:
                            $jobType = 'Contract';
                            break;
                        default:
                            $jobType = 'Unknown';
                    }
                    $data[] = [
                        'Recruitment_id' => $recru->Recruitment_id,
                        'job_title' => $recru->job_title,
                        'job_type' => $jobType,
                        'experience' => $recru->experience,
                        'qualification' => $recru->qualification,
                        'location' => $recru->location,
                        'timing' => $recru->timing,
                        'number_of_opening' => $recru->number_of_opening,
                        'salary' => $recru->salary

                    ];
                }

                if ($Recruitment->isEmpty()) {
                    return response()->json([
                        'message' => 'No recruitment data found',
                        'success' => false,
                    ], 200);
                }

                return response()->json([
                    'message' => 'Successfully fetched recruitment data',
                    'success' => true,
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer is not authorized.',
                ], 401);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $th->getMessage(),
            ], 500);
        }
    }
}
