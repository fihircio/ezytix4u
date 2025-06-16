@extends('eventmie::o_dashboard.index')

@section('title')
    @lang('eventmie-pro::em.lucky_draw')
@endsection

@section('o_dashboard')
<div class="container-fluid my-2">
    <div class="row">
        <div class="col-md-12">
            <router-view
                :date_format="{{ json_encode(
                    [
                        'vue_date_format' => format_js_date(),
                        'vue_time_format' => format_js_time(),
                    ],
                    JSON_HEX_APOS,
                ) }}">
            </router-view>
        </div>
    </div>
</div>

@section('javascript')
<script>var path = {!! json_encode($path, JSON_HEX_TAG) !!};</script>
<script type="text/javascript" src="{{ eventmie_asset('js/luckydraw.js') }}"></script>
@stop