@extends('layouts.empty')

@section('content')
    <div class="card">
        <div class="card-header">{{ __('Verify Your Email Address') }}</div>

        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success" role="alert">
                    {{ session('message') }}
                </div>
            @endif

            {{ __('Before proceeding, please enter your email for a verification link.') }}
            <form class="d-inline" method="POST" action="{{ route('student.verification.resend') }}">
                @csrf
                <input type="email" class="form-control @error('verify_email') is-invalid @enderror mb-3" name="verify_email" placeholder="Email ID"/> 
                <button type="submit" class="btn btn-dark w-100 mb-0 align-baseline">{{ __('click here to verify your email') }}</button>
            </form>
        </div>
    </div>
@endsection
