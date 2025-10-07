<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Technicial extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'Technicial';
    protected $primaryKey = 'Technicial_id';

    protected $fillable = [
        'Technicial_id',
        'name',
        'email',
        'Technicial_image',
        'password',
        'mobile_no',
        'is_changepasswordfirsttime',
        'stateid',
        'city',
        'otp',
        'expiry_time',
        'last_login',
        'firebaseDeviceToken',
        'iStatus',
        'isDelete',
        'created_at',
        'updated_at',
        'strIP'
    ];



    protected $hidden = ['password'];

    /**
     * Get the identifier that will be stored in the JWT token.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key-value array containing any custom JWT claims.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Relationship with StateMaster.
     */
    public function state()
    {
        return $this->belongsTo(StateMaster::class, 'stateid', 'stateId');
    }

    public function pincodes()
    {
        return $this->hasMany(TechnicialPincode::class, 'Technicial_id', 'Technicial_id');
    }

    public function services()
    {
        return $this->hasMany(TechnicialService::class, 'Technicial_id', 'Technicial_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'Technicial_id', 'Technicial_id');
    }
    public function customerReviews()
    {
        return $this->hasManyThrough(
            CustomerReview::class,
            Order::class,
            'Technicial_id',    // Foreign key on Order table
            'order_id',         // Foreign key on CustomerReview table
            'Technicial_id',    // Local key on Technicial
            'iOrderId'          // Local key on Order
        );
    }
    
    public function WhatsappMessage($mobile, $msg)
    {
        // API endpoint
        $url = "https://newweb.technomantraa.com/api/send";

        $data = Setting::where(['iStatus' => 1, 'isDelete' => 0, 'id' => 1])->first();

        $instance_id = $data->instance_id;

        // Data to be sent in JSON format
        $data = [
            "number" => "91" . $mobile,
            "type" => "text",
            "message" => $msg,
            "instance_id" => $instance_id, // Use your actual instance_id
            "access_token" => "686ba3da18ed6" // Use your actual access_token
        ];

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        // Execute cURL request
        $response = curl_exec($ch);
        //dd($response);
        // Check for cURL errors
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception('Curl error: ' . $error_msg);
        }

        // Close cURL session
        curl_close($ch);

        // Decode the JSON response
        $result = json_decode($response, true);

        // Return the result
        return $result;
    }
}
