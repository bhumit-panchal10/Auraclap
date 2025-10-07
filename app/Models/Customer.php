<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    protected $primaryKey = 'Customer_id';
    public $table = 'Customer';
    protected $fillable = [
        'Customer_id',
        'Customer_name',
        'Customer_Address',
        'Customerimg',
        'email',
        'city_id',
        'state_id',
        'area_id',
        'Customer_phone',
        'company_name',
        'hotelhostel_name',
        'role',
        'Gst_no',
        'Pincode',
        'Customer_GUID',
        'password',
        'confirm_password',
        'otp',
        'firebaseDeviceToken',
        'isOtpVerified',
        'expiry_time',
        'latitude',
        'longitude',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP',

    ];
    
    public function state()
    {
        return $this->belongsTo(StateMaster::class, 'state_id', 'stateId');
    }

    public function city()
    {
        return $this->belongsTo(CityMaster::class, 'city_id', 'cityId');
    }
    public function area()
    {
        return $this->belongsTo(AreaMaster::class, 'area_id', 'areaId');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $hidden = [
        'password'
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }

    // private function sendMessage($msgText)
    // {
    //     $client = new Client();


    //     try {
    //         $response = $client->request('GET', $msgText);
    //         $responseBody = $response->getBody()->getContents();

    //         return $responseBody;
    //     } catch (RequestException $e) {
    //         // Handle error
    //         // Log::error("Failed to send SMS to {$mobile}: " . $e->getMessage());
    //         return $e->getMessage();
    //     }
    // }
     public function WhatsappMessage($mobile, $msg)
    {
       
        $url = "https://newweb.technomantraa.com/api/send";

        $data = Setting::where(['iStatus' => 1, 'isDelete' => 0, 'id' => 1])->first();
        $instance_id = $data->instance_id;
       
      
        $data = [
            "number" => "91" . $mobile,
            "type" => "text",
            "message" => $msg,
            "instance_id" => $instance_id, // Use your actual instance_id
            "access_token" => "686ba3da18ed6" // Use your actual access_token
        ];
     
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        $response = curl_exec($ch);
     
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $error_msg);
        }

        curl_close($ch);
        $result = json_decode($response, true);

        return $result;
    }
}
