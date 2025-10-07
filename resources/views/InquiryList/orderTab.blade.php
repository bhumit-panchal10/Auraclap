<ul class="flex flex-wrap w-full text-sm font-medium text-center nav-tabs space-x-2">

    <li class="group grow @if (request()->routeIs('pendingorderlist')) active @endif">
        <a href="{{ route('pendingorderlist') }}"
            class="relative inline-block px-4 w-full py-2 text-base transition-all duration-300 ease-linear rounded-md text-slate-500 border border-transparent group-[.active]:bg-custom-500 group-[.active]:text-white hover:text-custom-500 active:text-custom-500">
            Pendign Order List
        </a>
    </li>

    <li class="group grow @if (request()->routeIs('ongoingorderlist')) active @endif">
        <a href="{{ route('ongoingorderlist') }}"
            class="relative inline-block px-4 w-full py-2 text-base transition-all duration-300 ease-linear rounded-md text-slate-500 border border-transparent group-[.active]:bg-custom-500 group-[.active]:text-white hover:text-custom-500 active:text-custom-500">
            Ongoing Order List
        </a>
    </li>


    <li class="group grow @if (request()->routeIs('completeorderlist')) active @endif">

        <a href="{{ route('completeorderlist') }}"
            class="relative inline-block px-4 w-full py-2 text-base transition-all duration-300 ease-linear rounded-md text-slate-500 border border-transparent group-[.active]:bg-custom-500 group-[.active]:text-white hover:text-custom-500 active:text-custom-500">
            Complete Order List
        </a>
    </li>
    
     <li class="group grow @if (request()->routeIs('cancelorderlist')) active @endif">

        <a href="{{ route('cancelorderlist') }}"
            class="relative inline-block px-4 w-full py-2 text-base transition-all duration-300 ease-linear rounded-md text-slate-500 border border-transparent group-[.active]:bg-custom-500 group-[.active]:text-white hover:text-custom-500 active:text-custom-500">
            Cancel Order List
        </a>
    </li>

</ul>
