@extends('layouts.app')

@section('title', 'Trip List')

@section('content')

    {!! Toastr::message() !!}

    <!-- Page-content -->
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Trip List</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Driver</a>
                    </li>
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('customer.index') }}" class="text-slate-400 dark:text-zink-200">Customer</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Trip List
                    </li>
                </ul>
            </div>

            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-5">

                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-zink-500">
                            <h6 class="mb-4 text-15">Trip List Of <span style="color:red;"> {{ $Customer->customername }} </span> </h6>
                            <a href="{{ route('customer.index') }}">
                                <button type="button"
                                    class="mb-4 text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                    data-modal-target="AddModal">
                                    <i class="ri-arrow-left-line"></i> Back
                                </button>
                            </a>
                        </div>    
                        <div>

                            <div class="tab-content">
                                <div class="block tab-pane" id="homeArrow">

                                @if (!$History->isEmpty())                                            
                                    <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                        <form onsubmit="return validateFile()" class="tablelist-form"
                                            action="{{ route('customer.trip_list',$id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf

                                            <div class="grid grid-cols-3 gap-4">
                                                <div class="">
                                                    <span style="color:red;"></span>From Date
                                                    <input type="text" id="startdatepicker" name="fromdate"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter From Date" value="<?= isset($FromDate) ? $FromDate : '' ?>"
                                                         autocomplete="off">
                                                </div>

                                                <div class="">
                                                    <span style="color:red;"></span>To Date
                                                    <input type="text" id="enddatepicker" name="todate"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter To Date" value="<?= isset($ToDate) ? $ToDate : '' ?>"
                                                         autocomplete="off">
                                                </div>

                                                <div class="ltr:md:text-end mt-5">
                                                    <button type="submit"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Search</button>
                                                    <a href="{{ route('customer.trip_list',$id) }}">
                                                        <button type="button"
                                                            class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                            Reset
                                                        </button>
                                                    </a>
                                                </div>
                                               
                                            </div>


                                        </form>
                                    </div>

                                    <div class="overflow-x-auto">

                                            <table class="w-full whitespace-nowrap" id="customerTable">
                                                <thead class="bg-slate-100 dark:bg-zink-600">
                                                    <tr>
                                                        
                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="state_name">Sr.no </th>
                                                        
                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="state_name"> Driver Name </th>    

                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="state_name"> Source </th>

                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="state_name"> Destination </th>

                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="state_name"> Amount </th>
                                                        
                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="state_name"> Date </th>    

                                                        <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                            data-sort="action">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="list form-check-all">
                                                    <?php $i = 1; ?>
                                                    @foreach ($History as $history)
                                                    
                                                    <?php 
                                                        
                                                        $destinationpincode = null;
                                                        $sourcepincode = null;
                                                        if (preg_match("/\b\d{6}\b/", $history->strSource, $matches)) {
                                                            $sourcepincode = $matches[0];
                                                        }
                                    
                                                        if (preg_match("/\b\d{6}\b/", $history->strDestination, $matches)) {
                                                            $destinationpincode = $matches[0];
                                                        }
                                    
                                                        $source =  App\Models\AreaMaster::where(['iStatus' => 1, 'isDelete' => 0, 'areaPincode' => $sourcepincode])->first();
                                                        $destination =  App\Models\AreaMaster::where(['iStatus' => 1, 'isDelete' => 0, 'areaPincode' => $destinationpincode])->first();
                                    
                                                        $rideAmount = $history->decNetAmount ?? $history->iAmountAfterAddCharges;
                                                        // Format date with AM/PM
                                                        // $formattedDate = Carbon::parse($history->EnterDateTime)->format('D, jS F h:i A');
                                    
                                                        if ($source) {
                                                            $strSource = $source->areaName;
                                                        } else {
                                                            $strSource = $history->strSource;
                                                        }
                                                        
                                                        if ($destination) {
                                                            $strDestination = $destination->areaName;
                                                        } else {
                                                            $strDestination = $history->strDestination;
                                                        }
                                                    ?>    
                                                        <tr>
                                                            
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                                {{ $i + $History->perPage() * ($History->currentPage() - 1) }}
                                                            </td>
                                                            
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_title">
                                                                {{ $history->name ?? -'' }}
                                                            </td>
                                                            
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_title">
                                                                {{ $strSource ?? -'' }}
                                                            </td>

                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_type">
                                                                {{ $strDestination ?? '-' }}
                                                            </td>

                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_location">
                                                                {{ $rideAmount ?? '-' }}
                                                            </td>
                                                            
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_location">
                                                                @if($history->EnterDateTime)
                                                                    {{ date('d-m-Y', strtotime($history->EnterDateTime))  }}
                                                                @else
                                                                    "-"
                                                                @endif
                                                            </td>
                                                            
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                                <div class="flex gap-2">
                                                                    <div class="edit">
                                                                       
                                                                        <a class="" href="{{ route('customer.trip_details',$history->iTripId) }}" title="Trip Detail">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M17.0839 15.812C19.6827 13.0691 19.6379 8.73845 16.9497 6.05025C14.2161 3.31658 9.78392 3.31658 7.05025 6.05025C4.36205 8.73845 4.31734 13.0691 6.91612 15.812C7.97763 14.1228 9.8577 13 12 13C14.1423 13 16.0224 14.1228 17.0839 15.812ZM12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.15076 5.63604 4.63604C9.15076 1.12132 14.8492 1.12132 18.364 4.63604C21.8787 8.15076 21.8787 13.8492 18.364 17.364L12 23.7279ZM12 12C10.3431 12 9 10.6569 9 9C9 7.34315 10.3431 6 12 6C13.6569 6 15 7.34315 15 9C15 10.6569 13.6569 12 12 12Z"></path></svg>
                                                                        </a>
                                                                        
                                                                    </div>
                                                                </div>
                                                            </td>

                                                            
                                                        </tr>
                                                        <?php $i++; ?>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="flex items-center justify-between">
                                                {{ $History->appends(request()->except('page'))->links() }}
                                            </div>
                                        @else
                                            <div class="noresult">
                                                <div class="text-center p-7">
                                                    <h5 class="mb-2">Sorry! No Result Found</h5>
                                                </div>
                                            </div>
                                    </div>
                                @endif


                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--end card-->

            </div>
            <!-- End Page-content -->

        </div>
    </div>
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <script>
        $(function() {
            $("#startdatepicker").datepicker({
                dateFormat: 'd-m-yy',
                //minDate: 0
            });
        });

        $(function() {
            $("#enddatepicker").datepicker({
                dateFormat: 'd-m-yy',
                //minDate: 0
            });
        });
    </script>

@endsection
