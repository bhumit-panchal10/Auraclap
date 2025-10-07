<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\PushNotificationController;
use GuzzleHttp\Client;
use App\Models\CMSMaster;
use App\Models\Recruitment;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Models\BaseURL;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;



class CmsApiController extends Controller

{
   
   public function cms(Request $request)
    {

        try {
             $request->validate([
                "id" => 'required'
            ]);
            $CMSMaster = CMSMaster::select(
                "id",
                "strTitle",
                "strDescription",
                "slugname"
            )->where('id',$request->id)->first();
            return response()->json([
                'message' => 'successfully CMS fetched...',
                'success' => true,
                'data' => $CMSMaster,
            ], 200);
        } catch (\Throwable $th) {
            // If there's an error, rollback any database transactions and return an error response.
            DB::rollBack();
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }
    

}
