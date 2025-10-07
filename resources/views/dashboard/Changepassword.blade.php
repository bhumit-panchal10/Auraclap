@extends('layouts.app')
@section('content')

    {!! Toastr::message() !!}
    <?php
    $session = Auth::user()->id;
    ?>
    <script type="text/javascript" src="//js.nicedit.com/nicEdit-latest.js"></script>
    <div class="relative min-h-screen group-data-[sidebar-size=sm]:min-h-sm">

        <div
            class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
            <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

                <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                    <div class="grow">
                        <h5 class="text-16">Change password</h5>
                    </div>
                    <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                        <li
                            class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                            <a href="{{ route('home') }}" class="text-slate-400 dark:text-zink-200">Home</a>
                        </li>
                        <li class="text-slate-700 dark:text-zink-100">
                            Change password
                        </li>
                    </ul>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-x-5">
                    <div class="xl:col-span-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-4 text-15 grow"></h6>
                                <form action="{{ route('Change_password') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $session }}">
                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-12">
                                        <div class="xl:col-span-4">
                                            <div>
                                                <label for="oldPassword"
                                                    class="inline-block mb-2 text-base font-medium"><span
                                                        style="color:red;">*</span> Old Password</label>
                                                <input type="password" id="oldPassword" name="oldPassword"
                                                    placeholder="Enter current password"
                                                    class="block w-full px-3 py-2 border rounded-md" value=""
                                                    required>
                                            </div>
                                        </div>
                                        <div class="xl:col-span-4">
                                            <div>
                                                <label for="newPassword"
                                                    class="inline-block mb-2 text-base font-medium"><span
                                                        style="color:red;">*</span> New Password</label>
                                                <input type="password" id="newPassword" placeholder="Enter new password"
                                                    name="newPassword" class="block w-full px-3 py-2 border rounded-md"
                                                    value="" required>
                                            </div>
                                        </div>
                                        <div class="xl:col-span-4">
                                            <div>
                                                <label for="confirmPassword"
                                                    class="inline-block mb-2 text-base font-medium"><span
                                                        style="color:red;">*</span> Confirm Password</label>
                                                <input type="password" id="confirmPassword"
                                                    placeholder="Enter Confirm password" name="confirmPassword"
                                                    class="block w-full px-3 py-2 border rounded-md" value=""
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end gap-2 mt-4">
                                        <button type="submit"
                                            class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Submit</button>
                                        <button type="reset"
                                            class="text-white btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Reset</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        <footer
            class="ltr:md:left-vertical-menu rtl:md:right-vertical-menu group-data-[sidebar-size=md]:ltr:md:left-vertical-menu-md group-data-[sidebar-size=md]:rtl:md:right-vertical-menu-md group-data-[sidebar-size=sm]:ltr:md:left-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:md:right-vertical-menu-sm absolute right-0 bottom-0 px-4 h-14 group-data-[layout=horizontal]:ltr:left-0  group-data-[layout=horizontal]:rtl:right-0 left-0 border-t py-3 flex items-center dark:border-zink-600">
            <div class="group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl w-full">
                <div
                    class="grid items-center grid-cols-1 text-center lg:grid-cols-2 text-slate-400 dark:text-zink-200 ltr:lg:text-left rtl:lg:text-right">
                    <div>
                        <script>
                            document.write(new Date().getFullYear())
                        </script> StarCode Kh
                    </div>
                    <div class="hidden lg:block">
                        <div class="ltr:text-right rtl:text-left">
                            Design & Develop by StarCode Kh
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
@endsection

@section('scripts')
    {{--  <script src="{{ asset('vendors/choices/choices.min.js') }}"></script>  --}}
    <script type="text/javascript">
        bkLib.onDomLoaded(function() {
            nicEditors.allTextAreas()
        });
    </script>
@endsection
