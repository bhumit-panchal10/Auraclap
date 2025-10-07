@extends('layouts.app')

@section('title', 'Customer Trip List')

@section('content')
    {!! Toastr::message() !!}

    <!-- Page-content -->
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    {{--  <h5 class="text-16">State List</h5>  --}}
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Customer</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Customer Trip List
                    </li>
                </ul>
            </div>


            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">
                <div class="xl:col-span-12">
                    <div class="card" id="customerList">
                        <div class="">
                            <div class="grid grid-cols-1 gap-5 mb-5 ">

                                <div class="rtl:md:text-start">
                                    <div class="bg-white shadow rounded-md dark:bg-zink-600">
                                        <!--<div-->
                                        <!--    class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">-->
                                        <!--    <h5 class="text-16" id="exampleModalLabel">Customer Trip List</h5>-->
                                        <!--    <a href="{{ route('customer.index') }}">-->
                                        <!--        <button type="button"-->
                                        <!--            class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"-->
                                        <!--            data-modal-target="AddModal">-->
                                        <!--                <i class="ri-arrow-left-line"></i> Back-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</div>-->

                                        <div class="p-4">
                                            
                                            
                                        </div>


                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <!-- End Page-content -->

        </div>
    </div>

    <div id="EditModal" modal-center=""
        class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
        <div class="w-screen md:w-[30rem] bg-white shadow rounded-md dark:bg-zink-600">
            <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                <h5 class="text-16" id="exampleModalLabel">Add Status</h5>
                <button data-modal-close="EditModal"
                    class="transition-all duration-200 ease-linear text-slate-400 hover:text-slate-500"><i data-lucide="x"
                        class="size-5"></i></button>
            </div>
            <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                <form class="tablelist-form" action="{{ route('driver.driver_documents_status_change') }}"
                    method="POST">
                    @csrf
                    <input type="hidden" id="driverMasterId" name="driverMasterId" />
                    <input type="hidden" id="status_name" name="status_name" />

                    <div class="mb-3">
                        <label for="Countryname-field" class="inline-block mb-2 text-base font-medium">Status
                        </label>
                        <select name="status" id="status" onchange="statuschange();"
                            class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                            autofocus required>
                            <option value="" selected="">Select Status</option>
                            <option value="1">Approve</option>
                            <option value="2">Reject</option>

                        </select>
                    </div>

                    <div class="mb-3" style="display: none" id="commentdiv">
                        <label for="email-field" class="">Comment</label>
                        <textarea name="comment" id="comment"
                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                            style="height: 100px !important;" placeholder="Enter Comment"></textarea>
                    </div>

                    <div class="flex justify-end gap-2">

                        <!--<button type="submit" data-modal-close="EditModal"-->
                        <!--    class="text-white bg-green-500 border-green-500 btn hover:text-white hover:bg-green-600 hover:border-green-600 focus:text-white focus:bg-green-600 focus:border-green-600 focus:ring focus:ring-green-100 active:text-white active:bg-green-600 active:border-green-600 active:ring active:ring-green-100 dark:ring-green-400/10"-->
                        <!--    id="add-btn">Submit</button>-->
                        <button type="submit"
                            class="text-white bg-green-500 border-green-500 btn hover:bg-green-600 focus:bg-green-600">Submit</button>
                        <button type="reset" data-modal-close="EditModal"
                            class="text-white btn bg-slate-500 border-slate-500 hover:text-white hover:bg-slate-600 hover:border-slate-600 focus:text-white focus:bg-slate-600 focus:border-slate-600 focus:ring focus:ring-slate-100 active:text-white active:bg-slate-600 active:border-slate-600 active:ring active:ring-slate-100 dark:ring-slate-400/10"
                            data-modal-close="EditModal">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        function getEditData(id, status) {
            $("#driverMasterId").val(id);
            $("#status_name").val(status);
        }

        function statuschange() {
            let status = $("#status").val();

            if (status == 1 || status == "" || status == 0) {
                $("#commentdiv").hide();
                $("#comment").prop('required', false);
            } else {
                $("#commentdiv").show();
                $("#comment").prop('required', true);
            }

        }
    </script>
@endsection
