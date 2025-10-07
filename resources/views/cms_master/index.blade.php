@extends('layouts.app')

@section('title', 'CMS List')

@section('content')

    {!! Toastr::message() !!}

    <!-- Page-content -->
    <div
        class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">CMS List</h5>
                </div>
                <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                    <li
                        class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                        <a href="#!" class="text-slate-400 dark:text-zink-200">Master Entry</a>
                    </li>
                    <li class="text-slate-700 dark:text-zink-100">
                        CMS List
                    </li>
                </ul>
            </div>


            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">

                <div class="xl:col-span-12">
                    <div class="card" id="customerList">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-5 mb-5 xl:grid-cols-2">

                                <div class="ltr:md:text-end rtl:md:text-start">

                                </div>
                            </div>

                            <div class="overflow-x-auto">

                                @if (!$CMSMaster->isEmpty())


                                    <table class="w-full whitespace-nowrap" id="customerTable">
                                        <thead class="bg-slate-100 dark:bg-zink-600">
                                            <tr>

                                                <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                    data-sort="state_name">Sr.no </th>

                                                <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                    data-sort="state_name">Title </th>

                                                <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                    data-sort="state_name"> Description </th>

                                                <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                    data-sort="action">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all">
                                            <?php $i = 1; ?>
                                            @foreach ($CMSMaster as $cms)
                                                <tr class="text-center">

                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                        {{ $i + $CMSMaster->perPage() * ($CMSMaster->currentPage() - 1) }}
                                                    </td>

                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                        {!! Str::limit($cms->strTitle, 50, '....') !!}
                                                    </td>

                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                        @if ($cms->strDescription)
                                                            {!! Str::limit($cms->strDescription, 50, '....') !!}
                                                        @else
                                                            {{ '-' }}
                                                        @endif
                                                    </td>


                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                        <div class=" gap-2">
                                                            <div class="edit">

                                                                <a class="mx-1" title="Edit"
                                                                    href="{{ route('cms.edit', $cms->id) }}">
                                                                    <i class="ri-edit-2-fill"></i>
                                                                </a>

                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php $i++; ?>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="flex items-center justify-between mt-5">
                                        {!! $CMSMaster->links() !!}
                                    </div>
                                @else
                                    <div class="noresult" style="display: none">
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
