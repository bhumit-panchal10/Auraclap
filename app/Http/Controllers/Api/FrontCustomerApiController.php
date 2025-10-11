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
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Managerate;
use App\Models\Offer;
use App\Models\Order;
use App\Models\PrimaryOrder;
use App\Models\OrderDetail;
use App\Models\Pincode;
use App\Models\JoinAsTechnicial;
use App\Models\CustomerCouponApplyed;
use GuzzleHttp\Client;
use App\Models\Timeslot;
use App\Models\CustomerReview;
use App\Models\Blog;
use App\Models\MetaData;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\BaseURL;
use Razorpay\Api\Api;
use Brian2694\Toastr\Facades\Toastr;



class FrontCustomerApiController extends Controller

{
    public function sendcontact_mail(Request $request)
    {
        try {
            //dd($request);
            $firstname = $request->firstname;
            $lastname = $request->lastname;
            $email = $request->email;
            $service = $request->service;
            $mobile = $request->mobile;
            $messageContent = $request->message;
            //$subject = $request->subject;
            $sendEmailDetails = DB::table('sendemaildetails')->where(['id' => 9])->first();

            $msg = [
                'FromMail' => $sendEmailDetails->strFromMail,
                'Title' => $sendEmailDetails->strTitle,
                'ToEmail' => $email,
                'Subject' => $sendEmailDetails->strSubject ?? '',
            ];
            

            $data = [
                'FirstName' => $firstname,
                'LastName' => $lastname,
                'Email' => $email,
                'Service' => $service,
                'Mobile' => $mobile,
                'Message' => $messageContent,
                //'Subject' => $subject
            ];
           
            Mail::send('emails.contactusmail', ['data' => $data], function ($message) use ($msg) {
                $message->from($msg['FromMail'], $msg['Title']);
                $message->to($msg['ToEmail'])->subject($msg['Subject']);
            });
            
             return response()->json([
                'message' => 'Mail send Successfully',
                'success' => true
               
            ], 200);
           
        } catch (\Throwable $th) {
            Toastr::error('Error: ' . $th->getMessage());
            return redirect()->back()->withInput();
        }
    }
    
