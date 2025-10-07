@extends('layouts.app')

@section('title', 'Edit Category')
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
                        <a href="{{ route('categories.index') }}" class="text-slate-400 dark:text-zink-200">Rate List</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Edit Category
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
                                            <h5 class="text-16" id="exampleModalLabel">Edit Category</h5>
                                            <a href="{{ route('categories.index') }}">
                                                <button type="button"
                                                    class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                                    data-modal-target="AddModal">
                                                    <i class="ri-arrow-left-line"></i> Back
                                                </button>
                                            </a>
                                        </div>
                                        <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                            <form class="tablelist-form" onsubmit="return EditvalidateFile()"
                                                action="{{ route('categories.update') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" id="Categoriesid" name="Categoriesid"
                                                    value="{{ $categories->Categories_id }}" />
                                                <div class="grid grid-cols-3 gap-4">
                                                    <div class="mb-3">
                                                        <label for="Countryname-field"
                                                            class="inline-block mb-2 text-base font-medium">Category
                                                            Name
                                                            <span class="text-red-500"> *</span>
                                                        </label>
                                                        <input type="text" name="Category_name" id="EditCategoryName"
                                                            maxlength="40" value="{{ $categories->Category_name }}"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Category Name" required autocomplete="off"
                                                            autofocus>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="Countryname-field"
                                                            class="inline-block mb-2 text-base font-medium">Caption
                                                            </label>
                                                        <input type="text" id="email-field" name="caption"
                                                            maxlength="200"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Caption" autocomplete="off"
                                                            autofocus value="{{ $categories->caption }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="Countryname-field"
                                                            class="inline-block mb-2 text-base font-medium">Rating
                                                            </label>
                                                        <input type="text" id="email-field" name="rating"
                                                            maxlength="200" oninput="this.value = this.value.replace(/[^0-9.]/g, '');"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Rating" autocomplete="off"
                                                            autofocus value="{{ $categories->rating }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">City<span
                                                                class="text-red-500"> *</span> </label>
                                                        <select id="category-field" name="CityId"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            required>
                                                            <option value="" disabled selected>Select a City</option>
                                                            @foreach ($cities as $city)
                                                                <option value="{{ $city->cityId }}"
                                                                    {{ $city->cityId == $categories->city_id ? 'selected' : '' }}>
                                                                    {{ $city->cityName }}</option>  
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Hours
                                                            </label>
                                                        <input type="text" id="email-field" name="hours"
                                                            maxlength="200"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Hours" autocomplete="off"
                                                            autofocus value="{{ $categories->hours }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Onwards Amount
                                                            </label>
                                                        <input type="text" id="email-field" name="onwards_amount"
                                                            maxlength="200"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Onwards Amount" autocomplete="off"
                                                            autofocus value="{{ $categories->onwards_amount }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Countryname-field"
                                                            class="inline-block mb-2 text-base font-medium">warranty

                                                        </label>
                                                        <input type="text" name="warranty" id="Editwarranty"
                                                            value="{{ $categories->warranty }}" maxlength="40"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter warranty" autocomplete="off" autofocus>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">Home
                                                            Category Image</label>

                                                        <!-- Image Tag for displaying the image -->
                                                        <input type="file" name="homeCategoryimage"
                                                            id="EdithomeCategoryimage" maxlength="150"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Home Category Image" autocomplete="off"
                                                            autofocus>

                                                        <input type="hidden" name="hiddenimagePhoto" id="hiddenimagePhoto"
                                                            value="">

                                                        <img class="img-fluid"
                                                            src="{{ asset('upload/Home-category-image/' . $categories->home_cate_image) }}"
                                                            alt="" height="50" width="50"
                                                            id="Edit_home_CategoryImage">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">Category
                                                            Image</label>

                                                        <!-- Image Tag for displaying the image -->
                                                        <input type="file" name="Categoryimage" id="EditCategoryimage"
                                                            maxlength="150"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Category Image" autocomplete="off" autofocus>

                                                        <input type="hidden" name="hiddeniconPhoto" id="hiddeniconPhoto"
                                                            value="">

                                                        <img class="img-fluid"
                                                            src="{{ asset('upload/category-image/' . $categories->Categories_img) }}"
                                                            alt="" height="50" width="50"
                                                            id="EditCategoryImage">
                                                    </div>


                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">RateCard
                                                            Pdf</label>

                                                        <!-- Image Tag for displaying the image -->
                                                        <input type="file" name="RateCardPdf" id="EditRateCardPdf"
                                                            maxlength="150"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter RateCard Pdf" autocomplete="off" autofocus>

                                                        <input type="hidden" name="hiddenRateCardPdf"
                                                            id="hiddenRateCardPdf" value="">

                                                        <a href="{{ asset('upload/RateCardPdf/' . $categories->ratecard_pdf) }}"
                                                            target="_blank"
                                                            style="text-decoration:underline; color:#0390fc;">View
                                                            Pdf</a>

                                                        <div id="EditExistingPdfLink" class="mt-2"></div>

                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">Category
                                                            Icon</label>

                                                        <!-- Image Tag for displaying the image -->
                                                        <input type="file" name="Categoryicon" id="EditCategoryIcon"
                                                            maxlength="150"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Category Icon" autocomplete="off"
                                                            autofocus>

                                                        <input type="hidden" name="hiddencaticonPhoto"
                                                            id="hiddencaticonPhoto" value="">

                                                        <img class="img-fluid"
                                                            src="{{ asset('upload/category-icon/' . $categories->Categories_icon) }}"
                                                            alt="" height="50" width="50"
                                                            id="EditCategoryicon">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="Categoryimage-field"
                                                            class="inline-block mb-2 text-base font-medium">Carousel
                                                            image</label>

                                                        <!-- Image Tag for displaying the image -->
                                                        <input type="file" name="Carouselimage" id="EditCarouselImage"
                                                            maxlength="150"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Carousel image" autocomplete="off"
                                                            autofocus>

                                                        <input type="hidden" name="hiddenCarouselimage"
                                                            id="hiddenCarouselimage" value="">

                                                        <img class="img-fluid"
                                                            src="{{ asset('upload/carousel-icon/' . $categories->carousel_image) }}"
                                                            alt="" height="50" width="50"
                                                            id="EditCarouselimage">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="email-field"
                                                            class="inline-block mb-2 text-base font-medium">Meta
                                                            Title</label>
                                                        <input type="text" id="email-field" name="meta_title"
                                                            maxlength="250" value="{{ $categories->meta_title }}"
                                                            class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                            placeholder="Enter Meta Title" autocomplete="off" autofocus>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 gap-4">

                                                    <div class="mb-3">
                                                        <span style="color:red;"></span>Meta Keyword
                                                        <textarea id="metaKeyword" name="metaKeyword" class="ckeditor-classic text-slate-800"
                                                            style="height: 300px !important;">{{ $categories->meta_keyword }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4">
                                                    <div class="mb-3">
                                                        <span style="color:red;"></span>Meta Description
                                                        <textarea id="metaDescription" name="metaDescription" class="ckeditor-classic text-slate-800"
                                                            style="height: 300px !important;">{{ $categories->meta_description }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4">
                                                    <div class="mb-3">
                                                        <span style="color:red;"></span>Head
                                                        <textarea id="head" name="head" class="ckeditor-classic text-slate-800" style="height: 300px !important;">{{ $categories->meta_head }}</textarea>
                                                    </div>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4">
                                                    <div class="mb-3">
                                                        <span style="color:red;"></span>Body
                                                        <textarea id="body" name="body" class="ckeditor-classic text-slate-800" style="height: 300px !important;">{{ $categories->meta_body }}</textarea>
                                                    </div>
                                                </div>



                                                <div class="flex justify-end gap-2">
                                                    <button type="submit"
                                                        class="text-white bg-custom-500 border-custom-500 btn hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/10"
                                                        id="add-btn">Update</button>
                                                    <a href="{{ route('categories.index') }}">
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

            //var url = "{{ route('manage_rate.category_subcategory_mapping', ':categoryid') }}";
            //url = url.replace(":categoryid", categoryid);
            var url = "{{ route('manage_rate.category_subcategory_mapping') }}";

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

    <script src="{{ asset('assets/libs/%40ckeditor/ckeditor5-build-classic/build/ckeditor.js') }}"></script>

    <script>
        // Initialize CKEditor for each textarea with class 'ckeditor-classic'
        document.addEventListener("DOMContentLoaded", function() {
            ClassicEditor.create(document.querySelector('#metaKeyword')).catch(error => {
                console.error(error);
            });
            ClassicEditor.create(document.querySelector('#metaDescription')).catch(error => {
                console.error(error);
            });
            ClassicEditor.create(document.querySelector('#head')).catch(error => {
                console.error(error);
            });
            ClassicEditor.create(document.querySelector('#body')).catch(error => {
                console.error(error);
            });
        });
    </script>

@endsection
