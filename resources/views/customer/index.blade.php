@extends('layouts.app')

@section('title', 'Customer List')
@section('content')

{!! Toastr::message() !!}

<!-- Page-content -->
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Customer List</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Driver</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Customer 
                </li>
            </ul>
        </div>


        <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">

            <div class="xl:col-span-12">
                <div class="card" id="customerList">
                    <div class="card-body">

                        <div class="overflow-x-auto">

                            @if (!$Customer->isEmpty())
                            <form id="bulkDeleteForm" method="POST" action="{{ route('career.deleteselected') }}">
                                @csrf
                                @method('DELETE')

                                <table class="w-full whitespace-nowrap" id="customerTable">
                                    <thead class="bg-slate-100 dark:bg-zink-600">
                                        <tr>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name">Sr.no </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name">Name </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name">Mobile </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name">Email </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name"> State </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name"> City </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name">Photo </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        <?php $i = 1; ?>
                                        @foreach ($Customer as $customer)
                                        <tr class="text-center">

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                {{ $i + $Customer->perPage() * ($Customer->currentPage() - 1) }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customername">
                                                {{ $customer->customername }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customermobile">
                                                {{ $customer->customermobile ?? '-' }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customeremail">
                                                {{ $customer->customeremail ?? '-' }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 stateName">
                                                {{ $customer->stateName ?? '-' }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 cityName">
                                                {{ $customer->cityName ?? '-' }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 strPhoto" style="display: flex;justify-content: center;">
                                                @if ($customer->strPhoto)
                                                <img class="img-fluid"
                                                    src="{{ asset('/upload/customer/photo/') . '/' . $customer->strPhoto }}"
                                                    alt="" height="50" width="50">
                                                @else
                                                <img class="img-fluid"
                                                    src="{{ asset('assets/images/noimage.png') }}"
                                                    alt="" height="50" width="50">
                                                @endif
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                <div class="gap-2" style="display: flex;justify-content: center;">
                                                    <div class="edit">
                                                        <a class="" href="{{ route('customer.trip_list', $customer->customerid) }}" title="Trip List">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                                <path d="M17.0839 15.812C19.6827 13.0691 19.6379 8.73845 16.9497 6.05025C14.2161 3.31658 9.78392 3.31658 7.05025 6.05025C4.36205 8.73845 4.31734 13.0691 6.91612 15.812C7.97763 14.1228 9.8577 13 12 13C14.1423 13 16.0224 14.1228 17.0839 15.812ZM12 23.7279L5.63604 17.364C2.12132 13.8492 2.12132 8.15076 5.63604 4.63604C9.15076 1.12132 14.8492 1.12132 18.364 4.63604C21.8787 8.15076 21.8787 13.8492 18.364 17.364L12 23.7279ZM12 12C10.3431 12 9 10.6569 9 9C9 7.34315 10.3431 6 12 6C13.6569 6 15 7.34315 15 9C15 10.6569 13.6569 12 12 12Z"></path>
                                                            </svg>
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
                                    {!! $Customer->links() !!}
                                </div>
                            </form>
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