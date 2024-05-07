@extends('eventmie::layouts.app')

@section('title')
    {{ ucfirst($venue->title) }}
@endsection

@section('content')
<section>
    @if(!empty(json_decode($venue->images, true)))
    <div class="container-fluid p-0">
        <div class="cover-img-bg" style="background-image: url({{ '/storage/' . json_decode($venue->images, true)[0] }});">
            <img class="cover-img" src="{{ '/storage/' . json_decode($venue->images, true)[0] }}" alt="{{ $venue->title }}" />
        </div>
    </div>
    @endif

    <div class="container mt-4">
        <div class="row mb-5">
            <div class="col-xl-8 col-lg-8 col-md-12 col-12">
                <div class="card mb-4">
                    
                    <div class="card-body p-4">
                        <h2 class="mb-2">{{ $venue->title }}</h2>
                        <div class="mb-4">{!! $venue->description !!}</div>
                    </div>
                </div>
                
                @if($venue->pricing)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4>@lang('eventmie-pro::em.pricing')</h4>
                        <div>{!! $venue->pricing !!}</div>
                    </div>
                </div>
                @endif
                
                @if($venue->availability)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4>@lang('eventmie-pro::em.availability')</h4>
                        <div>{!! $venue->availability !!}</div>
                    </div>
                </div>
                @endif
                
                @if($venue->food)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4>@lang('eventmie-pro::em.food')</h4>
                        <div>{!! $venue->food !!}</div>
                    </div>
                </div>
                @endif
                
                @if($venue->amenities)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4>@lang('eventmie-pro::em.amenities')</h4>
                        <div>{!! $venue->amenities !!}</div>
                    </div>
                </div>
                @endif
                

                @if(!empty(json_decode($venue->images, true)))
                <div class="card my-5" id="gallery">
                    <div class="card-body p-4">
                        <h4 class="mb-4">@lang('eventmie-pro::em.gallery')</h4>
                        <div class="zoom-gallery">
                            <div class="row">
                                @foreach(json_decode($venue->images, true) as $val)
                                <div class="col-md-6">
                                    <img src="{{ '/storage/' . $val }}" class="img-fluid rounded mb-2" alt="{{ $venue->title }}" />
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
                @endif
                
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-1">@lang('eventmie-pro::em.location')</h4>
                        <div id="venue_map" class="listing-map" style="width:100%;height:500px"></div>
                    </div>
                </div>


            </div>
            
            <div class="col-xl-4 col-lg-4 col-md-12 col-12 ">
                @if($venue->seated_guestnumber && $venue->standing_guestnumber)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4 class="mb-1">@lang('eventmie-pro::em.seated') / @lang('eventmie-pro::em.standing')</h4>
                                <p class="mb-0">
                                    {{ $venue->seated_guestnumber }} / {{ $venue->standing_guestnumber }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($venue->neighborhoods)
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4 class="mb-1">@lang('eventmie-pro::em.neighborhoods')</h4>
                                <p class="mb-0">
                                    {{ $venue->neighborhoods }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($venue->address || $venue->city || $venue->state || !empty($venue->country['country_name']))
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div>
                                <h4 class="mb-1">@lang('eventmie-pro::em.address')</h4>
                                <p class="mb-0">
                                    @if($venue->address) {{ $venue->address }} @endif
                                    @if($venue->city) {{ ', '.$venue->city }} @endif
                                    @if($venue->state) {{ ', '.$venue->state }} @endif
                                    @if($venue->zipcode) {{ ', '.$venue->zipcode }} @endif
                                    @if(!empty($venue->country['country_name'])) {{ ', '.$venue->country['country_name'] }} @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                


                @if ($venue->show_quoteform)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body p-4">
                                <h5 class="card-title">@lang('eventmie-pro::em.request_a_quote')</h5>
                                @if (\Session::has('msg'))
                                <div class="alert alert-success">
                                    {{ \Session::get('msg') }}
                                </div>
                                @endif

                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <form method="POST" class="lgx-contactform"
                                    action="{{ route('eventmie.request_quote') }}">
                                    @csrf
                                    @honeypot

                                    <input type="hidden" name="contact_email" value="{{ $venue->contact_email }}">

                                    <div class="form-group mb-3">
                                        <input type="text" name="name" class="form-control" id="lgxname"
                                            placeholder="{{ __('eventmie-pro::em.name') }}" required=""
                                            aria-required="true">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="email" name="email" class="form-control" id="lgxemail"
                                            placeholder="{{ __('eventmie-pro::em.email') }}" required=""
                                            aria-required="true">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="phone" class="form-control" id="phone"
                                            placeholder="{{ __('eventmie-pro::em.phone') }}" required=""
                                            aria-required="true">
                                    </div>
                                    <div class="form-group mb-3">
                                        <input type="text" name="guests" class="form-control lgxsubject"
                                            id="lgxsubject" placeholder="{{ __('eventmie-pro::em.number_of_guests') }}"
                                            aria-required="true">
                                    </div>
                                    <div class="form-group mb-3">
                                        <textarea class="form-control lgxmessage" name="message" id="lgxmessage" rows="5"
                                            placeholder="{{ __('eventmie-pro::em.mention_query') }}" required="" aria-required="true"></textarea>
                                    </div>
                                    <button type="submit" name="submit" value="contact-form"
                                        class="btn btn-primary"><span>@lang('eventmie-pro::em.send_message')</span></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script src="https://maps.googleapis.com/maps/api/js?key={{ setting('apps.google_map_key') }}&callback=initMap&v=weekly" defer></script>
<script>
function initMap() {
    const myLatLng = { 
        lat: {{ $venue->glat }}, 
        lng: {{ $venue->glong }}
    };
    const map = new google.maps.Map(document.getElementById("venue_map"), {
        zoom: 15,
        center: myLatLng,
    });

    new google.maps.Marker({
        position: myLatLng,
        map,
        title: {!! setting('site.site_name') ? json_encode(setting('site.site_name')) : json_encode(config('app.name')) !!},
    });
}
window.initMap = initMap;
</script>
@endsection