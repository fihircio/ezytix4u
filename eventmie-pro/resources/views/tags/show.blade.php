@extends('eventmie::layouts.app')

@section('title')
    {{ ucfirst($tag->type) }} - {{ $tag->title }}
@endsection

@section('content')
    <main>
        <div class="py-6 py-lg-10 ">
            <div class="container">
                <div class="row">
                    <!-- img  -->
                    <div class="col-lg-2 col-md-3 col-12">
                        @if ($tag->image)
                            <img src="/storage/{{ $tag->image }}" alt="{{ $tag->title }}"
                                class="rounded-circle avatar-lg-custom mx-auto mb-4" />
                        @else
                            <img src="{{ asset('ep_img/512x512.webp') }}" alt="{{ $tag->title }}"
                                class="rounded-circle avatar-lg-custom mx-auto mb-4" />
                        @endif
                    </div>
                    <!-- content  -->
                    <div class="col-lg-8 col-md-9 col-12">
                        <h1 class="mb-1">{{ $tag->title }}</h1>
                        <h5 class="mb-1">{{ $tag->sub_title }}</h5>
                        <!-- social media  -->
                        <div class="mt-4 mb-4">

                            @if ($tag->twitter)
                                <a class="me-1 text-inherit" href="{{ $tag->twitter }}" target="_blank">
                                    <i class="fab fa-twitter" aria-hidden="true"></i>
                                </a>
                            @endif

                            @if ($tag->facebook)
                                <a class="me-1 text-inherit" href="{{ $tag->facebook }}" target="_blank">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            @endif

                            @if ($tag->linkedin)
                                <a class="me-1 text-inherit" href="{{ $tag->linkedin }}" target="_blank">
                                    <i class="fab fa-linkedin" aria-hidden="true"></i>
                                </a>
                            @endif

                            @if ($tag->instagram)
                                <a class="me-1 text-inherit" href="{{ $tag->instagram }}" target="_blank">
                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                </a>
                            @endif

                            @if ($tag->website)
                                <a class="me-1 text-inherit" href="{{ $tag->website }}" target="_blank">
                                    <i class="fas fa-globe" aria-hidden="true"></i>
                                </a>
                            @endif
                        </div>

                        <p class="lead">
                            {!! $tag->description !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
