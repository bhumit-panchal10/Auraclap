@extends('layouts.appnew')

@section('title', 'User Detail')
@section('content')

    {!! Toastr::message() !!}
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">

    <!-- Page-content -->

    <style>
        .bluetxt {
            color: #0393be !important;
        }
    </style>

    <div class="xl:col-span-12 m-4">
        <div class="container-fluid group-data-[content=boxed]:max-w-boxed mx-auto">

            <div class="flex flex-col gap-2 py-4 md:flex-row md:items-center print:hidden">
                <div class="grow">
                    <h5 class="text-16">User Detail</h5>
                </div>

            </div>


            <div class="grid grid-cols-1 gap-x-5 xl:grid-cols-12">

                <div class="xl:col-span-12">
                    <div class="card" id="customerList">
                        <div class="card-body">
                            <div class="grid grid-cols-1 gap-5 mb-5 xl:grid-cols-0">

                            </div>



                            <div class="overflow-x-auto">
                                @if (!empty($user))
                                    <form id="bulkDeleteForm" method="POST"
                                        action="{{ route('manage_rate.deleteselected') }}">
                                        @csrf
                                        @method('DELETE')

                                        <table class="w-full whitespace-nowrap" id="customerTable">
                                            <thead class="bg-slate-100 dark:bg-zink-600">
                                                <tr>



                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="are_name">Username</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Name</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="state_name">Category</th>

                                                    <th class="sort px-3.5 py-2.5 font-semibold border-b border-slate-200 dark:border-zink-500 ltr:text-left rtl:text-right"
                                                        data-sort="action">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="list form-check-all">
                                                <?php $i = 1; ?>

                                                <tr>

                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                        {{ $user['login_id'] }}</td>


                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                        {{ $user['name'] }}</td>

                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">
                                                        {{ $user['cate_id'] }}</td>


                                                    <td
                                                        class="px-3.5 py-2.5 border-y border-slate-200 dark:border-zink-500 customer_name">

                                                        @if (!empty($examres) && !empty($examres->Exam_user_id) && !empty($examres->Exam_id))
                                                            <a href="{{ route('exam.results', ['exam_id' => $user['exam_id'], 'Exam_user_id' => $examres->Exam_user_id]) }}"
                                                                class="btn btn-primary">
                                                                View Result
                                                            </a>
                                                        @else
                                                            <a href="{{ route('exam.question', $user['exam_id']) }}"
                                                                class="btn btn-primary">
                                                                Start Exam
                                                            </a>
                                                        @endif
                                                    </td>


                                                </tr>
                                                <?php $i++; ?>

                                            </tbody>
                                        </table>
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


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>

@endsection
@section('script')
@endsection
