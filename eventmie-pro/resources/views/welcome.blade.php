@extends('eventmie::layouts.app')

@section('title') @lang('eventmie-pro::em.home') @endsection

@section('content')
    @php
        $perPage = 3;
    @endphp
    <!--Banner slider start-->
    <section>
        <div class="col-sm-12">
            @component('eventmie::skeleton.banner') @endcomponent
            @guest
                <banner-slider :banners="{{ json_encode($banners, JSON_HEX_APOS) }}" :is_logged="{{ 0 }}"
                    :is_customer="{{ 0 }}" :is_organiser="{{ 0 }}" :is_admin="{{ 0 }}"
                    :is_multi_vendor="{{ setting('multi-vendor.multi_vendor') ? 1 : 0 }}"
                    :demo_mode="{{ config('voyager.demo_mode') }}"
                    :check_session="{{ json_encode(session('verify'), JSON_HEX_TAG) }}"
                    :s_host="{{ json_encode($_SERVER['REMOTE_ADDR'], JSON_HEX_TAG) }}"></banner-slider>
            @else
                <banner-slider :banners="{{ json_encode($banners, JSON_HEX_APOS) }}" :is_logged="{{ 1 }}"
                    :is_customer="{{ Auth::user()->hasRole('customer') ? 1 : 0 }}"
                    :is_organiser="{{ Auth::user()->hasRole('organiser') ? 1 : 0 }}"
                    :is_admin="{{ Auth::user()->hasRole('admin') ? 1 : 0 }}"
                    :is_multi_vendor="{{ setting('multi-vendor.multi_vendor') ? 1 : 0 }}"
                    :demo_mode="{{ config('voyager.demo_mode') }}"
                    :check_session="{{ json_encode(session('verify'), JSON_HEX_TAG) }}"
                    :s_host="{{ json_encode($_SERVER['REMOTE_ADDR'], JSON_HEX_TAG) }}"></banner-slider>
            @endguest
        </div>
    </section>
    <!--Banner slider end-->

    {{-- Filter --}}
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 p-2 search-form rounded-md-pill smooth-shadow-md mt-n5">
                    <form type="GET" action="{{ route('eventmie.events_index') }}">
                        <div class="row align-items-center">
                            <div class="col-sm">
                                <div class="form-floating">
                                    <select
                                        class="form-select border-bottom border-bottom-md-0 rounded-0 border-0 form-focus-none bg-transparent"
                                        id="city" name="city">
                                        <option value="All">@lang('eventmie-pro::em.all')</option>
                                        @foreach ($cities as $val)
                                            <option value="{{ $val['city'] }}">{{ $val['city'] }}, {{ $val['state'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="city_select">{{ __('eventmie-pro::em.city') }}</label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-floating">
                                    <select
                                        class="form-select  border-bottom border-bottom-md-0 rounded-0 border-0 form-focus-none bg-transparent"
                                        id="category" name="category">
                                        <option value="All">@lang('eventmie-pro::em.all')</option>
                                        @foreach ($categories as $val)
                                            <option value="{{ $val['name'] }}">{{ $val['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <label for="category">{{ __('eventmie-pro::em.category') }}</label>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-floating">
                                    <select class="form-select border-0 form-focus-none" id="price" name="price">
                                        <option value="">@lang('eventmie-pro::em.any_price')</option>
                                        <option value="free">@lang('eventmie-pro::em.free')</option>
                                        <option value="paid">@lang('eventmie-pro::em.paid')</option>
                                    </select>
                                    <label for="price">@lang('eventmie-pro::em.price')</label>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="d-md-flex justify-content-end ms-md-n3 ms-lg-0 text-end  d-none d-md-block">
                                    <button type="submit" class="btn btn-primary rounded-circle btn-icon btn-icon-lg">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                                <div class="d-md-flex justify-content-end ms-md-n3 ms-lg-0  d-block d-md-none">
                                    <button type="submit"
                                        class="btn btn-primary d-grid gap-2 col-12 mx-auto">@lang('eventmie-pro::em.search')</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- New Themes Event Featured Start -->
    @if ($featured_events->isNotEmpty())
        <div class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <h3>@lang('eventmie-pro::em.featured_events')</h3>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-sm text-primary mt-lg-2 {{ App::isLocale('ar') ? 'float-start' : 'float-end' }}"
                            href="{{ route('eventmie.events_index') }}">@lang('eventmie-pro::em.view_all') <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                @component('eventmie::skeleton.event') @endcomponent

                <event-listing :events="{{ json_encode($featured_events, JSON_HEX_APOS) }}"
                    :currency="{{ json_encode($currency, JSON_HEX_APOS) }}" :item_count="{{ 3 }}"
                    :date_format="{{ json_encode(
                        [
                            'vue_date_format' => format_js_date(),
                            'vue_time_format' => format_js_time(),
                        ],
                        JSON_HEX_APOS,
                    ) }}">
                </event-listing>

            </div>
        </div>
    @endif
    <!-- New Themes Event Featured End -->

    <!-- New Themes Top-selling Start-->
    @if ($top_selling_events->isNotEmpty())
        <div class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <h3>@lang('eventmie-pro::em.top_selling_events')</h3>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-sm text-primary mt-lg-2 {{ App::isLocale('ar') ? 'float-start' : 'float-end' }}"
                            href="{{ route('eventmie.events_index') }}">@lang('eventmie-pro::em.view_all') <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                @component('eventmie::skeleton.event') @endcomponent

                <event-listing :events="{{ json_encode($top_selling_events, JSON_HEX_APOS) }}"
                    :currency="{{ json_encode($currency, JSON_HEX_APOS) }}" :item_count="{{ 3 }}"
                    :date_format="{{ json_encode(
                        [
                            'vue_date_format' => format_js_date(),
                            'vue_time_format' => format_js_time(),
                        ],
                        JSON_HEX_APOS,
                    ) }}">
                </event-listing>

            </div>

        </div>
    @endif
    <!-- New Themes Top-selling END -->

    <!-- New Themes Event Upcoming Start-->
    @if ($upcomming_events->isNotEmpty())
        <div class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <h3>@lang('eventmie-pro::em.upcoming_events')</h3>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-sm text-primary mt-lg-2 {{ App::isLocale('ar') ? 'float-start' : 'float-end' }}"
                            href="{{ route('eventmie.events_index') }}">@lang('eventmie-pro::em.view_all') <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                @component('eventmie::skeleton.event') @endcomponent

                <event-listing :events="{{ json_encode($upcomming_events, JSON_HEX_APOS) }}"
                    :currency="{{ json_encode($currency, JSON_HEX_APOS) }}" :item_count="{{ 3 }}"
                    :date_format="{{ json_encode(
                        [
                            'vue_date_format' => format_js_date(),
                            'vue_time_format' => format_js_time(),
                        ],
                        JSON_HEX_APOS,
                    ) }}">
                </event-listing>
            </div>
        </div>
    @endif
    <!-- New Themes Event Upcoming END -->

    <!-- New Themes Categories START-->
    @if (!empty($categories))
        <div class="py-5 bg-gradient">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3 class="text-white">@lang('eventmie-pro::em.event_categories')</h3>
                    </div>
                </div>
                <div class="row">
                    @php $i = 0; @endphp
                    @foreach ($categories as $key => $item)
                        @php $i++; @endphp
                        <div class="d-flex align-items-lg-stretch col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12' }}">
                            <div class="card w-100 border-0 overlay-bg img-hover mb-3"
                                @php $bgimg =  asset('/storage/'.$item['thumb']); @endphp
                                style="background-image: url({{ $bgimg }}); background-size: cover;">

                                <span class="single-name"></span>
                                <div class="pt-4 ps-4 pb-18 z-1">
                                    <h3 class="text-white mb-0">{{ $item['name'] }}</h3>
                                </div>

                                <a class="stretched-link"
                                    href="{{ route('eventmie.events_index', ['category' => urlencode($item['name'])]) }}"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <!-- New Themes Categories END -->

    <!-- New Themes cities_events START-->
    @if (!empty($cities_events))
        <div class="py-5 bg-gradient">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h2 class="text-white">@lang('eventmie-pro::em.cities_events')</h2>
                    </div>
                </div>
                <div class="row">
                    @php $i = 0; @endphp
                    @foreach ($cities_events as $key => $item)
                        @php $i++; @endphp
                        <div class="d-flex align-items-lg-stretch col-12 mb-4 {{ $i % 3 ? 'col-md-4' : 'col-md-8' }}">
                            <div class="card w-100 border-0 overlay-bg img-hover "
                                @php $bgimg =  asset('/storage/'.$item->poster); @endphp
                                style="background-image: url({{ $bgimg }}); background-size: cover;">

                                <span class="single-name"></span>
                                <div class="pt-4 ps-4 pb-18 z-1">
                                    <h3 class="text-white mb-0">{{ $item->city }}</h3>

                                </div>

                                <a class="stretched-link"
                                    href="{{ route('eventmie.events_index', ['search' => urlencode($item->city)]) }}"></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <!-- New Themes cities_events END -->

    <!-- New Themes Blogs Start-->
    @if (!empty($posts))
        <div class="py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-8">
                        <h2>@lang('eventmie-pro::em.blogs')</h2>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-sm text-primary mt-lg-2 {{ App::isLocale('ar') ? 'float-start' : 'float-end' }}"
                            href="{{ route('eventmie.get_posts') }}">@lang('eventmie-pro::em.view_all') <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="row">
                    @foreach ($posts as $item)
                        <div class="col-lg-4 col-md-6 col-12">
                            <a class="text-reset"href="{{ route('eventmie.post_view', $item['slug']) }}">
                                <div class="card smooth-shadow-sm border-0 mb-4 mb-lg-0">
                                    <div class="card-img">
                                        <div class="back-image rounded-top"
                                            style="background-image:url('/storage/{{ $item['image'] }}')"></div>
                                    </div>
                                    <div class="card-body ">
                                        <h5 class="mb-0">{{ Str::limit($item['title'], 35) }}</h5>
                                        <p class="text-ms font-weight-semi-bold mb-2">
                                            {{ Str::limit($item['excerpt'], 50) }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <!-- New Themes Blogs End-->

    <!-- New Themes Organiser section Start -->
    <div class="py-7">
        <div class="container">
            <div class="row ">
                <div class="col-12">
                    <div class="text-center mb-6">
                        <p class="mb-0">@lang('eventmie-pro::em.how_it_works')</p>
                        <h2 class="mb-1">@lang('eventmie-pro::em.for_event_organisers')</h2>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-4 col-12">
                    <div class="step text-center mb-6 mb-lg-0">
                        <div
                            class="border border-primary border-3 icon-xxxl icon-shape rounded-circle bg-white mb-lg-7 mb-3">
                            <div class="icon-shape icon-xl bg-white shadow rounded-circle ">
                                <i class="fas fa-calendar-plus fa-1x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">1. @lang('eventmie-pro::em.organiser_1')</h3>
                        <p class="mb-0 px-lg-3">@lang('eventmie-pro::em.organiser_1_info') </p>
                    </div>

                </div>
                <div class="col-md-4 col-12">
                    <div class="step text-center mb-6 mb-lg-0">
                        <div class="border border-info border-3 icon-xxxl icon-shape rounded-circle bg-white mb-lg-7 mb-3">
                            <div class="icon-shape icon-xl bg-white shadow rounded-circle ">
                                <i class="fas fa-calendar-check fa-1x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">2. @lang('eventmie-pro::em.organiser_2')</h3>
                        <p class="mb-0 px-lg-3">@lang('eventmie-pro::em.organiser_2_info')</p>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="text-center mb-6 mb-lg-0">
                        <div
                            class="border border-success border-3 icon-xxxl icon-shape rounded-circle bg-white mb-lg-7 mb-3">
                            <div class="icon-shape icon-xl bg-white shadow rounded-circle ">
                                <i class="fas fa-money-check-alt fa-1x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">3. @lang('eventmie-pro::em.organiser_3')</h3>
                        <p class="mb-0 px-lg-3">@lang('eventmie-pro::em.organiser_3_info') </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- New Themes Organiser section End -->


    <!-- New Themes TRAVEL INFO Start -->
    <div class="py-7 bg-light">
        <div class="container">
            <div class="row ">
                <div class="col-12">
                    <div class="text-center mb-10">
                        <p class="mb-0">@lang('eventmie-pro::em.how_it_works') </p>
                        <h2 class="mb-1">@lang('eventmie-pro::em.for_customers')</h2>
                    </div>
                </div>
            </div>
            <div class="row ">
                <div class="col-md-4 col-12">
                    <div class="step text-center mb-6 mb-lg-0">
                        <div
                            class="border border-primary border-3 icon-xxxl icon-shape rounded-circle bg-white mb-lg-7 mb-3">
                            <div class="icon-shape icon-xl bg-white shadow rounded-circle ">
                                <i class="fas fa-calendar-alt fa-1x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">1. @lang('eventmie-pro::em.customer_1')</h3>
                        <p class="mb-0 px-lg-3">@lang('eventmie-pro::em.customer_1_info') </p>
                    </div>

                </div>
                <div class="col-md-4 col-12">
                    <div class="step text-center mb-6 mb-lg-0">
                        <div class="border border-info border-3 icon-xxxl icon-shape rounded-circle bg-white mb-lg-7 mb-3">
                            <div class="icon-shape icon-xl bg-white shadow rounded-circle ">
                                <i class="fas fa-ticket-alt fa-1x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">2. @lang('eventmie-pro::em.customer_2')</h3>
                        <p class="mb-0 px-lg-3">@lang('eventmie-pro::em.customer_2_info')</p>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="text-center mb-6 mb-lg-0">
                        <div
                            class="border border-success border-3 icon-xxxl icon-shape rounded-circle bg-white mb-lg-7 mb-3">
                            <div class="icon-shape icon-xl bg-white shadow rounded-circle ">
                                <i class="fas fa-walking fa-1x text-primary"></i>
                            </div>
                        </div>
                        <h3 class="mb-3">3. @lang('eventmie-pro::em.customer_3')</h3>
                        <p class="mb-0 px-lg-3">@lang('eventmie-pro::em.customer_3_info') </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- New Themes TRAVEL INFO End -->

@endsection

@section('javascript')
    <script type="text/javascript">
        var google_map_key = {!! json_encode(setting('apps.google_map_key')) !!};
        var events_slider = true;
    </script>
    <script type="text/javascript" src="{{ eventmie_asset('js/welcome.js') }}"></script>
@stop