   public function front_category_subcategory(Request $request)
   {
    $request->validate([
        'slug'    => 'required|string', // e.g. microwave-service
        'city_id' => 'required|integer',
    ]);

    try {
        $baseCategoryImgUrl   = asset('/upload/category-image/');
        
        $baseSubCategoryImgUrl = asset('/upload/subcategory-images/');
        $baseratcardImgUrl     = asset('/upload/RateCardPdf/');
        $basecarouseliconUrl     = asset('/upload/carousel-icon/');
        $baseCategoryiconUrl     = asset('/upload/category-icon/');

        // $category = Categories::with([
        //     'subcategories' => function ($q) {
        //         $q->where('isDelete', 0)
        //             ->where('iStatus', 1)
        //             ->where('sub_rat_flag', 0)
        //             ->select('iSubCategoryId','iCategoryId','strSubCategoryName','strSlugName','SubCategories_img');
        //     },
        //     'subcategories.rates' => function ($q) use ($request) {
        //         $q->where('isDelete', 0)
        //             ->where('iStatus', 1)
        //             ->where('city_id', $request->city_id);
        //     },
        // ])
        // ->where('isDelete', 0)
        // ->where('iStatus', 1)
        // ->where('Categories_slug', $request->slug)
        // ->first();
        
        $category = Categories::with([
            'subcategories' => function ($q) use ($request) {
                  $q->where('sub_rat_flag', 0)
                  ->select('iSubCategoryId','iCategoryId','strSubCategoryName','strSlugName','SubCategories_img')
                  ->whereHas('rates', function ($r) use ($request) {
                      $r->where('isDelete', 0)
                        ->where('iStatus', 1)
                        ->where('city_id', $request->city_id);
                  })
                  ->with(['rates' => function ($r) use ($request) {
                      $r->where('isDelete', 0)
                        ->where('iStatus', 1)
                        ->where('city_id', $request->city_id)
                        ->select('rate_id','subcate_id','title','description','amount','time','city_id');
                  }]);
            },
        ])
        ->where('isDelete', 0)
        ->where('iStatus', 1)
        ->where('Categories_slug', $request->slug)
        ->first();


        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found for given slug.',
                'data'    => null
            ], 404);
        }

        // 2) Transform the single category node
        $category->Categories_img = $category->Categories_img
            ?  $baseCategoryImgUrl.'/'  . $category->Categories_img
            : null;

        $category->ratecard_pdf = $category->ratecard_pdf
            ? $baseratcardImgUrl.'/' . $category->ratecard_pdf
            : null;
            
        $category->carousel_image = $category->carousel_image
            ? $basecarouseliconUrl.'/' . $category->carousel_image
            : null;
            
        $category->Categories_icon = $category->Categories_icon
            ? $baseCategoryiconUrl.'/' . $category->Categories_icon
            : null;

        $hasRates = false; // flag to check if any city-wise rate exists

        if ($category->subcategories) {
            $category->subcategories->transform(function ($sub) use ($baseSubCategoryImgUrl, &$hasRates) {
                $sub->SubCategories_img = $sub->SubCategories_img
                    ? $baseSubCategoryImgUrl.'/' . $sub->SubCategories_img
                    : null;

                if ($sub->rates && $sub->rates->count() > 0) {
                    $hasRates = true; // at least one rate exists
                    $sub->rates->transform(function ($rate) {
                        return [
                            'rate_id'    => $rate->rate_id,
                            'title'      => $rate->title,
                            'description'=> $rate->description,
                            'amount'     => $rate->amount,
                            'time'       => $rate->time,
                            'city_id'    => $rate->city_id,
                        ];
                    });
                }
                return $sub;
            });
        }

        if (!$hasRates) {
            return response()->json([
                'status'  => false,
                'message' => 'No rates available for this city.',
                'data'    => null
            ], 200);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Category (by slug) with subcategories and rates fetched successfully',
            'data'    => $category,
        ], 200);

    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    } catch (\Throwable $th) {
        return response()->json(['status' => false, 'message' => $th->getMessage()], 500);
    }
}

    
    public function subcat(Request $request)
    {
        $request->validate([
            'Categories_id' => 'required',
        ]);

        try {
            if (Auth::guard('customerapi')->check()) {
            $subcategorydata = Managerate::with('category', 'subcategory')->where('cate_id', $request->Categories_id)->get();

            if (!$subcategorydata) {
                return response()->json([
                    'message' => 'No category found.',
                    'success' => false,
                ], 404);
            }
            $data = [];
            foreach ($subcategorydata as $subcat) {
                $data[] = [
                    'cate_id'  => $subcat->cate_id,
                    'subcate_id'  => $subcat->subcate_id,
                    'title'  => $subcat->title,
                    'description'  => $subcat->description,
                    'amount'  => $subcat->amount,
                    'strSubCategoryName'  => $subcat->subcategory->strSubCategoryName,
                    'Category_name'  => $subcat->category->Category_name,
                    'SubCategories_img'  => "https://getdemo.in/Mkservice/upload/subcategory-images/{$subcat->subcategory->SubCategories_img}",

                ];
            }

            return response()->json([
                'message' => 'Successfully fetched category with subcategories and rates.',
                'success' => true,
                'data'    => $data
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
    
    
    public function JoinAsTechnicial(Request $request)
    {
        // dd($request);
        try {

            $request->validate([
                "name" => 'required',
                "email" => 'required',
                "phone" => 'required|digits:10',
                "state_id" => 'required',
                "city" => 'required'

            ]);
            $existingTechnicial = JoinAsTechnicial::where('mobile_no', $request->phone)->first();
            if ($existingTechnicial) {
                return response()->json([
                    'success' => false,
                    'message' => 'A Technicial with this mobile number already exists.',
                ], 409); // 409 Conflict HTTP status code
            }
            $Technicialdata = array(

                "name" => $request->name,
                "email" => $request->email,
                "mobile_no" => $request->phone,
                "stateid" => $request->state_id,
                "city" => $request->city,
                'strIP' => $request->ip(),
            );

            $Technicial = JoinAsTechnicial::create($Technicialdata);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Join As Technicial Registration Successfully.',
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
    
    public function existinguser(Request $request)
    {
       
        try {
            $request->validate([
               
                "mobile" => 'required|digits:10',
               
            ]);
          
            $existingCustomer = Customer::where('Customer_phone', $request->mobile)->first();
            if (!$existingCustomer) {
            return response()->json(['success' => false, 'message' => 'Customer not found'], 404);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'data' => $existingCustomer,
                'message' => 'Customer fetch Successfully.',
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
    
    // public function order_checkout(Request $request)
    // {
    //     if (Auth::guard('customerapi')->check()) {
    //     // 2. Create Order
    //     $order = Order::create([
    //         'iCustomerId' => $request->Customer_id,
    //         'Customer_name' => $request->Customer_name,
    //         'Customer_Address' => $request->Customer_Address,
    //         'Customer_phone' => $request->Customer_phone,
    //         'Pincode' => $request->Pincode,
    //         'order_date' => $request->order_date,
    //         'slot_id' => $request->slot_id,
    //         'city_id' => $request->city_id,
    //         'payment_mode' => $request->payment_mode,
    //         'iAmount' => $request->iAmount ?? 0,
    //         'iDiscount' => $request->iDiscount ?? 0,
    //         'gst_amount' => $request->gst_amount ?? 0,
    //         'iNetAmount' => $request->total_amount ?? 0
    //         //'order_status' => 1
    //     ]);

    //     // 3. Save Order Details
    //     foreach ($request->ProductList as $product) {
    //         OrderDetail::create([
    //             'iOrderId' => $order->iOrderId,
    //             'iCustomerId' => $request->Customer_id,
    //             'qty' => $product['qty'] ?? 1,
    //             'rate' => $product['rate'] ?? 0,
    //             'Ratecard_id' => $product['Ratecard_id'] ?? 1,
    //             'amount' => ($product['qty'] ?? 1) * ($product['rate'] ?? 0),
    //             'category_id' => $product['category_id'] ?? null,
    //             'subcategory_id' => $product['subcategory_id'] ?? null
    //         ]);
    //     }

    //     // 4. Razorpay Payment Integration
    //     if ($request->payment_mode == 1) {
    //         $api = new Api(config('app.razorpay_key'), config('app.razorpay_secret'));
    //         $razorpayOrder = $api->order->create([
    //             'receipt' => $order->iOrderId . '-' . now()->format('dmYHis'),
    //             'amount' => (int) round($request->total_amount * 100), // in paise
    //             'currency' => 'INR'
    //         ]);

    //         return response()->json([
    //             'status' => 1,
    //             'message' => 'Order placed successfully. Proceed to online payment.',
    //             'OrderId' => $order->iOrderId,
    //             'razorpay_order_id' => $razorpayOrder['id'],
    //             'amount' => $razorpayOrder['amount'],
    //             'currency' => 'INR',
    //             'payment_mode' => 'online'
    //             //'redirectURL' => route('authorize.payment', [$order->iOrderId])
    //         ]);
    //     }

    //     // 5. Return Response
    //     return response()->json([
    //         'status' => 1,
    //         'message' => 'Order placed successfully. Pay on delivery.',
    //         'OrderId' => $order->iOrderId,
    //         'payment_mode' => 'cash'
    //     ]);
    //     } else {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Customer is not authorized.',
    //         ], 401);
    //     }
    // }
    
    
    public function order_checkout(Request $request)
    {
    try {
        // if (!Auth::guard('customerapi')->check()) {
        //     return response()->json(['success' => false, 'message' => 'Customer is not authorized.'], 401);
        // }
       
        // 0) Validate input
        $request->validate([
            'Customer_id'      => 'nullable|integer',
            'Customer_name'    => 'nullable|string',
            'Customer_Address' => 'nullable|string',
            //'Customer_phone' => 'required|integer|unique:Customer,Customer_phone',
            'Customer_phone' => 'required|integer',

            'Pincode'          => 'required|integer',
            'order_date'       => 'required|date',
            'slot_id'          => 'required|integer',
            'city_id'          => 'required|integer',
            'payment_mode'     => 'required|integer', // 1=online, 2=COD (adjust if needed)
            'dis_per'          => 'nullable|numeric',        // optional % discount on each line

            'ProductList'      => 'required|array|min:1',
            'ProductList.*.category_id'    => 'required|integer',
            'ProductList.*.subcategory_id' => 'required|integer',
            'ProductList.*.qty'            => 'required|numeric|min:1',
            'ProductList.*.rate'           => 'required|numeric|min:0',
            'ProductList.*.Ratecard_id'    => 'nullable|integer',
        ]);
        $customer_rec = Customer::where('Customer_id', $request->Customer_id)->first();
        if ($customer_rec) {
            $Customerid = $customer_rec->Customer_id;
            $Customer = $customer_rec; // ✅ Assign the existing record
        } else {
            $Customer = Customer::create([
                "email"            => $request->email,
                "Customer_name"    => $request->Customer_name,
                "Customer_Address" => $request->Customer_Address,
                "Customer_phone"   => $request->Customer_phone,
            ]);
            $Customerid = $Customer->Customer_id; // Also ensure this is set
        }


        $disPer = (float) ($request->dis_per ?? 0);
        $lines  = collect($request->ProductList)->map(function ($p) use ($request, $disPer) {
            $qty    = (float) ($p['qty']  ?? 1);
            $rate   = (float) ($p['rate'] ?? 0);
            $gross  = round($qty * $rate, 2);
            $disc   = round(($gross * $disPer) / 100);
            $after  = max(0, $gross - $disc);
            $gst    = round($after * 0.18, 2);         // 18% GST
            $net    = round($after + $gst, 2);

            return [
                'Ratecard_id'     => $p['Ratecard_id'] ?? null,
                'iCustomerId'     => $request->Customer_id,
                'category_id'     => (int) $p['category_id'],
                'subcategory_id'  => (int) $p['subcategory_id'],
                'qty'             => $qty,
                'rate'            => $rate,
                'amount'          => $gross,           // before discount
                'discount_amount' => $disc,
                'GSTAmount'       => $gst,
                'net_amount'      => $net,
            ];
        });

        if ($lines->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No products to place order.'], 422);
        }

        $byCategory = $lines->groupBy('category_id');

        // PRIMARY totals
        $primaryGross    = $lines->sum('amount');
        $primaryDiscount = $lines->sum('discount_amount');
        $primaryGst      = $lines->sum('GSTAmount');
        $primaryNet      = $lines->sum('net_amount');

        DB::beginTransaction();

        // 3) Create PRIMARY ORDER
        $primary = PrimaryOrder::create([
            "iCustomerId"      => $request->Customer_id ?? $Customerid,
            "iAmount"          => round($primaryGross, 2),
            "gst_amount"       => round($primaryGst, 2),
            "iDiscount"        => round($primaryDiscount, 2),
            "iNetAmount"       => round($primaryNet, 2),

            "order_date"       => $request->order_date,
            "slot_id"          => $request->slot_id,
            "payment_mode"     => $request->payment_mode,
            "dis_per"          => $disPer,
            "city_id"          => $request->city_id,

            "Customer_name"    => $request->Customer_name,
            "Customer_Address" => $request->Customer_Address,
            "Customer_phone"   => $request->Customer_phone,
            "Pincode"          => $request->Pincode,
            "strIP"            => $request->ip(),
        ]);
        // 4) Create ORDER (per category) + ORDER_DETAIL (per line)
        $createdOrderIds = [];
        foreach ($byCategory as $catId => $catLines) {
            $catGross    = $catLines->sum('amount');
            $catDiscount = $catLines->sum('discount_amount');
            $catGst      = $catLines->sum('GSTAmount');
            $catNet      = $catLines->sum('net_amount');

            $order = Order::create([
                "order_primary_id" => $primary->primaryiOrderId,
                "iCustomerId"      => $request->Customer_id ?? $Customerid,
                "payment_mode"     => $request->payment_mode,
                "category_id"      => (int) $catId,

                "iAmount"          => round($catGross, 2),
                "gst_amount"       => round($catGst, 2),
                "iDiscount"        => round($catDiscount, 2),
                "iNetAmount"       => round($catNet, 2),

                "strIP"            => $request->ip(),
            ]);

            foreach ($catLines as $l) {
                OrderDetail::create([
                    "iOrderId"         => $order->iOrderId,
                    "order_primary_id" => $primary->primaryiOrderId,
                    "Ratecard_id"      => $l['Ratecard_id'],
                    "iCustomerId"      => $l['iCustomerId'],
                    "category_id"      => $l['category_id'],
                    "subcategory_id"   => $l['subcategory_id'],
                    "qty"              => $l['qty'],
                    "rate"             => $l['rate'],

                    "amount"           => $l['amount'],
                    "discount_amount"  => $l['discount_amount'],
                    "GSTAmount"        => $l['GSTAmount'],
                    "net_amount"       => $l['net_amount'],

                    "strIP"            => $request->ip(),
                    "created_at"       => now(),
                ]);
            }

            $createdOrderIds[] = $order->iOrderId;
        }
        
        $otpResponse = null;
        if ((int)$request->payment_mode === 2) {
        
            // Generate a random 6-digit OTP
            $otp = rand(1000, 9999);
            
            // Optionally, you can send this OTP to the customer's phone or email here.

            $otpResponse = [
                'otp' => $otp,
                'message' => 'OTP for Cash on Delivery (COD) payment.',
                'order_primary_id' => $primary->primaryiOrderId,
                'amount'           => round($primary->iNetAmount),
            ];
             PrimaryOrder::where('primaryiOrderId', $primary->primaryiOrderId)
           ->update(['payment_mode' => 4,'before_cash_payment_otp'=>$otp]);
           
            $message = "Dear Customer, your OTP is $otp. Please use this to verify your action. Do not share it with anyone. Regards, Team The Auraclap";
            $Customer->WhatsappMessage($request->Customer_phone, $message);

        }
       
      
        // 5) Create Razorpay order for PRIMARY total (if online)
        $razorpayResponse = null;
        if ((int)$request->payment_mode === 1) {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );
            

            $rzp = $api->order->create([
                'receipt'  => $primary->primaryiOrderId . '-' . now()->format('dmYHis'),
                'amount'   => (int) round($primary->iNetAmount * 100), // paise
                'currency' => 'INR',
            ]);

            $razorpayResponse = [
                'order_id'         => $rzp['id'],
                'order_primary_id' => $primary->primaryiOrderId,
                'amount'           => round($primary->iNetAmount),
                'currency'         => 'INR',
                'receipt'          => $rzp['receipt'],
            ];
        }

        DB::commit();
        

        return response()->json([
            'success'  => true,
            'message'  => 'Order created successfully!',
            'primary'  => [
                'primaryiOrderId' => $primary->primaryiOrderId,
                'total_net'       => round($primary->iNetAmount),
                'orders'          => $createdOrderIds,
            ],
           'payment'  => [
                'mode'             => match ((int)$request->payment_mode) {
                    1 => 'online',
                    2 => 'cash',
                    3 => 'hotel_hostel',
                    default => 'unknown',
                },
                'razorpayResponse' => $razorpayResponse,
                'otpResponse' => $otpResponse,
            ],
        ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = implode(', ', Arr::flatten($e->errors()));
            return response()->json(['success' => false, 'message' => $errorMessage], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['success' => false, 'error' => $th->getMessage()], 500);
        }
    }
    
    public function payment_status(Request $request)
    {
     try{
       // if (Auth::guard('customerapi')->check()) {
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
                'json' => json_encode($request->json),
                'iPaymentType' => 1,
                "Remarks" => "Online Payment"
            );
            //dd($request);
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
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Customer is not authorized.',
        //     ], 401);
        // }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function regenerate_cod_otp(Request $request)
    {
        $request->validate([
            'order_primary_id' => 'required|integer|exists:primary_orders,primaryiOrderId',
        ]);

        try {
            // Fetch the order
            $order = PrimaryOrder::where('primaryiOrderId', $request->order_primary_id)->first();
           
            $customer = Customer::where('Customer_id',$order->iCustomerId)->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
    
            // Generate new 6-digit OTP
            $otp = rand(1000, 9999);
    
            // Update the OTP in DB
            $order->before_cash_payment_otp = $otp;
            $order->payment_mode = 4; // Assuming 4 is temporary COD state requiring OTP
            $order->save();
    
            // Send OTP via WhatsApp
            $message = "Dear Customer, your regenerated OTP is $otp. Use this to verify your COD order. Do not share it. - Team The Auraclap";
    
            // Call WhatsappMessage (assumed to be defined)
            $customer->WhatsappMessage($customer->Customer_phone, $message); // replace with actual function
    
            return response()->json([
                'success' => true,
                'message' => 'OTP regenerated and sent successfully.',
                'data' => [
                    'otp' => $otp,
                    'order_primary_id' => $order->primaryiOrderId
                ]
            ], 200);
    
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getMessage()], 500);
        }
    }
    
    public function verifyotp(Request $request)
    {
        try {
            // Validate the request input
            $request->validate([
                'order_primary_id' => 'required|integer',
                'otp'      => 'required|integer',
            ]);
    
            // Retrieve the order
            $order = PrimaryOrder::where('primaryiOrderId', $request->order_primary_id)->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
            }
    
            // Check if the OTP matches
            if ($order->before_cash_payment_otp == $request->otp) {
              
                $order->update(['payment_mode' => 2]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully. Payment confirmed.',
                ], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid OTP.'], 400);
            }
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = implode(', ', Arr::flatten($e->errors()));
            return response()->json(['success' => false, 'message' => $errorMessage], 422);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th->getMessage()], 500);
        }
    }
    
    
     public function orderdetail(Request $request)
    {
        $request->validate(['order_id' => 'required|integer']);

        try {
            
            $primaryOrders = PrimaryOrder::query()
                ->where('iCustomerId', $request->Customer_id)
                ->where('primaryiOrderId', $request->order_id)

                ->whereIn('isPayment', [0, 1])
                ->whereIn('payment_mode', [0, 1, 2, 3])
                ->with([
                    'slot',
                    'orders' => function ($q) use ($request) {
                        $q->where('iCustomerId', $request->Customer_id)
                            ->whereIn('order_status', [0, 1,2,3])
                            ->orderBy('iOrderId', 'desc');
                    },
                    'orders.orderdetail' => function ($q) {
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
                'message' => 'Orders detail Fetched Successfully',
                'success' => true,
                'data'    => $payload,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }


    
    
    public function blogs(Request $request)
    {
        try {
            $blogs = Blog::get();
           
            $data = [];
            foreach ($blogs as $ourTeam) {
                $data[]  = array(
                    "blogId" => $ourTeam->blogId,
                    "blogTitle" => $ourTeam->blogTitle,
                    "slugname" => $ourTeam->slugname,
                    "blogDescription" => $ourTeam->blogDescription,
                    "blogDate" => $ourTeam->blogDate,
                    "blogImage" => asset('upload/Blog/'. $ourTeam->blogImage)
                );
            }
           
 
            return response()->json([
                'message' => 'successfully blogs fetched...',
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
   
    public function blog_details(Request $request)
    {
        try {
           
            $request->validate(
                [
                    'slugname' => 'required'
                ]
            );
          
            $blog = Blog::where(['isDelete' => 0, 'iStatus' => 1,'slugname'=> $request->slugname])->first();
            
            $data = array(
                "blogId" => $blog->blogId,
                "blogTitle" => $blog->blogTitle,
                "slugname" => $blog->slugname,
                "blogDescription" => $blog->blogDescription,
                "blogDate" => $blog->blogDate,
                "metaTitle" => $blog->metaTitle,
                "metaKeyword" => $blog->metaKeyword,
                "metaDescription" => $blog->metaDescription,
                "head" => $blog->head,
                "body" => $blog->body,
                "blogImage" => asset('upload/Blog/'. $blog->blogImage)
            );
           
            return response()->json([
                'message' => 'successfully blog detail fetched...',
                'success' => true,
                'data' => $data,
            ], 200);
           
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function homelist(Request $request)
    {
        try {
            $homedata = MetaData::where('pagename', 'home')->first();
            
            return response()->json([
                'message' => 'Successfully homelist fetched...',
                'success' => true,
                'data' => $homedata,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function aboutlist(Request $request)
    {
        try {
            $homedata = MetaData::where('pagename', 'about')->first();
            
            return response()->json([
                'message' => 'Successfully aboutlist fetched...',
                'success' => true,
                'data' => $homedata,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function contactlist(Request $request)
    {
        try {
            $homedata = MetaData::where('pagename', 'contact')->first();
            
            return response()->json([
                'message' => 'Successfully contactlist fetched...',
                'success' => true,
                'data' => $homedata,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    
    public function serviceslist(Request $request)
    {
        try {
            $homedata = MetaData::where('pagename', 'services')->first();
            
            return response()->json([
                'message' => 'Successfully serviceslist fetched...',
                'success' => true,
                'data' => $homedata,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

   
}
