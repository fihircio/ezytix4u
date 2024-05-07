@extends('eventmie::layouts.app')

@section('title')
    @lang('eventmie-pro::em.blogs')
@endsection

@section('content')
    <main>

        <!--Blogs-->
        <section>
            <!-- section blog -->
            <div class="py-lg-11 pb-7">
                <div class="container">
                    <div class="row">
                        @if (!empty($posts))
                            @foreach ($posts as $item)
                                <div class="col-lg-4 col-md-6 col-sm-12 col-12">
                                    <div class="mb-6">
                                        <!-- post classic block -->

                                        <a href="{{ route('eventmie.post_view', $item['slug']) }}">
                                            <img src="/storage/{{ $item['image'] }}" alt=""
                                                class="w-100 rounded-3 img-hover">
                                        </a>

                                        <div class="mt-4">
                                            <div class="text-sm mb-1 ">
                                                <span class="me-2 font-weight-semi-bold">
                                                    {{ \Carbon\Carbon::parse($item['updated_at'])->translatedFormat(format_carbon_date()) }}
                                                </span>
                                            </div>
                                            <h4 class="mb-0">
                                                <a href="{{ route('eventmie.post_view', $item['slug']) }}"
                                                    class="text-inherit">
                                                    {{ $item['title'] }}
                                                </a>
                                            </h4>
                                            <p>{{ $item['excerpt'] }}</p>
                                            <!-- postmeta start -->
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12 col-12">
                                <h4 class="text-center">@lang('eventmie-pro::em.nothing')</h4>
                            </div>
                        @endif

                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            {{ $posts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--Blogs END-->

    </main>
@endsection
