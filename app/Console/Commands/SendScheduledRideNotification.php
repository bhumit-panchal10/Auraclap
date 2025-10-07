<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Http\Controllers\RideController; // Import your controller
use Illuminate\Http\Request;
use App\Models\SMSTemplate;
use App\Models\ActiveTrip;
use App\Models\AreaMaster;
use App\Models\Driver;
use App\Models\Customer;
use Carbon\Carbon;
use App\Http\Controllers\PushNotificationController; // Import PushNotificationController

class SendScheduledRideNotification extends Command

{
    protected $signature = 'send:scheduled-notifications';

    //protected $description = 'Send notifications for scheduled rides that are 30 or 15 minutes away';
    protected $description = 'Send notifications for scheduled rides and pass renewals';

    protected $notificationController;

    public function __construct()
    {
        parent::__construct();
        $this->notificationController = new PushNotificationController();
    }

    public function handle()
    {
        // Fetch all active trips that are scheduled and not deleted
        $ActiveTrip = ActiveTrip::select('active-trip.*', 'driver-masters.name')->where(['active-trip.iStatus' => 1, 'active-trip.isDelete' => 0, 'active-trip.iTripStatus' => 1, 'active-trip.isScheduledRide' => 1])

            ->leftjoin('driver-masters', 'active-trip.iDriverId', '=', 'driver-masters.id')
            ->get();

         $this->info('Active trips count: ' . $ActiveTrip->count());

        // Loop through each active trip and trigger the notification logic
        foreach ($ActiveTrip as $trip) {
            $this->sendNotification($trip);
        }
        
        // New logic for sending pass expiration notifications
        // $this->sendPassExpiryNotifications();

        // $currentTime = Carbon::now();
        // $this->info('Scheduled ride notifications sent at . $currentTime');

        $currentTime = Carbon::now()->format('Y-m-d H:i:s'); // Optional: Change format as needed
        $this->info('Scheduled ride notifications sent at ' . $currentTime);

    }

