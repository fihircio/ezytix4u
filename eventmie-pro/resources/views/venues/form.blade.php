@extends('eventmie::o_dashboard.index')

@section('title')
    @lang('eventmie-pro::em.myvenues')
@endsection
    
@section('o_dashboard')
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-xl-12 col-md-12 col-12">
            <router-view
                :organiser_id="{{ $organiser_id }}"
            ></router-view> 
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>    
    var path = {!! json_encode($path, JSON_HEX_TAG) !!};
</script>
<script  src="https://maps.googleapis.com/maps/api/js?key={{setting('apps.google_map_key')}}&libraries=places"></script>
<script type="text/javascript" src="{{ eventmie_asset('js/venues_manage.js') }}"></script>
@stop
