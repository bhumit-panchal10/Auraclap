@extends('layouts.app')

@section('title', 'Contact Inquiry List')
@section('content')

{!! Toastr::message() !!}

<!-- Page-content -->
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Contact Inquiry List</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Inquiry</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Contact Inquiry 
                </li>
                
            </ul>
        </div>


        <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">

            <div class="xl:col-span-12">
                <div class="card" id="customerList">
                    <div class="card-body">
                        <div class="grid grid-cols-1 gap-5 mb-5 xl:grid-cols-0">

                            <div class="rtl:md:text-start">
                                @if (!$contactlist->isEmpty())
                                <button type="button" onclick="confirmBulkDelete()"
                                    class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                    Delete Selected
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="overflow-x-auto">



                            @if (!$contactlist->isEmpty())
                            <form id="bulkDeleteForm" method="POST" action="{{ route('contact_inquiry.deleteselected') }}">
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
                                                data-sort="state_name">First Name </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name"> Last Name </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name"> Email </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="state_name"> Phone Number </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        <?php $i = 1; ?>
                                        @foreach ($contactlist as $list)
                                        <tr class="text-center">
                                            <th class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500"
                                                scope="row">
                                                <input
                                                    class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500 checked:disabled:bg-custom-400 checked:disabled:border-custom-400"
                                                    type="checkbox" name="contact_ids[]"
                                                    value="{{ $list->id }}">
                                            </th>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                {{ $i + $contactlist->perPage() * ($contactlist->currentPage() - 1) }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_title">
                                                {{ $list->firstName }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_type">
                                                {{ $list->lastName ?? '-' }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_location">
                                                {{ $list->email ?? '-' }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 job_location">
                                                {{ $list->phone_number ?? '-' }}
                                            </td>


                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                <div class="gap-2">
                                                    <div class="edit">

                                                        <a class="mx-1" title="View Details"
                                                            href="{{ route('contact_inquiry.inquiry_view', $list->id) }}">
                                                            <i class="ri-eye-fill"></i>
                                                        </a>

                                                        <a class="mx-1" title="Delete" href="#"
                                                            onclick="confirmSingleDelete({{ $list->id }})">
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
                                <div class="flex items-center justify-between mt-5">
                                    {!! $contactlist->links() !!}
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

<script>
    function checkAll(source) {
        checkboxes = document.querySelectorAll('input[name="contact_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = source.checked);
    }

    function confirmBulkDelete() {
        const selected = document.querySelectorAll('input[name="contact_ids[]"]:checked');
        const ids = Array.from(selected).map(checkbox => checkbox.value);

        if (ids.length === 0) {
            Swal.fire({
                title: 'No Selection',
                text: 'Please select at least one career inquiry to delete.',
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
                fetch(`{{ route('contact_inquiry.delete', ['id' => '__id__']) }}`.replace('__id__', id), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                                //}).then(() => location.reload());
                            }).then(() => {
                                // Redirect to state.index after successful deletion
                                window.location.href = `{{ route('contact_inquiry.index') }}`;
                            });
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
@endsection