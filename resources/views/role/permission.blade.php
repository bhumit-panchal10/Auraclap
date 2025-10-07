@extends('layouts.app')

@section('title', 'Role Permission')

@section('content')

    {!! Toastr::message() !!}
    <div class="relative min-h-screen group-data-[sidebar-size=sm]:min-h-sm">
        <div
            class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
            <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">
                <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                    <div class="grow">
                        {{--  <h5 class="text-16">Add Permission</h5>  --}}
                    </div>
                    <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                        <li
                            class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                            <a href="#!" class="text-slate-400 dark:text-zink-200">Employee</a>
                        </li>
                        <li
                            class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                            <a href="{{ route('role.index') }}" class="text-slate-400 dark:text-zink-200">Role</a>
                        </li>
                        <li class="text-slate-700 dark:text-zink-100">
                            Add Permission
                        </li>
                    </ul>
                </div>
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-x-5">
                    <div class="xl:col-span-12">
                        <div class="card" id="customerList">
                            <div class="">
                                <div class="grid grid-cols-1 gap-5 mb-5 ">
                                    <div class="rtl:md:text-start">
                                        <div class="bg-white shadow rounded-md dark:bg-zink-600">
                                            <div
                                                class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                                                <h5 class="text-16" id="exampleModalLabel">Add Permission</h5>
                                                <a href="{{ route('role.index') }}">
                                                    <button type="button"
                                                        class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20"
                                                        data-modal-target="AddModal">
                                                        <i class="ri-arrow-left-line"></i> Back
                                                    </button>
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <h6 class="mb-4 text-15 grow"></h6>
                                                <form action="{{ route('role.role_Permission_store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="role_id" value="{{ $id }}">

                                                    <h2 class="mb-4 border-b-2 border-gray-400 pb-2">
                                                        {{ __('Master Entry') }}
                                                    </h2>
                                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                                                        <div>
                                                            <label for="MasterEntry">{{ __('Master Entry') }}</label><br>
                                                            <select id="MasterEntry" name="MasterEntry"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                                autofocus>
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->MasterEntry == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->MasterEntry == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label for="States">{{ __('States') }}</label><br>
                                                            <select id="States" name="States"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->States == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->States == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="City">{{ __('City') }}</label><br>
                                                            <select id="City" name="City"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->City == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->City == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label for="Price">{{ __('Price') }}</label><br>
                                                            <select id="Price" name="Price"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Price == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Price == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Area">{{ __('Area') }}</label><br>
                                                            <select id="Area" name="Area"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Area == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Area == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Career">{{ __('Career') }}</label><br>
                                                            <select id="Career" name="Career"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Career == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Career == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Testimonial">{{ __('Testimonial') }}</label><br>
                                                            <select id="Testimonial" name="Testimonial"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Testimonial == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Testimonial == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Faq">{{ __('Faq') }}</label><br>
                                                            <select id="Faq" name="Faq"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Faq == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Faq == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label
                                                                for="News_and_Updates">{{ __('News & Updates') }}</label><br>
                                                            <select id="News_and_Updates" name="News_and_Updates"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->News_and_Updates == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->News_and_Updates == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Tags">{{ __('Tags') }}</label><br>
                                                            <select id="Tags" name="Tags"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Tags == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Tags == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Vehicle">{{ __('Vehicle') }}</label><br>
                                                            <select id="Vehicle" name="Vehicle"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Vehicle == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Vehicle == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Cms">{{ __('Cms') }}</label><br>
                                                            <select id="Cms" name="Cms"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Cms == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Cms == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="Goods_Type">{{ __('Goods Type') }}</label><br>
                                                            <select id="Goods_Type" name="Goods_Type"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Goods_Type == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Goods_Type == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="Our_Team">{{ __('Our Team') }}</label><br>
                                                            <select id="Our_Team" name="Our_Team"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Our_Team == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Our_Team == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="Offer">{{ __('Offer') }}</label><br>
                                                            <select id="Offer" name="Offer"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Offer == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Offer == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                    </div>

                                                    <h2 class="mb-4 border-b-2 border-gray-400 pb-2">{{ __('Driver') }}
                                                    </h2>
                                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                                                        <div>
                                                            <label for="Driver_Request">{{ __('Driver Request') }}</label><br>
                                                            <select id="Driver_Request" name="Driver_Request"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Driver_Request == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Driver_Request == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="Driver_List">{{ __('Driver List') }}</label><br>
                                                            <select id="Driver_List" name="Driver_List"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Driver_List == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Driver_List == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="Driver_Location">{{ __('Driver Location') }}</label><br>
                                                            <select id="Driver_Location" name="Driver_Location"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Driver_Location == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Driver_Location == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div>
                                                            <label for="Driver_Pass">{{ __('Pass') }}</label><br>
                                                            <select id="Driver_Pass" name="Driver_Pass"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Driver_Pass == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Driver_Pass == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Seo">{{ __('Seo') }}</label><br>
                                                            <select id="Seo" name="Seo"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Seo == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Seo == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Customer">{{ __('Customer') }}</label><br>
                                                            <select id="Customer" name="Customer"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Customer == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Customer == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>

                                                    </div>

                                                    <h2 class="mb-4 border-b-2 border-gray-400 pb-2">{{ __('Employee') }}
                                                    </h2>
                                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                                                        <div>
                                                            <label
                                                                for="Employee_List">{{ __('Employee List') }}</label><br>
                                                            <select id="Employee_List" name="Employee_List"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Employee_List == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Employee_List == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Role">{{ __('Role') }}</label><br>
                                                            <select id="Role" name="Role"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Role == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Role == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>

                                                    </div>
                                                    
                                                    <h2 class="mb-4 border-b-2 border-gray-400 pb-2">{{ __('Inquiry') }}
                                                    </h2>
                                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
                                                        <div>
                                                            <label
                                                                for="Career_Inquiry">{{ __('Career Inquiry') }}</label><br>
                                                            <select id="Career_Inquiry" name="Career_Inquiry"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Career_Inquiry == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Career_Inquiry == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="Contact_Inquiry">{{ __('Contact Inquiry') }}</label><br>
                                                            <select id="Contact_Inquiry" name="Contact_Inquiry"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->Contact_Inquiry == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->Contact_Inquiry == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label for="News_Letter_Inquiry">{{ __('News Letter Inquiry') }}</label><br>
                                                            <select id="News_Letter_Inquiry" name="News_Letter_Inquiry"
                                                                class="form-select border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200">
                                                                <option value="1"
                                                                    {{ isset($permission) && $permission->News_Letter_Inquiry == 1 ? 'selected' : '' }}>
                                                                    {{ __('Yes') }}
                                                                </option>
                                                                <option value="0"
                                                                    {{ isset($permission) && $permission->News_Letter_Inquiry == 0 ? 'selected' : '' }}>
                                                                    {{ __('No') }}</option>
                                                            </select>
                                                        </div>

                                                    </div>

                                                    <div class="xl:col-span-12">
                                                        <div class="flex justify-end gap-2 mt-4">
                                                            <button type="submit"
                                                                class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                                Submit
                                                            </button>
                                                            <button type="reset"
                                                                class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                                                Reset
                                                            </button>
                                                        </div>
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
            </div>
        </div>
    </div>
@endsection
