@extends('eventmie::auth.authapp')

@section('title')
    @lang('eventmie-pro::em.register')
@endsection

@section('authcontent')

    <div class="card border-0 shadow">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            <span class="" role="alert">
                                <strong>{{ $error }}</strong>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card-body p-5">


            <h3 class="mb-4">@lang('eventmie-pro::em.register')</h3>
            <!-- form -->
            <form method="POST" action="{{ route('eventmie.register') }}">
                @csrf
                @honeypot
                <div class="mb-3">
                    <label for="email" class="form-label">@lang('eventmie-pro::em.name')</label>
                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                        name="name" value="{{ old('name') }}" required autofocus placeholder="@lang('eventmie-pro::em.name')">
                </div>

                <!-- email -->
                <div class="mb-3">
                    <label for="email" class="form-label">@lang('eventmie-pro::em.email_address')</label>
                    <input id="email" type="email"
                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email"
                        value="{{ old('email') }}" required placeholder="@lang('eventmie-pro::em.email')">

                </div>
                <!-- password -->
                <div class="mb-3">
                    <label for="password" class="form-label">@lang('eventmie-pro::em.password')</label>
                    <input id="password" type="password"
                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required
                        placeholder="@lang('eventmie-pro::em.password')">

                </div>


                <div class="mb-2">
                    <input class="form-check-input" type="checkbox" name="accept" id="accept" checked value="1"
                        hidden>
                    <p class="text-sm">
                        @lang('eventmie-pro::em.accept_terms')
                    </p>
                </div>
                <!-- button -->

                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-door-open"></i>
                    @lang('eventmie-pro::em.register')</button>

                <div class="d-flex justify-content-between mb-2 pb-2 mt-3 text-sm ">
                    <!-- form check -->
                    <div class="fw-bold">
                        <a href="{{ route('eventmie.password.request') }}" class="text-inherit">@lang('eventmie-pro::em.forgot_password')</a>
                    </div>
                    <!-- forgot password -->
                    <div class="fw-bold">
                        <a href="{{ route('eventmie.login') }}" class="text-inherit"> @lang('eventmie-pro::em.login')</a>
                    </div>
                </div>
                <div class="mt-3">
                    <hr style="border-top: 2px solid #eee;">
                    @if (!empty(config('services')['facebook']['client_id']) || !empty(config('services')['google']['client_id']))
                        <div class="d-flex justify-content-between mb-2 pb-2 mt-3 text-sm">
                            <div class="text-left">
                                <span>@lang('eventmie-pro::em.continue_with')</span>
                            </div>
                            <div class="text-right">
                                @if (!empty(config('services')['facebook']['client_id']))
                                    <a href="{{ route('eventmie.oauth_login', ['social' => 'facebook']) }}"
                                        class="btn btn-sm btn-primary btn-block"><i class="fab fa-facebook-f"></i>
                                        @lang('eventmie-pro::em.facebook')</a>
                                @endif

                                @if (!empty(config('services')['google']['client_id']))
                                    <a href="{{ route('eventmie.oauth_login', ['social' => 'google']) }}"
                                        class="btn btn-sm btn-primary btn-block"><i class="fab fa-google"></i>
                                        @lang('eventmie-pro::em.google')</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </form>



        </div>
    </div>

@endsection
