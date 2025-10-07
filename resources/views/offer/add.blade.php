@extends('layouts.app')

@section('title', 'Add Offer')

@section('content')
    {!! Toastr::message() !!}

    <!-- Page-content -->
    <style>
        /* Dark theme styling for Select2 */
        .select2-container--default .select2-selection--multiple {
            background-color: #fff !important;
            /* Dark blue-gray */
            border: 1px solid #374151 !important;
            color: #fff !important;
        }

        .select2-dropdown {
            background-color: #2563eb !important;
            border: 1px solid #2563eb !important;
            color: #fff !important;
        }

        .select2-results__option {
            color: #fff !important;
            background-color: #2563eb !important;
        }

        .select2-results__option--highlighted[aria-selected] {
            background-color: #2563eb !important;
            /* Highlighted blue */
        }

        .select2-selection__choice {
            background-color: #2563eb !important;
            /* Selected items */
            color: #fff !important;
            border: none !important;
        }

        .select2-selection__choice__remove {
            color: #fff !important;
        }
    </style>

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
                        <a href="{{ route('offer.index') }}" class="text-slate-400 dark:text-zink-200">Offer
                            List</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Add Offer
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
                                            <h5 class="text-16" id="exampleModalLabel">Add Offer</h5>
                                            <button type="button"
                                                class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                                data-modal-target="AddModal">
                                                <a href="{{ route('offer.index') }}">
                                                    <i class="ri-arrow-left-line"></i> Back
                                                </a>


                                            </button>
                                        </div>

                                        <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                            <form onsubmit="return validateFile() && syncEditorContent()"
                                                class="tablelist-form" action="{{ route('offer.store') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf

                                                <div class="grid grid-cols-3 gap-4">
                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Text<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="text" id="email-field" name="text"
                                                            maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ old('text') }}" placeholder="Enter text" required
                                                            autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Percentage
                                                            (%)

                                                            off <span class="text-red-500">*</span> </label>

                                                        <input type="text" id="email-field" name="value" maxlength="2"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ old('value') }}"
                                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                            placeholder="Enter Value" required autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">From
                                                            Date <span class="text-red-500">*</span> </label>

                                                        <input type="text" id="startdatepicker" name="startdate"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ old('startdate') }}" placeholder="Enter From Date"
                                                            required autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">To Date
                                                            <span class="text-red-500">*</span> </label>

                                                        <input type="text" id="enddatepicker" name="enddate"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ old('enddate') }}" placeholder="Enter To Date"
                                                            required autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryid"
                                                            class="inline-block mb-2 text-base font-medium">Category
                                                            Name <span class="text-red-500">*</span> </label>
                                                        <select id="Categoryid" name="Categoryid[]" multiple required
                                                            class="select2-dark form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 dark:focus:border-custom-800 dark:bg-zink-700 dark:text-white w-full">
                                                            @foreach ($categories as $category)
                                                                <option value="{{ $category->Categories_id }}">
                                                                    {{ $category->Category_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>







                                                </div>



                                                <div class="ltr:md:text-end  mt-10">
                                                    <button type="submit"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Submit</button>

                                                    <button type="reset"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                        Clear
                                                    </button>

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

    <script src="{{ asset('assets/libs/%40ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-editor-classic.init.js') }}"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <script>
        $(document).ready(function() {
            $('#Categoryid').select2({
                placeholder: "Select Categories",
                allowClear: true,
                width: "100%",
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector("button[type='reset']").addEventListener("click", function() {
                setTimeout(() => {
                    $("#Categoryid").val(null).trigger("change");
                }, 10);
            });
        });
    </script>

    <script>
        $(function() {

            $("#startdatepicker").datepicker({

                dateFormat: 'd-m-yy',

                minDate: 0

            });

        });



        $(function() {

            $("#enddatepicker").datepicker({

                dateFormat: 'd-m-yy',

                minDate: 0

            });

        });
    </script>

    <script>
        function syncEditorContent() {
            const editorContent = document.querySelector('.ck-editor__editable').innerHTML;
            document.getElementById('description').value = editorContent;
            return true;
        }

        function validateFile() {
            var allowedExtension = ['jpeg', 'jpg', 'png', 'webp', ''];
            var fileExtension = document.getElementById('vendorimg').value.split('.').pop().toLowerCase();
            var isValidFile = false;

            for (var index in allowedExtension) {

                if (fileExtension === allowedExtension[index]) {
                    isValidFile = true;
                    break;
                }
            }
            if (!isValidFile) {
                alert('Allowed Extensions are : *.' + allowedExtension.join(', *.'));
            }
            return isValidFile;
        }
    </script>


    {{-- Add photo --}}

    <script>
        const mobileField = document.getElementById('mobile-field');
        mobileField.addEventListener('input', function() {
            // Remove non-numeric characters
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
    </script>
@endsection
