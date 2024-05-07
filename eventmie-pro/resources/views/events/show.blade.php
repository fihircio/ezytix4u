@extends('eventmie::layouts.app')

@section('title', $event->title)
@section('meta_title', $event->meta_title)
@section('meta_keywords', $event->meta_keywords)
@section('meta_description', $event->meta_description)
@section('meta_image', '/storage/' . $event['thumbnail'])
@section('meta_url', url()->current())


@section('content')

    <!--Cover-->
    <section>
        <div class="container-fluid p-0">
            <div class="cover-img-bg" style="background-image: url({{ '/storage/' . $event['poster'] }});">
                <img class="cover-img" src="{{ '/storage/' . $event['poster'] }}" alt="{{ $event['title'] }}" />
            </div>
        </div>
    </section>

    <!--ABOUT-->
    <section>
        <div class="pt-lg-4 pb-lg-11 py-4">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8 col-lg-8 col-md-12 col-12">
                        <div class="card mb-4">
                            <!-- listing detail head -->
                            <div class="card-body p-4 py-3">
                                <h2 class="mb-2">{{ $event['title'] }}</h2>
                                <p class="mb-4 fs-8">{{ $event['excerpt'] }}</p>
                                <p class="fs-6 mb-2">
                                    @if (!empty($event['online_location']))
                                        <span class="badge bg-info text-white"><i class="fas fa-signal"></i>&nbsp;
                                            @lang('eventmie-pro::em.online_event')</span>
                                    @endif

                                    <span class="badge bg-primary text-white">{{ $category['name'] }}</span>

                                    @if (!empty($free_tickets))
                                        <span class="badge bg-primary text-white">@lang('eventmie-pro::em.free_tickets')</span>
                                    @endif

                                    @if ($event->repetitive)
                                        @if ($event->repetitive_type == 1)
                                            <span class="badge bg-primary text-white">@lang('eventmie-pro::em.repetitive_daily_event')</span>
                                        @elseif($event->repetitive_type == 2)
                                            <span class="badge bg-primary text-white">@lang('eventmie-pro::em.repetitive_weekly_event')</span>
                                        @elseif($event->repetitive_type == 3)
                                            <span class="badge bg-primary text-white">@lang('eventmie-pro::em.repetitive_monthly_event')</span>
                                        @endif
                                    @endif

                                    @if ($ended)
                                        <span class="badge bg-danger text-white">@lang('eventmie-pro::em.event_ended')</span>
                                    @endif
                                </p>
                            </div>
                            <div class="card-footer bg-gradient">
                                <div class="text-white">
                                    <span><strong>@lang('eventmie-pro::em.share_event') &nbsp;</strong></span>
                                    <a class="me-1 text-white  badge text-bg-primary" target="_blank"
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}">
                                        <i class="fab fa-facebook"></i>
                                    </a>
                                    <a class="me-1 text-white  badge text-bg-primary" target="_blank"
                                        href="https://twitter.com/intent/tweet?text={{ urlencode($event->title) }}&url={{ url()->current() }}">

                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a class="me-1 text-white  badge text-bg-primary" target="_blank"
                                        href="http://www.linkedin.com/shareArticle?mini=true&url={{ url()->current() }}&title={{ urlencode($event->title) }}">
                                        <i class="fab fa-linkedin"></i>
                                    </a>
                                    <a class="me-1 text-white  badge text-bg-primary" target="_blank"
                                        href="https://wa.me/?text={{ url()->current() }}">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                    <a class="me-1 text-white  badge text-bg-primary" target="_blank"
                                        href="https://www.reddit.com/submit?title={{ urlencode($event->title) }}&url={{ url()->current() }}">
                                        <i class="fab fa-reddit"></i>
                                    </a>

                                    <a class="me-1 text-white  badge text-bg-primary" href="javascript:void(0)"
                                        onclick="copyToClipboard()"><i class="fas fa-link"></i></a>

                                </div>

                            </div>
                            <!-- listing detail head -->
                        </div>
                        <!--SCHEDULE-->
                        <div class="card mb-4 bg-light" id="buy-tickets">
                            <div class="card-body p-4">
                                <div class="mb-4 text-left">
                                    @if ($event->merge_schedule)
                                        <h4 class="mb-0 fw-bold h4">
                                            @lang('eventmie-pro::em.get_tickets') &nbsp;
                                            <div class="badge bg-primary position-relative">
                                                @lang('eventmie-pro::em.seasonal_tickets')
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
                                                    <i class="fas fa-medal"></i>
                                                    <span class="visually-hidden">&nbsp;</span>
                                                </span>
                                            </div>
                                        </h4>

                                        <p class="text-primary"> @lang('eventmie-pro::em.seasonal_tickets_ie')</p>
                                    @else
                                        <h4 class="mb-0 fw-bold h4">@lang('eventmie-pro::em.get_tickets')</h4>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-xl-12 col-md-12 col-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <select-dates :event="{{ json_encode($event, JSON_HEX_APOS) }}"
                                                    :max_ticket_qty="{{ json_encode($max_ticket_qty, JSON_HEX_APOS) }}"
                                                    :login_user_id="{{ json_encode(\Auth::id(), JSON_HEX_APOS) }}"
                                                    :is_customer="{{ Auth::id() ? (Auth::user()->hasRole('customer') ? 1 : 0) : 1 }}"
                                                    :is_organiser="{{ Auth::id() ? (Auth::user()->hasRole('organiser') ? 1 : 0) : 0 }}"
                                                    :is_admin="{{ Auth::id() ? (Auth::user()->hasRole('admin') ? 1 : 0) : 0 }}"
                                                    :is_paypal="{{ $is_paypal }}"
                                                    :is_offline_payment_organizer="{{ setting('booking.offline_payment_organizer') ? 1 : 0 }}"
                                                    :is_offline_payment_customer="{{ setting('booking.offline_payment_customer') ? 1 : 0 }}"
                                                    :tickets="{{ json_encode($tickets, JSON_HEX_APOS) }}"
                                                    :booked_tickets="{{ json_encode($booked_tickets, JSON_HEX_APOS) }}"
                                                    :currency="{{ json_encode($currency, JSON_HEX_APOS) }}"
                                                    :total_capacity="{{ $total_capacity }}"
                                                    :is_usaepay="{{ json_encode($is_usaepay) }}"
                                                    :date_format="{{ json_encode(
                                                        [
                                                            'vue_date_format' => format_js_date(),
                                                            'vue_time_format' => format_js_time(),
                                                        ],
                                                        JSON_HEX_APOS,
                                                    ) }}">

                                                </select-dates>
                                            </div>

                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                        <!--SCHEDULE END-->

                        <!-- post single -->
                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <h4>@lang('eventmie-pro::em.overview')</h4>
                                <p>{!! $event['description'] !!}</p>
                            </div>
                        </div>

                        <!-- post single -->

                        <!--Event FAQ-->
                        @if ($event['faq'])
                            <div class="card mb-4">
                                <div class="card-body p-4">
                                    <h4 class=" text-left">@lang('eventmie-pro::em.event_info')</h4>
                                    <p>{!! $event['faq'] !!}</p>
                                </div>
                            </div>
                        @endif
                        <!--Event FAQ END-->


                        <!--TAGS-->
                        @php $i = 0; @endphp
                        @foreach ($tag_groups as $key => $group)
                            @php $i++; @endphp
                            <div class="card mb-4  {{ $i % 2 ? 'bg-light' : '' }}">
                                <div class="card-body p-4">
                                    <!-- section heading  -->
                                    <h4 class="mb-3">{{ ucfirst($key) }}</h4>
                                    <div class="row">
                                        @foreach ($group as $key1 => $value)
                                            <div class="col-lg-4 col-md-6 col-12 text-center">
                                                <!-- member -->
                                                @if ($value['is_page'] > 0)
                                                    <a
                                                        href="{{ route('eventmie.events_tags', [$event->slug, str_replace(' ', '-', $value['title'])]) }}">
                                                    @elseif($value['website'])
                                                        <a href="{{ $value['website'] }}" target="_blank">
                                                @endif
                                                <div class="mb-3">
                                                    @if ($value['image'])
                                                        <img src="/storage/{{ $value['image'] }}"
                                                            alt="{{ $value['title'] }}" class="rounded-3 w-100 mb-4 " />
                                                    @else
                                                        <img src="{{ asset('ep_img/512x512.webp') }}"
                                                            alt="{{ $value['title'] }}" class="rounded-3 w-100 mb-4 " />
                                                    @endif
                                                    <h5 class="mb-0">
                                                        @if ($value['is_page'] > 0)
                                                            <a
                                                                href="{{ route('eventmie.events_tags', [$event->slug, str_replace(' ', '-', $value['title'])]) }}">{{ $value['title'] }}</a>
                                                        @elseif($value['website'])
                                                            <a href="{{ $value['website'] }}"
                                                                target="_blank">{{ $value['title'] }}</a>
                                                        @else
                                                            {{ $value['title'] }}
                                                        @endif
                                                    </h5>
                                                    <p class="small font-weight-semibold">
                                                        @if ($value['sub_title'])
                                                            {{ $value['sub_title'] }}
                                                        @endif
                                                    </p>

                                                </div>
                                                @if ($value['is_page'] > 0 || $value['website'])
                                                    </a>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!--Tags END-->

                        <!--PHOTO GALLERY-->
                        @if (!empty($event->images))
                            <div class="card mb-4">
                                <div class="card-body p-4 pb-0">
                                    <h4 class="mb-3">@lang('eventmie-pro::em.event_gallery')</h4>
                                    <gallery-images :gimages="{{ $event->images }}" :style=''>
                                    </gallery-images>
                                </div>
                            </div>
                        @endif
                        <!--PHOTO GALLERY END-->

                        <!--Event Video-->
                        @if (!empty($event->video_link))
                            <div class="card mb-4">
                                <div class="card-body p-4">
                                    <h4 class="mb-3">@lang('eventmie-pro::em.watch_trailer')</h4>
                                    <div class="ratio ratio-16x9">
                                        <iframe src="https://www.youtube.com/embed/{{ $event->video_link }}"
                                            allowfullscreen class="rounded-4 img-hover"></iframe>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!--Event Video END-->

                        <!--GOOGLE MAP-->
                        @if ($event->latitude && $event->longitude)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h4 class="mb-3">@lang('eventmie-pro::em.location')</h4>
                                    <div class="innerpage-section g-map-wrapper">
                                        <div class="lgxmapcanvas map-canvas-default">
                                            <g-component :lat="{{ json_encode($event->latitude, JSON_HEX_APOS) }}"
                                                :lng="{{ json_encode($event->longitude, JSON_HEX_APOS) }}">
                                            </g-component>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!--GOOGLE MAP END-->

                    </div>

                    <!-- comment-form -->

                    {{-- Event Start Date Start --}}
                    <div class="col-xl-4 col-lg-4 col-md-12 col-12">

                        <!-- widget -->
                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <div class="d-grid">
                                    <a class="btn btn-primary btn-lg " href="#buy-tickets">
                                        <i class="fas fa-ticket-alt"></i>
                                        @lang('eventmie-pro::em.get_tickets')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <!-- card-->
                                <h4 class="mb-2 fw-bold">@lang('eventmie-pro::em.where')</h4>
                                <p>
                                    @if (!empty($event['online_location']))
                                        <strong>@lang('eventmie-pro::em.online_event')</strong> <br>
                                    @endif

                                    @if ($event->venues->isNotEmpty())
                                        <a class="col-white"
                                            href="{{ route('eventmie.venues.show', [$event->venues[0]->slug]) }}"><strong>{{ $event->venue }}
                                                <i class="fas fa-external-link-alt"></i></strong> </a>
                                    @else
                                        <strong>{{ $event->venue }}</strong>
                                    @endif

                                    <br>
                                    @if ($event->address)
                                        {{ $event->address }} {{ $event->zipcode }} <br>
                                    @endif

                                    @if ($event->city)
                                        {{ $event->city }},
                                    @endif

                                    @if ($event->state)
                                        {{ $event->state }},
                                    @endif

                                    @if ($country)
                                        {{ $country->get('country_name') }}
                                    @endif
                                </p>

                            </div>
                        </div>

                        <!-- card-->
                        <div class="card  mb-4">
                            <div class="card-body">
                                <h4 class="mb-2 fw-bold">@lang('eventmie-pro::em.when')</h4>


                                @if (!$event->repetitive)
                                    <p>
                                        {{ userTimezone($event->start_date . ' ' . $event->start_time, 'Y-m-d H:i:s', format_carbon_date(false)) }}

                                        {{ showTimezone() }}

                                        -

                                        {{ userTimezone($event->end_date . ' ' . $event->end_time, 'Y-m-d H:i:s', format_carbon_date(false)) }}

                                        {{ showTimezone() }}
                                    </p>
                                @else
                                    <p>
                                        {{ userTimezone($event->start_date . ' ' . $event->start_time, 'Y-m-d H:i:s', format_carbon_date(true)) }}

                                        -

                                        {{ userTimezone($event->start_date . ' ' . $event->start_time, 'Y-m-d H:i:s', 'Y-m-d') <= userTimezone($event->end_date . ' ' . $event->end_time, 'Y-m-d H:i:s', 'Y-m-d') ? userTimezone($event->end_date . ' ' . $event->end_time, 'Y-m-d H:i:s', format_carbon_date(true)) : userTimezone($event->start_date . ' ' . $event->start_time, 'Y-m-d H:i:s', format_carbon_date(true)) }}

                                    </p>
                                @endif

                            </div>
                        </div>

                    </div>
                    {{-- Event Start Date End --}}
                </div>
            </div>
        </div>

    </section>
    <!--ABOUT END-->
@endsection

@section('javascript')
    <script type="text/javascript">
        var google_map_key = {!! json_encode($google_map_key) !!};
    </script>
    <script src="https://cdn.jsdelivr.net/npm/v-mask/dist/v-mask.min.js"></script>
    <script type="text/javascript" src="{{ eventmie_asset('js/events_show.js') }}"></script>
@stop
