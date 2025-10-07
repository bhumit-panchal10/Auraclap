@extends('auth.layout.app')
@section('content')
    <div class="mb-0 w-screen lg:w-[500px] card shadow-lg border-none shadow-slate-100 relative">
        <div class="!px-10 !py-12 card-body">
            <a href="#!">
                <img src="{{ asset('assets/images/logo.png') }}" alt="" class="hidden h-6 mx-auto dark:block">
                <img src="{{ asset('assets/images/logo.png') }}" alt="" class="block h-6 mx-auto dark:hidden">
            </a>

            <div class="mt-8 text-center">
                <h4 class="mb-1 text-custom-500 dark:text-custom-500">Join As Technical</h4>
            </div>

            <form action="{{ route('registerstore') }}" class="mt-10" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="username-field" class="inline-block mb-2 text-base font-medium">Name <span
                            class="text-red-500"> *</span></label>
                    <input type="text" name="name" id="username-field"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Enter name" required>
                </div>
                <div class="mb-3">
                    <label for="email-field" class="inline-block mb-2 text-base font-medium">Email <span
                            class="text-red-500"> *</span></label>
                    <input type="text" name="email" id="email-field"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Enter email" required>
                </div>

                <div class="mb-3">
                    <label for="email-field" class="inline-block mb-2 text-base font-medium">Mobile No <span
                            class="text-red-500"> *</span></label>
                    <input type="text" name="mobile_no" id="email-field"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Enter Mobile No" maxlength="10" required>
                </div>

                <div class="mb-3">
                    <label for="email-field" class="inline-block mb-2 text-base font-medium">State<span
                            class="text-red-500"> *</span> </label>
                    <select id="stateid" name="stateid"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        required>
                        <option value="" disabled selected>Select a State
                        </option>
                        @foreach ($states as $state)
                            <option value="{{ $state->stateId }}">
                                {{ $state->stateName }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="email-field" class="inline-block mb-2 text-base font-medium">City <span
                            class="text-red-500"> *</span></label>
                    <input type="text" name="city" id="email-field"
                        class="form-input border-slate-200 dark:border-zink-500 focus:outline-none focus:border-custom-500 disabled:bg-slate-100 dark:disabled:bg-zink-600 disabled:border-slate-300 dark:disabled:border-zink-500 dark:disabled:text-zink-200 disabled:text-slate-500 dark:text-zink-100 dark:bg-zink-700 dark:focus:border-custom-800 placeholder:text-slate-400 dark:placeholder:text-zink-200"
                        placeholder="Enter City" required>
                </div>


                <div class="mt-10">
                    <button type="submit"
                        class="w-full text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Sign
                        In</button>
                </div>

            </form>
        </div>
    </div>

@section('script')
@endsection
@endsection
