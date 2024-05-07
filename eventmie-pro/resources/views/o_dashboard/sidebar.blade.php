<div id="db-wrapper-two">
    <nav class="navbar-vertical-compact shadow-sm mt-9">
        <div data-simplebar style="" class="vh-100 mt-5">
            <ul class="navbar-nav flex-column" id="sideNavbar">

                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.o_dashboard' ? 'active' : '' }}"
                        href="{{ route('eventmie.o_dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                        title="@lang('eventmie-pro::em.dashboard')">
                        <span class="nav-icon"><i class="fas fa-gauge"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.dashboard')</span>
                    </a>
                </li>

                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.myevents_index' || Route::currentRouteName() == 'eventmie.myevents_form' ? 'active' : '' }}"
                        href="{{ route('eventmie.myevents_index') }}" title="@lang('eventmie-pro::em.myevents')">
                        <span class="nav-icon"><i class="far fa-calendar-alt"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.myevents')</span>
                    </a>
                </li>

                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.ticket_scan' ? 'active' : '' }}"
                        href="{{ route('eventmie.ticket_scan') }}" title="@lang('eventmie-pro::em.scan_ticket')">
                        <span class="nav-icon"><i class="fas fa-qrcode"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.scan_ticket')</span>
                    </a>
                </li>


                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.obookings_index' ? 'active' : '' }}"
                        href="{{ route('eventmie.obookings_index') }}" title="@lang('eventmie-pro::em.mybookings')">
                        <span class="nav-icon"><i class="fas fa-money-check-alt"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.mybookings')</span>
                    </a>
                </li>

                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.event_earning_index' ? 'active' : '' }}"
                        href="{{ route('eventmie.event_earning_index') }}" title="@lang('eventmie-pro::em.myearning')">
                        <span class="nav-icon"><i class="fas fa-wallet"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.myearning')</span>
                    </a>
                </li>

                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.tags_form' ? 'active' : '' }}"
                        href="{{ route('eventmie.tags_form') }}" title="@lang('eventmie-pro::em.mytags')">
                        <span class="nav-icon"><i class="fas fa-user-tag"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.mytags')</span>
                    </a>
                </li>

                <li class="nav-item tooltip-custom">
                    <a class="nav-link {{ Route::currentRouteName() == 'eventmie.myvenues.index' ? 'active' : '' }}"
                        href="{{ route('eventmie.myvenues.index') }}" title="@lang('eventmie-pro::em.myvenues')">
                        <span class="nav-icon"><i class="fas fa-map-location"></i></span>
                        <span class="tooltiptext">@lang('eventmie-pro::em.myvenues')</span>
                    </a>
                </li>

            </ul>
        </div>
    </nav>
</div>
