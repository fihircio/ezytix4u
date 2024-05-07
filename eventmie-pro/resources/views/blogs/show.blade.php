@extends('eventmie::layouts.app')

@section('title', $post['title'])
@section('meta_title', $post['seo_title'])
@section('meta_keywords', $post['meta_keywords'])
@section('meta_description', $post['meta_description'])
@section('meta_image', '/storage/' . $post['image'])
@section('meta_url', url()->current())

@section('content')
    <main>

        <!--News-->
        <section>
            <div class="py-lg-15">
                <div class="container">
                    <div class="row d-flex align-items-center">
                        <div class="col-lg-7 col-md-12 col-12">
                            <!-- img -->
                            <div class="mb-6 ">

                                <img src="/storage/{{ $post['image'] }}" alt="{{ $post['title'] }}"
                                    class="img-fluid rounded-3 img-hover">

                            </div>
                        </div>
                        <!-- content -->
                        <div class="col-lg-5 col-md-12 col-12">
                            <div class="ms-lg-3">
                                <div class="text-sm mb-4 ">
                                    <span class="me-2 font-weight-semi-bold">
                                        <i class="fas fa-history"></i>
                                        {{ \Carbon\Carbon::parse($post['updated_at'])->translatedFormat(format_carbon_date()) }}
                                    </span>
                                </div>
                                <h1 class="mb-6 display-5 fw-bold">
                                    {{ $post['title'] }}
                                </h1>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-12 blog-img">
                            {!! $post['body'] !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--News END-->

    </main>

@endsection
