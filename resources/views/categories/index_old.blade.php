@extends('layouts.app')

@section('title', 'Categories List')
@section('content')

    {!! Toastr::message() !!}

    <!-- Page-content -->
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">Category List</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Master Entry</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        Category List
                    </li>
                </ul>
            </div>


            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">
                <div class="xl:col-span-5">
                    <div class="card" id="customerList">
                        <div class="">
                            <div class="grid grid-cols-1 gap-5 mb-5 ">

                                <div class="rtl:md:text-start">
                                    <div class="bg-white shadow rounded-md dark:bg-zink-600">
                                        <div
                                            class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                                            <h5 class="text-16" id="exampleModalLabel">Add Category</h5>
                                        </div>
                                        <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                            <form method="POST" onsubmit="return validateFile()"
                                                action="{{ route('categories.store') }}" id="registerForm"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">Category Name<span
                                                            class="text-red-500"> *</span> </label>
                                                    <input type="text" id="email-field" name="Categoryname"
                                                        maxlength="40"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter Categories Name" required autocomplete="off"
                                                        autofocus>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">Home Category
                                                        Image <span class="text-red-500"> *</span></label>
                                                    <input type="file" id="homeCategoryimage" name="homeCategoryimage"
                                                        maxlength="150"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter Home Category Img" autocomplete="off" required
                                                        autofocus>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">Category
                                                        Image <span class="text-red-500"> *</span></label>
                                                    <input type="file" id="Categoryimage" name="Categoryimage"
                                                        maxlength="150"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter Categories Img" autocomplete="off" required
                                                        autofocus>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">Warranty</label>
                                                    <input type="text" id="email-field" name="warranty" maxlength="40"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter Warranty" autocomplete="off" autofocus>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">
                                                        RateCard Pdf </label>
                                                    <input type="file" id="ratecard_pdf" name="ratecard_pdf"
                                                        maxlength="150"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter RateCard Pdf" autocomplete="off" autofocus>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">
                                                        Category icon </label>
                                                    <input type="file" id="Category_icon" name="Category_icon"
                                                        maxlength="150"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter Category icon" autocomplete="off" autofocus>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="email-field"
                                                        class="inline-block mb-2 text-base font-medium">
                                                        Carousel image </label>
                                                    <input type="file" id="carousel_image" name="carousel_image"
                                                        maxlength="150"
                                                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                        placeholder="Enter carousel image" autocomplete="off" autofocus>
                                                </div>

                                                <div class="mt-10">
                                                    <button type="submit"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Submit</button>
                                                    <button type="reset"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Clear</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="xl:col-span-7">
                    <div class="card" id="customerList">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-5 mb-5 xl:grid-cols-0">

                                <div class="rtl:md:text-start">
                                    @if (!$categories->isEmpty())
                                        <button type="button" onclick="confirmBulkDelete()"
                                            class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                            Delete Selected
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                @if (!$categories->isEmpty())
                                    <form id="bulkDeleteForm" method="POST"
                                        action="{{ route('categories.deleteselected') }}">
                                        @csrf
                                        @method('DELETE')

                                        <table class="w-full whitespace-nowrap" id="customerTable">
                                            <thead class="bg-slate-100 dark:bg-zink-600">
                                                <tr>
                                                    <th class="px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500"
                                                        scope="col" style="width: 50px;">
                                                        <input
                                                            class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500 checked:disabled:bg-custom-400 checked:disabled:border-custom-400"
                                                            type="checkbox" onclick="checkAll(this);" id="check_all">
                                                    </th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                        data-sort="state_name">Sr.no </th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                        data-sort="state_name">Category Name </th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                        data-sort="state_name">Category Image</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                        data-sort="state_name"> Status </th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                        data-sort="action">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <?php $i = 1; ?>
                                                @foreach ($categories as $category)
                                                    <tr class="text-center">
                                                        <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500"
                                                            scope="row">
                                                            <input
                                                                class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500 checked:disabled:bg-custom-400 checked:disabled:border-custom-400"
                                                                type="checkbox" name="Categories_ids[]"
                                                                value="{{ $category->Categories_id }}">
                                                        </td>

                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                            {{ $i + $categories->perPage() * ($categories->currentPage() - 1) }}
                                                        </td>
                                                        {{-- <td class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id"
                                                            style="display:none;"><a href="javascript:void(0);"
                                                                class="fw-medium link-primary id">#VZ2101</a></td> --}}
                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                            {{ $category->Category_name }}
                                                        </td>

                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 Categories_icon">
                                                            @if ($category->Categories_img)
                                                                <img class="img-fluid"
                                                                    src="{{ asset('upload/category-image') . '/' . $category->Categories_img }}"
                                                                    alt="" height="50" width="50">
                                                            @else
                                                                <img class="img-fluid"
                                                                    src="{{ asset('assets/images/noimage.png') }}"
                                                                    alt="" height="50" width="50">
                                                            @endif
                                                        </td>



                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_location">
                                                            <div class="items-center">
                                                                <div
                                                                    class="relative inline-block w-10 align-middle transition duration-200 ease-in ltr:mr-2 rtl:ml-2">
                                                                    @if ($category->iStatus == 1)
                                                                        <input type="checkbox" name="greenIconSwitch"
                                                                            id="greenIconSwitch{{ $category->Categories_id }}"
                                                                            class="absolute block size-5 transition duration-300 ease-linear border-2 border-slate-200 dark:border-zink-500 rounded-full appearance-none cursor-pointer bg-white/80 dark:bg-zink-600 peer/published checked:bg-white dark:checked:bg-white ltr:checked:right-0 rtl:checked:left-0 checked:bg-none checked:border-green-500 dark:checked:border-green-500 arrow-none after:absolute after:text-slate-500 dark:after:text-zink-200 after:content-['\eb99'] after:text-xs after:inset-0 after:flex after:items-center after:justify-center after:font-remix after:leading-none checked:after:text-green-500 dark:checked:after:text-green-500 checked:after:content-['\eb7b']"
                                                                            onchange="updateStatus(<?= $category->iStatus ?>,<?= $category->Categories_id ?>);"
                                                                            checked="">
                                                                    @else
                                                                        <input type="checkbox" name="greenIconSwitch"
                                                                            id="greenIconSwitch{{ $category->Categories_id }}"
                                                                            class="absolute block size-5 transition duration-300 ease-linear border-2 border-slate-200 dark:border-zink-500 rounded-full appearance-none cursor-pointer bg-white/80 dark:bg-zink-600 peer/published checked:bg-white dark:checked:bg-white ltr:checked:right-0 rtl:checked:left-0 checked:bg-none checked:border-green-500 dark:checked:border-green-500 arrow-none after:absolute after:text-slate-500 dark:after:text-zink-200 after:content-['\eb99'] after:text-xs after:inset-0 after:flex after:items-center after:justify-center after:font-remix after:leading-none checked:after:text-green-500 dark:checked:after:text-green-500 checked:after:content-['\eb7b']"
                                                                            onchange="updateStatus(<?= $category->iStatus ?>,<?= $category->Categories_id ?>);">
                                                                    @endif
                                                                    <label
                                                                        for="greenIconSwitch{{ $category->Categories_id }}"
                                                                        class="block h-5 overflow-hidden duration-300 ease-linear border rounded-full cursor-pointer cursor-pointertransition border-slate-200 dark:border-zink-500 bg-slate-200 dark:bg-zink-600 peer-checked/published:bg-green-500 peer-checked/published:border-green-500"></label>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        <td
                                                            class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                            <div class="gap-2">
                                                                <div class="edit">
                                                                    <a class="mx-1" title="Edit" href="#"
                                                                        data-modal-target="EditModal"
                                                                        onclick="getEditData(<?= $category->Categories_id ?>)">
                                                                        <i class="ri-edit-2-fill"></i>
                                                                    </a>

                                                                    <a class="mx-1" title="Delete" href="#"
                                                                        onclick="confirmSingleDelete({{ $category->Categories_id }})">
                                                                        <i class="ri-delete-bin-5-fill"></i>
                                                                    </a>

                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php $i++; ?>
                                                @endforeach
                                            </tbody>
                                        </table>

                                    </form>
                                    <div class="flex items-center justify-between mt-5">
                                        {!! $categories->links() !!}
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

            <div id="EditModal" modal-center=""
                class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
                <div class="w-screen md:w-[30rem] bg-white shadow rounded-md dark:bg-zink-600">
                    <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                        <h5 class="text-16" id="exampleModalLabel">Edit Category</h5>
                        <button data-modal-close="EditModal"
                            class="transition-all duration-200 ease-linear text-slate-400 hover:text-slate-500"><i
                                data-lucide="x" class="size-5"></i></button>
                    </div>
                    <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                        <form class="tablelist-form" onsubmit="return EditvalidateFile()"
                            action="{{ route('categories.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="Categoriesid" name="Categoriesid" />
                            <div class="mb-3">
                                <label for="Countryname-field" class="inline-block mb-2 text-base font-medium">Category
                                    Name
                                    <span class="text-red-500"> *</span>
                                </label>
                                <input type="text" name="Category_name" id="EditCategoryName" maxlength="40"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter Category Name" required autocomplete="off" autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="Countryname-field" class="inline-block mb-2 text-base font-medium">warranty
                                    
                                </label>
                                <input type="text" name="warranty" id="Editwarranty" maxlength="40"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter warranty" autocomplete="off" autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="Categoryimage-field" class="inline-block mb-2 text-base font-medium">Home
                                    Category Image</label>

                                <!-- Image Tag for displaying the image -->
                                <input type="file" name="homeCategoryimage" id="EdithomeCategoryimage"
                                    maxlength="150"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter Home Category Image" autocomplete="off" autofocus>

                                <input type="hidden" name="hiddenimagePhoto" id="hiddenimagePhoto" value="">

                                <img class="img-fluid" src="" alt="" height="50" width="50"
                                    id="Edit_home_CategoryImage">
                            </div>

                            <div class="mb-3">
                                <label for="Categoryimage-field" class="inline-block mb-2 text-base font-medium">Category
                                    Image</label>

                                <!-- Image Tag for displaying the image -->
                                <input type="file" name="Categoryimage" id="EditCategoryimage" maxlength="150"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter Category Image" autocomplete="off" autofocus>

                                <input type="hidden" name="hiddeniconPhoto" id="hiddeniconPhoto" value="">

                                <img class="img-fluid" src="" alt="" height="50" width="50"
                                    id="EditCategoryImage">
                            </div>
                            
                           
                            <div class="mb-3">
                                <label for="Categoryimage-field" class="inline-block mb-2 text-base font-medium">RateCard
                                    Pdf</label>

                                <!-- Image Tag for displaying the image -->
                                <input type="file" name="RateCardPdf" id="EditRateCardPdf" maxlength="150"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter RateCard Pdf" autocomplete="off" autofocus>

                                <input type="hidden" name="hiddenRateCardPdf" id="hiddenRateCardPdf" value="">

                                <div id="EditExistingPdfLink" class="mt-2"></div>

                            </div>
                            
                            <div class="mb-3">
                                <label for="Categoryimage-field" class="inline-block mb-2 text-base font-medium">Category
                                    Icon</label>

                                <!-- Image Tag for displaying the image -->
                                <input type="file" name="Categoryicon" id="EditCategoryIcon" maxlength="150"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter Category Icon" autocomplete="off" autofocus>

                                <input type="hidden" name="hiddencaticonPhoto" id="hiddencaticonPhoto" value="">

                                <img class="img-fluid" src="" alt="" height="50" width="50"
                                    id="EditCategoryicon">
                            </div>
                            
                            <div class="mb-3">
                                <label for="Categoryimage-field" class="inline-block mb-2 text-base font-medium">Carousel image</label>

                                <!-- Image Tag for displaying the image -->
                                <input type="file" name="Carouselimage" id="EditCarouselImage" maxlength="150"
                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                    placeholder="Enter Carousel image" autocomplete="off" autofocus>

                                <input type="hidden" name="hiddenCarouselimage" id="hiddenCarouselimage" value="">

                                <img class="img-fluid" src="" alt="" height="50" width="50"
                                    id="EditCarouselimage">
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

    <script>
        function getEditData(id) {
            var url = "{{ route('categories.edit', ':id') }}";
            url = url.replace(":id", id);
            if (id) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        id
                    },
                    success: function(data) {
                        var obj = JSON.parse(data);


                        var imageUrlicon = obj.Categories_img ? "{{ asset('upload/category-image') }}/" + obj
                            .Categories_img : "{{ asset('assets/images/noimage.png') }}";

                        var homeimageUrl = obj.home_cate_image ? "{{ asset('upload/Home-category-image') }}/" +
                            obj
                            .home_cate_image : "{{ asset('assets/images/noimage.png') }}";
                            
                        var categoryiconUrl = obj.Categories_icon ? "{{ asset('upload/category-icon') }}/" +
                            obj
                            .Categories_icon : "{{ asset('assets/images/noimage.png') }}";
                            
                         var categoryCarouselUrl = obj.carousel_image ? "{{ asset('upload/carousel-icon') }}/" +
                            obj
                            .carousel_image : "{{ asset('assets/images/noimage.png') }}";

                        var pdfUrl = obj.ratecard_pdf ? "{{ asset('upload/RateCardPdf') }}/" + obj
                            .ratecard_pdf : null;

                        if (pdfUrl) {
                            let linkHtml = `
                                <a href="${pdfUrl}" target="_blank" class="inline-flex items-center text-blue-600 hover:underline">
                                    <i class="fas fa-file-pdf text-red-600 mr-1"></i> View PDF
                                </a>`;
                            $('#EditExistingPdfLink').html(linkHtml);
                        } else {
                            $('#EditExistingPdfLink').html(
                                '<span class="text-gray-500 italic">No PDF uploaded</span>');
                        }


                        $("#EditCategoryName").val(obj.Category_name);

                        $("#Editwarranty").val(obj.warranty);

                        $("#hiddenimagePhoto").val(obj.home_cate_image ? obj.home_cate_image : '');

                        $("#hiddenRateCardPdf").val(obj.ratecard_pdf ? obj.ratecard_pdf : '');

                        $("#hiddeniconPhoto").val(obj.Categories_img ? obj.Categories_img : '');
                        
                        $("#hiddencaticonPhoto").val(obj.Categories_icon ? obj.Categories_icon : '');
                        
                        $("#hiddenCarouselimage").val(obj.carousel_image ? obj.carousel_image : '');


                        
                        $("#Edit_home_CategoryImage").attr('src', homeimageUrl);

                        $("#EditCategoryImage").attr('src', imageUrlicon);
                        
                        $("#EditCategoryicon").attr('src', categoryiconUrl);
                        
                        $("#EditCarouselimage").attr('src', categoryCarouselUrl);

                        $('#Categoriesid').val(id);
                    },
                    error: function(xhr) {
                        alert('Failed to load data');
                    }
                });
            }
        }
    </script>

    <script>
        function checkAll(source) {
            checkboxes = document.querySelectorAll('input[name="Categories_ids[]"]');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }

        function confirmBulkDelete() {
            const selected = document.querySelectorAll('input[name="Categories_ids[]"]:checked');
            const ids = Array.from(selected).map(checkbox => checkbox.value);

            if (ids.length === 0) {
                Swal.fire({
                    title: 'No Selection',
                    text: 'Please select at least one state to delete.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20 ltr:mr-1 rtl:ml-1',
                    cancelButton: 'text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20',
                },
                confirmButtonText: 'Yes, delete it!',
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(result) {
                if (result.value) {
                    document.getElementById('bulkDeleteForm').submit();
                }
            });
        }

        function confirmSingleDelete(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: 'text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20 ltr:mr-1 rtl:ml-1',
                    cancelButton: 'text-white bg-red-500 border-red-500 btn hover:text-white hover:bg-red-600 hover:border-red-600 focus:text-white focus:bg-red-600 focus:border-red-600 focus:ring focus:ring-red-100 active:text-white active:bg-red-600 active:border-red-600 active:ring active:ring-red-100 dark:ring-custom-400/20',
                },
                confirmButtonText: "Yes, delete it!",
                buttonsStyling: false,
                showCloseButton: true
            }).then(function(result) {
                if (result.value) {
                    fetch("{{ route('categories.delete', ['id' => '__id__']) }}".replace('__id__', id), {
                            method: "DELETE",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            }
                        })

                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Your file has been deleted.',
                                    icon: 'success',
                                    customClass: {
                                        confirmButton: 'text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20',
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    // Redirect to state.index after successful deletion
                                    window.location.href = '{{ route('categories.index') }}';
                                    // location.reload();
                                });
                                // location.reload());
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'There was an issue deleting the state.',
                                    icon: 'error',
                                    customClass: {
                                        confirmButton: 'text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20',
                                    },
                                    buttonsStyling: false
                                });
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            });
        }
    </script>

    <script>
        function updateStatus(status, categoryId) {
            //$("p").toggle();
            $('#loading').css("display", "block");
            $.ajax({
                type: 'POST',
                url: "{{ route('categories.updateStatus') }}",
                data: {
                    status: status,
                    categoryId: categoryId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#loading').css("display", "none");

                    if (response == 1) {
                        $('#loading').css("display", "none");
                        $("#msgdata").html("<strong>Success !</strong> Company Created Successfully.");
                        window.location.href = "";
                        return true;
                    } else {
                        $('#loading').css("display", "none");
                        return false;
                    }
                }
            });
        }
    </script>


    <script>
        function validateFile() {
            var allowedExtension = ['jpeg', 'jpg', 'png', 'webp', ""];
            var fileExtension = document.getElementById('Categoryimage').value.split('.').pop().toLowerCase();
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

    <script>
        function EditvalidateFile() {
            //alert('hello');
            var allowedExtension = ['jpeg', 'jpg', 'png', 'webp', ""];
            var fileExtension = document.getElementById('EditCategoryimage').value.split('.').pop().toLowerCase();
            var isValidFile = false;
            var image = document.getElementById('EditCategoryimage').value;
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

@endsection
