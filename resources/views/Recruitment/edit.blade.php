@extends('layouts.app')

@section('title', 'Edit Recruitment')

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
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Master Entry</a>
                    </li>
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="{{ route('Recruitment.index') }}" class="text-slate-400 dark:text-zink-200">Recruitment
                            List</a>
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
                                            <h5 class="text-16" id="exampleModalLabel">Edit Recruitment</h5>
                                            <a href="{{ route('Recruitment.index') }}">
                                                <button type="button"
                                                    class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                                    data-modal-target="AddModal">
                                                    <i class="ri-arrow-left-line"></i> Back
                                                </button>
                                            </a>
                                        </div>
                                        <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                            <form onsubmit="return validateFile()" class="tablelist-form"
                                                action="{{ route('Recruitment.update', $data->Recruitment_id) }}"
                                                method="POST" enctype="multipart/form-data">
                                                @csrf

                                                <div class="grid grid-cols-3 gap-4">
                                                    <input type="hidden" name="Recruitment_id"
                                                        value="{{ $data->Recruitment_id }}">

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Job Title<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="text" id="email-field" name="job_title"
                                                            maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->job_title }}" placeholder="Enter Job Title"
                                                            required autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Job Type<span
                                                                class="text-red-500">*</span> </label>

                                                        <select id="job_type" name="job_type"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            required autocomplete="off" autofocus>
                                                            <option value="">Please Select</option>
                                                            <option value="1" @selected($data->job_type == 1)>Full Time
                                                            </option>
                                                            <option value="2" @selected($data->job_type == 2)>Part Time
                                                            </option>
                                                            <option value="3" @selected($data->job_type == 3)>Contract
                                                            </option>

                                                        </select>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Experience<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="text" id="experience" name="experience"
                                                            maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->experience }}" placeholder="Enter Experience"
                                                            required autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Qualification<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="text" id="qualification" name="qualification"
                                                            maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->qualification }}"
                                                            placeholder="Enter Qualification" required autocomplete="off"
                                                            autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Location<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="text" id="location" name="location" maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->location }}" placeholder="Enter Location"
                                                            required autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Timing<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="Time" id="timing" name="timing"
                                                            maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->timing }}" placeholder="Enter Timing"
                                                            required autocomplete="off" autofocus>

                                                    </div>
                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Number Of
                                                            Opening<span class="text-red-500">*</span> </label>

                                                        <input type="text" id="number_of_opening"
                                                            name="number_of_opening" maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->number_of_opening }}"
                                                            placeholder="Enter Number Of Opening" required
                                                            autocomplete="off" autofocus>

                                                    </div>

                                                    <div class="mb-3">

                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Salary<span
                                                                class="text-red-500">*</span> </label>

                                                        <input type="text" id="salary" name="salary"
                                                            maxlength="100"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            value="{{ $data->salary }}" placeholder="Enter Salary"
                                                            required autocomplete="off" autofocus>

                                                    </div>


                                                </div>



                                                <div class="ltr:md:text-end  mt-10">
                                                    <button type="submit"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Update</button>
                                                    <a href="{{ route('Recruitment.index') }}">
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
    </script>


    <script>
        $(function() {

            $("#Editstartdatepicker").datepicker({

                dateFormat: 'd-m-yy',

                minDate: 0

            });

        });



        $(function() {

            $("#Editenddatepicker").datepicker({

                dateFormat: 'd-m-yy',

                minDate: 0

            });

        });



        function getcity() {
            var state = $("#areastateId").val();
            var url = "{{ route('area.state_city_mapping', ':state') }}";
            url = url.replace(":state", state);
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    state: state,
                },
                success: function(data) {
                    $("#areacityId").html('');
                    $("#areacityId").append(data);
                }
            });
        }

        function EditvalidateFile() {
            //alert('hello');
            var allowedExtension = ['jpeg', 'jpg', 'png', 'webp'];
            var fileExtension = document.getElementById('editvendorimg').value.split('.').pop().toLowerCase();
            var isValidFile = false;
            var image = document.getElementById('editvendorimg').value;
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

    {{-- Edit photo --}}
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#hello').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#editvendorimg").change(function() {
            html =
                '<img src="' + readURL(this) +
                '"   id="hello" width="70px" height = "70px" > ';
            $('#viewimg').html(html);
        });
    </script>
@endsection
