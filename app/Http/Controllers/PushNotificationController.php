<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Auth;
use App\Models\Technicial;
use App\Models\Customer;
use App\Models\Notification;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

class PushNotificationController extends Controller
{
    public function notification($request)
    {

        $title = $request['title'];
        $Technicial_id = $request['Technicial_id'];
        $body = $request['body'];
        $guid = $request['guid'] ?? '';
        $customer_id = $request['customer_id'];
        $order_id = $request['order_id'];
        $type = $request['type'];
        $service = $request['service'];

        if ($service == "Techincial") {
            // $FcmToken = Driver::where("id", $request['id'])->pluck('firebaseDeviceToken')->all();
            $FcmToken = Technicial::select('firebaseDeviceToken', 'name as name')->where("Technicial_id", $request['Technicial_id'])->first();
        }

        if ($service == "Customer") {
            // $FcmToken = Driver::where("id", $request['id'])->pluck('firebaseDeviceToken')->all();
            $FcmToken = Customer::select('firebaseDeviceToken', 'Customer_name as name')->where("Customer_id", $request['customer_id'])->first();
        }
         

        // foreach ($FcmTokens as $FcmToken) {
        $data = [
            'message' => [
                'token' => $FcmToken->firebaseDeviceToken, // single token
                'notification' => [
                    'title' => $title,
                    'body' => $body
                ],

                'data' => [

                    'guid' => (string)$guid,
                    'type' => (string)$type,
                    'service' => (string)$service,
                ]
            ],
        ];
      


        $json_data = json_encode($data);


        $serviceAccountPath = __DIR__ . '/../../../aura-clap-eb40fb815b6b.json';

        $client = new Client();
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

        // Save the notification to the database before sending
        $notification = Notification::create([
            'Technicial_id' => $Technicial_id,
            'title' => $title,
            'customer_id' => $customer_id,
            'order_id' => $order_id,
            'name' => $FcmToken->name,
            'body' => $body,
            'guid' => $guid,
            'type' => $type,
            'service' => $service,
            'fcm_token' => $FcmToken->firebaseDeviceToken,
            'status' => 'pending',
            'created_at' => now()
        ]);

        try {

            $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);

            $accessToken = $credentials->fetchAuthToken()['access_token'];
            $url = 'https://fcm.googleapis.com/v1/projects/aura-clap/messages:send';
            $client = new Client();

            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'body' => $json_data,
            ]);

            $result = $response->getBody()->getContents();
            // Update Notification Status and Response
            $notification->update([
                'status' => 'sent',
                'response' => $result,
                'updated_at' => now()
            ]);

            return response()->json(['success' => 'Notification sent successfully', 'response' => $result]);
            // Log successful response or handle as needed
        } catch (\Exception $e) {

            // Update Notification Status on Failure
            $notification->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
                'updated_at' => now()
            ]);

            // Log or handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
        // }
    }
}
