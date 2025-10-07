@extends('layouts.app')

@section('title', 'Roles List')
@section('content')

{!! Toastr::message() !!}

<!-- Page-content -->
<div
    class="group-data-[sidebar-size=lg]:ltr:md:ml-vertical-menu group-data-[sidebar-size=lg]:rtl:md:mr-vertical-menu group-data-[sidebar-size=md]:ltr:ml-vertical-menu-md group-data-[sidebar-size=md]:rtl:mr-vertical-menu-md group-data-[sidebar-size=sm]:ltr:ml-vertical-menu-sm group-data-[sidebar-size=sm]:rtl:mr-vertical-menu-sm pt-[calc(theme('spacing.header')_*_1)] pb-[calc(theme('spacing.header')_*_0.8)] px-4 group-data-[navbar=bordered]:pt-[calc(theme('spacing.header')_*_1.3)] group-data-[navbar=hidden]:pt-0 group-data-[layout=horizontal]:mx-auto group-data-[layout=horizontal]:max-w-screen-2xl group-data-[layout=horizontal]:px-0 group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:ltr:md:ml-auto group-data-[layout=horizontal]:group-data-[sidebar-size=lg]:rtl:md:mr-auto group-data-[layout=horizontal]:md:pt-[calc(theme('spacing.header')_*_1.6)] group-data-[layout=horizontal]:px-3 group-data-[layout=horizontal]:group-data-[navbar=hidden]:pt-[calc(theme('spacing.header')_*_0.9)]">
    <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

        <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
            <div class="grow">
                <h5 class="text-16">Roles List</h5>
            </div>
            <ul class="flex items-center gap-2 text-sm font-normal shrink-0">
                <li
                    class="relative before:content-['\ea54'] before:font-remix ltr:before:-right-1 rtl:before:-left-1  before:absolute before:text-[18px] before:-top-[3px] ltr:pr-4 rtl:pl-4 before:text-slate-400 dark:text-zink-200">
                    <a href="#!" class="text-slate-400 dark:text-zink-200">Employee</a>
                </li>
                <li class="text-slate-700 dark:text-zink-100">
                    Role
                </li>
            </ul>
        </div>


        <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">
            <div class="xl:col-span-5">
                <div class="card" id="customerList">
                    <div class="">
                        <div class="grid grid-cols-1 gap-5 mb-5">

                            <div class="rtl:md:text-start">
                                <div class="bg-white shadow rounded-md dark:bg-zink-600">
                                    <div
                                        class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                                        <h5 class="text-16" id="exampleModalLabel">Add Role</h5>
                                    </div>
                                    <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                                        <form method="POST" action="{{ route('role.store') }}" id="registerForm">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="email-field" class="inline-block mb-2 text-base font-medium">Role Name <span class="text-red-500">*</span> </label>
                                                <input type="text" id="name" name="name"
                                                    class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                                    placeholder="Enter Role Name" required autocomplete="off" autofocus
                                                    value="{{ old('name') }}">
                                            </div>

                                            <div class="mt-10">
                                                <button type="submit" title="Submit"
                                                    class=" text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Submit</button>
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
            <div class="xl:col-span-6">
                <div class="card" id="customerList">
                    <div class="card-body">
                        <div class="grid grid-cols-1 gap-5 mb-5 xl:grid-cols-2">

                            <div class="rtl:md:text-start">
                                @if (!$Roles->isEmpty())
                                <button type="button" onclick="confirmBulkDelete()" title="Delete Selected"
                                    class=" text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
                                    Delete Selected
                                </button>
                                @endif
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            @if (!$Roles->isEmpty())
                            <form id="bulkDeleteForm" method="POST" action="{{ route('role.deleteselected') }}">
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
                                                data-sort="name">Role Name </th>

                                            <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 "
                                                data-sort="action">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        <?php $i = 1; ?>
                                        @foreach ($Roles as $role)
                                        <tr class="text-center">
                                            <th class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500"
                                                scope="row">
                                                <input
                                                    class="border rounded-sm appearance-none cursor-pointer size-4 bg-slate-100 border-slate-200 dark:bg-zink-600 dark:border-zink-500 checked:bg-custom-500 checked:border-custom-500 dark:checked:bg-custom-500 dark:checked:border-custom-500 checked:disabled:bg-custom-400 checked:disabled:border-custom-400"
                                                    type="checkbox" name="role_ids[]"
                                                    value="{{ $role->id }}">
                                            </th>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 id">
                                                {{ $i + $Roles->perPage() * ($Roles->currentPage() - 1) }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                {{ $role->name }}
                                            </td>

                                            <td
                                                class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500">
                                                <div class="gap-2">
                                                    <div class="edit">
                                                        <a class="mx-1" title="Edit" href="#"
                                                            data-modal-target="EditModal"
                                                            onclick="getEditData(<?= $role->id ?>)">
                                                            <i class="ri-edit-2-fill"></i>
                                                        </a>

                                                        <a class="mx-1" title="Delete" href="#"
                                                            onclick="confirmSingleDelete({{ $role->id }})">
                                                            <i class="ri-delete-bin-5-fill"></i>
                                                        </a>

                                                        <a class="mx-1" title="Add Permission"
                                                            href="{{ route('role.role_Permission', $role->id) }}">
                                                            <i class="ri-add-fill"></i>
                                                        </a>

                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php $i++; ?>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="flex items-center justify-between">
                                    {!! $Roles->links() !!}
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

        <div id="EditModal" modal-center=""
            class="fixed flex flex-col hidden transition-all duration-300 ease-in-out left-2/4 z-drawer -translate-x-2/4 -translate-y-2/4 show">
            <div class="w-screen md:w-[30rem] bg-white shadow rounded-md dark:bg-zink-600">
                <div class="flex items-center justify-between p-4 border-b border-slate-200 dark:border-zink-500">
                    <h5 class="text-16" id="exampleModalLabel">Edit Role</h5>
                    <button data-modal-close="EditModal"
                        class="transition-all duration-200 ease-linear text-slate-400 hover:text-slate-500"><i
                            data-lucide="x" class="size-5"></i></button>
                </div>
                <div class="max-h-[calc(theme('height.screen')_-_180px)] overflow-y-auto p-4">
                    <form class="tablelist-form" action="{{ route('role.update') }}" method="POST">
                        @csrf
                        <input type="hidden" id="roleid" name="roleid" />

                        <div class="mb-3">
                            <label for="email-field" class="inline-block mb-2 text-base font-medium">Role Name<span class="text-red-500">*</span> </label>

                            <input type="text" name="name" id="Editname"
                                class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                                placeholder="Enter Role Name" required autocomplete="off"
                                value="{{ old('name') }}">
                        </div>


                        <div class="flex justify-end gap-2">

                            <button type="submit" 
                                class="text-white bg-custom-500 border-custom-500 btn hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/10"
                                id="add-btn">Submit</button>
                            <a href="{{ route('role.index') }}">
                                <button type="button" class="text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">
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
        var url = "{{ route('role.edit', ':id') }}";
        url = url.replace(":id", id);
        if (id) {
            $.ajax({
                url: url,
                type: 'GET',
                data: {
                    id,
                    id
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $("#Editname").val(obj.name);
                    $('#roleid').val(id);
                },
                error: function(xhr) {
                    alert('Failed to load files');
                }
            });
        }
    }
</script>

<script>
    function checkAll(source) {
        checkboxes = document.querySelectorAll('input[name="role_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = source.checked);
    }

    function confirmBulkDelete() {
        const selected = document.querySelectorAll('input[name="role_ids[]"]:checked');
        const ids = Array.from(selected).map(checkbox => checkbox.value);

        if (ids.length === 0) {
            Swal.fire({
                title: 'No Selection',
                text: 'Please select at least one role to delete.',
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
                fetch(`{{ route('role.delete', ['id' => '__id__']) }}`.replace('__id__', id), {

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
                                // }).then(() => location.reload());
                            }).then(() => {
                                // Redirect to state.index after successful deletion
                                window.location.href = `{{ route('role.index') }}`;
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