@extends('eventmie::o_dashboard.index')

{{-- Page title --}}
@section('title')
    @lang('eventmie-pro::em.scan_ticket')
@endsection

@section('o_dashboard')
    <section>
        <div class="py-2 bg-light">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-md-12">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </div>
                </div>

               <div class="row">
                    <div class="col-lg-12">

                        <div class="card border-0 shadow-sm">
                            <h1 class="fw-bold h2 pt-3 ps-3"> @lang('eventmie-pro::em.scan_ticket')</h1>
                            <ticket-scan
                                :date_format="{{ json_encode(
                                    [
                                        'vue_date_format' => format_js_date(),
                                        'vue_time_format' => format_js_time(),
                                    ],
                                    JSON_HEX_APOS,
                                ) }}">
                            </ticket-scan>

                            <form id="form" action="{{ route('eventmie.verify_ticket') }}" method="POST"
                                enctype="multipart/form-data" class="lgx-contactform">
                                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                <input type="hidden" name="booking_id" id="booking_id">
                                <input type="hidden" name="order_number" id="order_number">

                                <div class="d-grid">
                                    <button type="submit" id="check_in_button" class="btn btn-success btn-lg"
                                        style="display: none;">@lang('eventmie-pro::em.verify_n_checkin')</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script type="text/javascript" src="{{ eventmie_asset('js/ticket_scan.js') }}"></script>
@stop
