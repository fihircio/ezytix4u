@extends('eventmie::o_dashboard.index')

@section('title')
    @lang('eventmie-pro::em.mytags')
@endsection


@section('o_dashboard')
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-md-12">
            <router-view :organiser_id="{{ $organiser_id }}"></router-view>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>var path = {!! json_encode($path, JSON_HEX_TAG) !!};</script>
<script type="text/javascript" src="{{ eventmie_asset('js/tags_manage.js') }}"></script>
@stop