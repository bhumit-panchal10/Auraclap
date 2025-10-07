@extends('layouts.app')

@section('title', 'Trip Detail')

@section('content')

    @php
        use Carbon\Carbon;
    @endphp

    {!! Toastr::message() !!}

    <!-- Page-content -->
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Trip Detail</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('customer.index') }}" class="text-slate-400 dark:text-zink-200">Customer</a>
                    </li>
                    <li class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('customer.trip_list', $History->iCustomerId ) }}" class="text-slate-400 dark:text-zink-200">Trip List</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Trip Detail
                    </li>
                </ul>
            </div>

            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-5">

                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-zink-500">
                            <h6 class="mb-4 text-15">Trip Detail Of <span style="color:red;"> {{ $History->strCustomerName }} </span> </h6>
                            <a href="javascript: history.go(-1)">
                                <button type="button"
                                    class="mb-4 text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                    data-modal-target="AddModal">
                                    <i class="ri-arrow-left-line"></i> Back
                                </button>
                            </a>
                        </div>    
                        <div>

                            <div class="mt-5 tab-content">
                                <div class="block tab-pane" id="homeArrow">


                                    <div class="overflow-x-auto">
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 font-bold w-2/3 underline ">pickup</div>
                                        <div class="mb2 font-bold w-1/3 underline ">{{ Carbon::parse($History->startRideDateTime)->isoFormat('ddd, Do MMMM') }}</div>
                                        <div class="mb2 font-bold w-1/3 underline ">{{ date('h:i A',strtoTime($History->startRideDateTime)) }}</div>
                                     </div>
                                     <div class="">&nbsp;&nbsp;&nbsp;&nbsp;{{ $History->strSource }}</div>
                                     <hr>
                                     <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 font-bold w-2/3 underline ">drop off</div>
                                        <div class="mb2 mt-5 font-bold w-1/3 underline ">{{ Carbon::parse($History->endRideDateTime)->isoFormat('ddd, Do MMMM') }}</div>
                                        <div class="mb2 mt-5 font-bold w-1/3 underline ">{{  date('h:i A',strtoTime($History->endRideDateTime)) }}</div>
                                     </div>
                                     <div class="">&nbsp;&nbsp;&nbsp;&nbsp;{{ $History->strDestination }}</div>
                                     <hr>
                                     <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 font-bold w-2/3 underline ">distance travelled</div>
                                        <div class="mb2 mt-5 font-bold w-1/3 underline ">time taken</div>
                                     </div>
                                     <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 w-2/3 ">{{$History->iDistanceInKM }}</div>
                                        <div class="mb2 mt-5  w-1/3 ">{{$History->strApproxTime }}</div>
                                     </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--end card-->
                
                <div class="card">
                    <div class="card-body">
                        <h6 class="mb-4 text-15">payment breakdown </h6>
                        <div>

                            <div class="mt-5 tab-content">
                                <div class="block tab-pane" id="homeArrow">


                                    <div class="overflow-x-auto">
                                    <hr>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 w-2/3 ">trip fare</div>
                                        <div class="mb2 mt-5  w-1/3 ">₹ {{$History->decNetAmount }}</div>
                                     </div>
                                     <hr>
                                     <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 w-2/3 ">toll tax</div>
                                        <div class="mb2 mt-5  w-1/3 ">
                                            @if($History->toll_tax_charge)
                                                ₹ {{$History->toll_tax_charge }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                     </div>
                                     <hr>
                                     <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 w-2/3 ">commission</div>
                                        <div class="mb2 mt-5  w-1/3 ">
                                            
                                            @if($History->decDriverCharges)
                                              ₹ {{ $History->decDriverCharges }}
                                            @else
                                                -
                                            @endif
                                        
                                        </div>
                                     </div>
                                    <hr>
                                        @php
                                            $total =$History->decNetAmount + $History->toll_tax_charge - $History->decDriverCharges ;
                                        @endphp
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="mb2 mt-5 w-2/3 ">total earning</div>
                                        <div class="mb2 mt-5  w-1/3 ">₹ {{ $total }}</div>
                                     </div>
                                    
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div><!--end card-->

            </div>
            <!-- End Page-content -->

        </div>
    </div>

@endsection

@section('script')

@endsection

