@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<!-- Error 404 Template 1 - Bootstrap Brain Component -->
<section class="py-3 py-md-5 d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row">
        <div class="col-12">
            <div class="text-center">
            <h2 class="d-flex justify-content-center align-items-center gap-2 mb-4">
                <span class="display-1 fw-bold">4</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-exclamation-circle-fill text-danger display-4" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4m.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                </svg>
                <span class="display-1 fw-bold bsb-flip-h">4</span>
            </h2>
            <h3 class="h2 mb-2">Oops! You're lost.</h3>
            <p class="mb-5">The page you are looking for was not found.</p>
            <!-- <a class="btn bsb-btn-5xl btn-dark rounded-pill px-5 fs-6 m-0" href="#!" role="button">Back to Home</a> -->
            </div>
        </div>
        </div>
    </div>
</section>
@endsection