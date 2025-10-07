@extends('layouts.app')

@section('title', 'OngoingOrder List')
@section('content')

    {!! Toastr::message() !!}
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

    <!-- Page-content -->
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">OngoingOrder List</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Master Entry</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        OngoingOrder List
                    </li>
                </ul>
            </div>


            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">

                <div class="xl:col-span-12">
                    <div class="card" id="customerList">
                        @include('InquiryList.orderTab')
                        <div class="card-body">

                            <div class="overflow-x-auto">
                                @if (!$ongoingorderlist->isEmpty())
                                    <form id="bulkDeleteForm" method="POST"
                                        action="{{ route('manage_rate.deleteselected') }}">
                                        @csrf
                                        @method('DELETE')

                                        <table class="w-full whitespace-nowrap" id="customerTable">
                                            <thead class="bg-slate-100 dark:bg-zink-600">
                                                <tr>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Sr.no </th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="are_name">Customer Name</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Customer Phone</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Pincode</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Address</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Accept DateTime</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Technicial</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Payment Mode</th>


                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <?php $i = 1; ?>
                                                @foreach ($ongoingorderlist as $ongoingorder)
                                                    @foreach ($ongoingorder->orders as $order)
                                                        <tr>
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                                {{ $i + $ongoingorderlist->perPage() * ($ongoingorderlist->currentPage() - 1) }}
                                                            </td>
                                                            <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id"
                                                                style="display:none;"><a href="javascript:void(0);"
                                                                    class="fw-medium link-primary id">#VZ2101</a></td>
                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ $ongoingorder->Customer_name ?? '-' }}</td>


                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ $ongoingorder->Customer_phone ?? '-' }}</td>

                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ $ongoingorder->Pincode ?? '-' }}</td>


                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ $ongoingorder->Customer_Address ?? '-' }}</td>


                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ date('d-m-Y H:i:s', strtotime($ongoingorder->created_at)) ?? '-' }}
                                                            </td>

                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ $order->Technicial->name ?? '-' }}</td>

                                                            <td
                                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                                {{ $ongoingorder->payment_mode == 1 ? 'online' : 'cash' }}
                                                            </td>

                                                        </tr>
                                                        <?php $i++; ?>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </form>
                                    <div class="flex items-center justify-between mt-5">
                                        {{ $ongoingorderlist->appends(request()->except('page'))->links() }}
                                    </div>
                                @else
                                    <div class="noresult">
                                        <div class="text-center p-7">
                                            <h5 class="mb-2">Sorry! No Result Found</h5>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <!-- End Page-content -->


        </div>
    </div>
@endsection
@section('script')
@endsection
