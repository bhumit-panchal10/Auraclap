<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategories;
use App\Models\Categories;
use App\Models\CategoryOffer;
use App\Models\StateMaster;
use App\Models\CityMaster;
use App\Models\Order;
use App\Models\PrimaryOrder;
use App\Models\Recruitment;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;


class OrderlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function pendingorderlist()
    {

        try {

            // $pendingorderlist = PrimaryOrder::with('orders')->where('order_status', 0)->orderBy('primaryiOrderId', 'asc')
            //     ->paginate(config('app.per_page'));
            $pendingorderlist = PrimaryOrder::with('orders')
                ->whereHas('orders', function ($query) {
                    $query->where('order_status', 0);  // Filter orders where order_status is 0
                })
                ->orderBy('primaryiOrderId', 'asc')
                ->paginate(config('app.per_page'));

            return view('InquiryList.pendingorder', compact('pendingorderlist'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function ongoingorderlist()
    {
        try {
            $ongoingorderlist = PrimaryOrder::with('orders.Technicial')
                ->whereHas('orders', function ($query) {
                    $query->where('order_status', 1);  // Filter orders where order_status is 0
                })
                ->orderBy('primaryiOrderId', 'desc')
                ->paginate(config('app.per_page'));

            return view('InquiryList.ongoingorder', compact('ongoingorderlist'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function completeorderlist()
    {
        try {
            $completeorderlist = PrimaryOrder::with('orders.Technicial')
                ->whereHas('orders', function ($query) {
                    $query->where('order_status', 2);
                })
                ->orderBy('primaryiOrderId', 'desc')
                ->paginate(config('app.per_page'));
            //dd($pendingorderlist);
            return view('InquiryList.completeorder', compact('completeorderlist'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function cancelorderlist()
    {
        try {

            $cancelorderlist = PrimaryOrder::with('orders')
                ->whereHas('orders', function ($query) {
                    $query->where('order_status', 3);
                })
                ->orderBy('primaryiOrderId', 'asc')
                ->paginate(config('app.per_page'));
            return view('InquiryList.cancelorder', compact('cancelorderlist'));
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }

    public function RefundPaymentflag(Request $request, $orderid)
    {

        try {

            $order = Order::where('iOrderId', $orderid)->first();

            if (!$order) {
                Toastr::error('Order not found.');
                return redirect()->back();
            }
            $order->isRefund = 1;
            $order->save();
            Toastr::success('Refund flag updated successfully.');

            return redirect()->back();
        } catch (\Throwable $th) {

            Toastr::error('Error: ' . $th->getMessage());

            return redirect()->back()->withInput();
        }
    }
}