    private function sendNotification($trip)
    {
        $this->info('Sending notification for trip ID: ' . $trip->iTripId);

        $SMSTemplate = SMSTemplate::where('id', 19)->first();

        if (!$SMSTemplate) {
            $this->error('SMS template not found.');
            return;
        }

        $scheduledTime = Carbon::parse($trip->strScheduledTime);
        $currentTime = Carbon::now();
        $minutesDifference = $currentTime->diffInMinutes($scheduledTime, false);
        
        $this->info('Minutes difference for trip ID ' . $trip->iTripId . ': ' . $minutesDifference);

        if ($minutesDifference == 30 || $minutesDifference == 15 || $minutesDifference == 5) {  // Check for 30 minutes or 15 minutes difference    

            $Date = $scheduledTime->format('d-m-Y');
            $Time = $scheduledTime->format('h:i A');
            $appName = env('APP_NAME');
            $pincode = null;

            if (preg_match("/\b\d{6}\b/", $trip->strSource, $matches)) {
                $pincode = $matches[0];
            }

            $area =  AreaMaster::where(['iStatus' => 1, 'isDelete' => 0, 'areaPincode' => $pincode])->first();
            $areaName = $area ? $area->areaName : $trip->strSource;
            $TrackingLink = 'http://vybecabs.com/';

            // Replace placeholders in the SMS template description

            $RiderTextMessage = str_replace(
                ['[Driver Name]', '[Time]', '[Date]', '[Pickup Location]', '[Tracking Link]'],  // Placeholders in the template
                [$trip->name, $Time, $Date, $areaName, $TrackingLink],          // Actual values to replace with
                $SMSTemplate->strDescription       // The template content
            );
            
            $driver = Driver::where(['isDelete' => 0, 'id' => $trip->iDriverId])->first();
            $customer = Customer::where(['iStatus' => 1, 'isDelete' => 0, 'customerid' => $trip->iCustomerId])->first();
            
            if ($driver) {
                $DriverrTextMessage = str_replace(
                    ['[Driver Name]', '[Time]', '[Date]', '[Pickup Location]', '[Tracking Link]'],  // Placeholders in the template
                    [$customer->customername, $Time, $Date, $areaName, $TrackingLink],          // Actual values to replace with
                    $SMSTemplate->strDescription       // The template content
                );
            } else {
                $this->error('Driver not found for trip ID: ' . $trip->iTripId);
                return;
            }    

            $riderarray = array(
                "id" => $trip->iCustomerId,
                'title' => $appName,
                'body' => $RiderTextMessage,
                'guid' => $trip->strGuid,
                'type' => "scheduled-ride",
                'service' => "rider"
            );
            
            $driverarray = array(
                "id" => $trip->iDriverId,
                'title' => $appName,
                'body' => $DriverrTextMessage,
                'guid' => $trip->strGuid,
                'type' => "scheduled-ride",
                'service' => "driver"
            );
            
            $this->notificationController->notification($riderarray);
            $this->notificationController->notification($driverarray);
            
            $Drivername = $customer->customername ;
            $MobileNo = $driver->contactNumber ;
            $driver = new Driver();
            $driver->ScheduledRide( $MobileNo , $Drivername , $Time , $Date , $areaName );
            
            $Ridername = $trip->name ;
            $MobileNo = $customer->customermobile ;
            $customer = new Customer();
            $customer->ScheduledRide( $MobileNo , $Ridername , $Time , $Date , $areaName );

        } else if ($minutesDifference < 0) {
            
            // Send sorry message if scheduled time has passed
            $appName = env('APP_NAME');
            $Date = $scheduledTime->format('d-m-Y');
            $Time = $scheduledTime->format('h:i A');
    
            // Construct the sorry message
            $sorryMessage = "The current driver is unavailable! Would you like to switch to another driver?";
            // this driver is not available now do you want to continue with other driver 
            
            $riderarray = array(
                "id" => $trip->iCustomerId,
                'title' => $appName,
                'body' => $sorryMessage,
                'guid' => $trip->strGuid,
                'type' => "scheduled_ride_passed",
                'service' => "rider"
            );
    
            $this->notificationController->notification($riderarray);
        } else {

            // return response()->json(['error' => 'No scheduled ride found or trip is not active.'], 404);

            $this->info('No notification sent for trip ID: ' . $trip->iTripId . ' (not within 30 or 15 minutes).');
        }
    }
    
    // private function sendPassExpiryNotifications()
    // {
    //     $sevenDaysFromNow = Carbon::now()->addDays(7)->format('Y-m-d');
    //     $expiringPasses = PassPurchase::where('iStatus', 1)
    //         ->where('isDelete', 0)
    //         ->where('strEndDate', $sevenDaysFromNow)
    //         ->get();

    //     foreach ($expiringPasses as $pass) {
    //         $this->sendPassExpiryNotification($pass);
    //     }
    // }
    
    // private function sendPassExpiryNotification($pass)
    // {
    //     $this->info('Sending pass expiry notification for pass ID: ' . $pass->id);

    //     $customer = Customer::where(['iStatus' => 1, 'isDelete' => 0, 'customerid' => $pass->iCustomerId])->first();
    //     if (!$customer) {
    //         $this->error('Customer not found for pass ID: ' . $pass->id);
    //         return;
    //     }

    //     $appName = env('APP_NAME');
    //     $expiryDate = Carbon::parse($pass->strEndDate)->format('d-m-Y');
    //     $notificationMessage = "Dear {$customer->customername}, your pass is set to expire on {$expiryDate}. Please renew it to continue enjoying the benefits.";

    //     $notificationData = [
    //         "id" => $pass->iCustomerId,
    //         'title' => $appName,
    //         'body' => $notificationMessage,
    //         'guid' => $pass->id,  // Assuming pass ID as unique identifier
    //         'type' => "pass-expiry",
    //         'service' => "rider"
    //     ];

    //     $this->notificationController->notification($notificationData);
    // }
}
