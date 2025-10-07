<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\Driver;
use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;


class SendRideNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $driver;
    protected $tripId;
    protected $source;
    protected $destination;

    /**
     * Create a new job instance.
     */
    // public function __construct(Driver $driver, Customer $customer, $rideDetails)
    // {
    //     $this->driver = $driver;
    //     $this->customer = $customer;
    //     $this->rideDetails = $rideDetails;
    // }
    
    public function __construct($driver, $tripId, $source, $destination)
    {
        $this->driver = $driver;
        $this->tripId = $tripId;
        $this->source = $source;
        $this->destination = $destination;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $this->notifyDriver($this->driver, $this->customer, $this->rideDetails);
    }

    protected function notifyDriver($driver, $customer, $rideDetails)
    {
        // Your notification logic goes here
    }
}
