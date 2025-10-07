@extends('layouts.app')

@section('title', 'Edit Onboard Technicial')
@section('content')
    {!! Toastr::message() !!}
    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: rgba(19, 35, 55, var(--tw-bg-opacity)) !important;
            color: white !important;
            border: 1px solid gray !important;
        }

        .select2-container--default .select2-results__option {
            background-color: #333 !important;
            /* Set background color for options */
            color: white !important;
            /* Set text color for options */
        }

        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #555 !important;
            /* Highlight selected option */
        }

        .select2-container--default .select2-results__option:hover {
            background-color: #444 !important;
            /* Hover effect for options */
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: transparent !important;
            /* Remove background */
            border: 1px solid #aaa;
            border-radius: 4px;
            cursor: default;
            float: left;
            margin-right: 5px;
            margin-top: 5px;
            padding: 0 5px;
        }
    </style>
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
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Master Entry</a>
                    </li>
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('OnboardTechniciallist') }}" class="text-slate-400 dark:text-zink-200">Onboard
                            Technicial
                            List</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Edit Onboard Technicial
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
                                        <div
                                            class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                                            <h5 class="text-16" id="exampleModalLabel">Edit Onboard Technicial</h5>
                                            <a href="{{ route('OnboardTechniciallist') }}">
                                                <button type="button"
                                                    class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                                    data-modal-target="AddModal">
                                                    <i class="ri-arrow-left-line"></i> Back
                                                </button>
                                            </a>
                                        </div>
                                        <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                            <form onsubmit="return EditvalidateFile()" class="tablelist-form"
                                                action="{{ route('onboardupdate') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <input type="hidden" name="Technicialid" id="Technicialid"
                                                    value="{{ $Technicial->Technicial_id }}">

                                                <div class="grid grid-cols-3 gap-4">

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">
                                                            Name</label>

                                                        <input type="text" name="name" id="name"
                                                            value="{{ $Technicial->name }}" maxlength="20"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Name" autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">
                                                            Email</label>

                                                        <input type="text" name="email" id="email"
                                                            value="{{ $Technicial->email }}" maxlength="50"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Email" autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">
                                                            Mobile No</label>

                                                        <input type="text" name="mobile_no" id="mobile_no"
                                                            value="{{ $Technicial->mobile_no }}" maxlength="10"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Mobile No" autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">State</label>
                                                        <select id="stateid" name="stateid"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                            <option value="">Select a State</option>
                                                            @foreach ($states as $state)
                                                                <option value="{{ $state->stateId }}"
                                                                    @if ($Technicial->stateid == $state->stateId) selected @endif>
                                                                    {{ $state->stateName }}
                                                                </option>
                                                            @endforeach
                                                        </select>



                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">
                                                            City</label>

                                                        <input type="text" name="city" id="city"
                                                            value="{{ $Technicial->city }}" maxlength="20"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="City" autocomplete="off" autofocus>

                                                    </div>
                                                    {{-- <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">
                                                            Password</label>

                                                        <input type="password" name="password" id="password" maxlength="10"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Password" autocomplete="off" autofocus>

                                                    </div> --}}
                                                    <div class="mb-3">
                                                        <label for="categoryid"
                                                            class="block mb-2 text-base font-medium">Category</label>
                                                        <div class="relative">
                                                            @php
                                                                $selectedCategories = $TechnicialService
                                                                    ->pluck('Category_id')
                                                                    ->toArray();
                                                            @endphp
                                                            <select id="categoryid" name="categoryid[]" multiple
                                                                class="w-full px-3 py-2 border rounded-lg bg-black text-white border-gray-600 focus:ring focus:ring-custom-500 dark:bg-black dark:text-white dark:border-gray-500">
                                                                <option value="" disabled>Select Categories</option>
                                                                @foreach ($Categories as $cat)
                                                                    <option value="{{ $cat->Categories_id }}"
                                                                        @if (in_array($cat->Categories_id, $selectedCategories)) selected @endif>
                                                                        {{ $cat->Category_name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                    </div>


                                                </div>


                                                <div class="ltr:md:text-end  mt-10">
                                                    <button type="submit"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Update</button>
                                                    <a href="{{ route('manage_rate.index') }}">
                                                        <button type="button"
                                                            class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                            Cancel
                                                        </button>
                                                    </a>
                                                </div>

                                            </form>
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


    <script>
        function getsubcategory() {
            var categoryid = $("#Categoryid").val();

            var url = "{{ route('manage_rate.category_subcategory_mapping', ':categoryid') }}";
            url = url.replace(":categoryid", categoryid);

            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    categoryid: categoryid,
                },
                success: function(data) {
                    $("#AreasubCategoryid").html('');
                    $("#AreasubCategoryid").append(data);
                }
            });
        }
    </script>

    <script>
        function EditvalidateFile() {
            //alert('hello');
            var allowedExtension = ['jpeg', 'jpg', 'png', 'webp', ''];
            var fileExtension = document.getElementById('editmain_img').value.split('.').pop().toLowerCase();
            var isValidFile = false;
            var image = document.getElementById('editmain_img').value;
            for (var index in allowedExtension) {
                if (fileExtension === allowedExtension[index]) {
                    isValidFile = true;
                    break;
                }
            }
            if (image != "") {
                if (!isValidFile) {
                    alert('Allowed Extensions are : *.' + allowedExtension.join(', *.'));
                }
                return isValidFile;
            }
            return true;
        }
    </script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#categoryid').select2({
                placeholder: "Select Category",
                allowClear: true
            });
        });
    </script>

@endsection
