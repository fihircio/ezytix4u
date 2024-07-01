@extends('eventmie::layouts.app')
@section('title')
    @lang('eventmie-pro::em.private') 
@endsection

@section('content')
<main>
<section class="bg-light py-5">
    <div class="container">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                    <div class="row">
                        <div class="col-12 offset-sm-2 col-sm-8 offset-lg-3 col-lg-6">
                            <div class="alert alert-info">@lang('eventmie-pro::em.private_event_text')</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 offset-sm-2 col-sm-8 offset-lg-3 col-lg-6">
                            <form method="POST" class="lgx-contactform" action="{{ route('eventmie.verify_event_password') }}">
                                @csrf
                                <input type="hidden" name="event_slug" value="{{ $event->slug }}">
                                
                                <div class="form-group">
                                    <label for="event_password" class="col-form-label">@lang('eventmie-pro::em.enter') @lang('eventmie-pro::em.event') @lang('eventmie-pro::em.password') </label>
                                    <input type="password" name="event_password" class="form-control lgxname" id="event_password" placeholder="@lang('eventmie-pro::em.event') @lang('eventmie-pro::em.password')" required>
                                    
                                    @if ($errors->has('event_password'))
                                    <div class="alert alert-danger">{{ $errors->first('event_password') }}</div>
                                    @endif
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block mt-4"><span><i class="fas fa-door-closed"></i> @lang('eventmie-pro::em.continue')</span></button>
                            </form>
                        </div> <!--//.COL-->
                    </div>
            </div>
        </div>
    </div>
</section>
</main>
@endsection
