@extends('layouts.empty')

@section('content')
<div class="card">
    <div class="card-header text-center fs-4 fw-bold">{{ __('Reset Password') }}</div>

    <div class="card-body">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="row mb-3">
                <label for="email" class="col-md-4 col-form-label text-md-end d-none">{{ __('Email Address') }}</label>

                <div class="col-md-12">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Email Address') }}" required autocomplete="email" autofocus>

                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="password" class="col-md-4 col-form-label text-md-end d-none">{{ __('Password') }}</label>

                <div class="col-md-12">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" name="password" required autocomplete="new-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="password-confirm" class="col-md-4 col-form-label text-md-end d-none">{{ __('Confirm Password') }}</label>

                <div class="col-md-12">
                    <input id="password-confirm" type="password" class="form-control" placeholder="{{ __('Confirm Password') }}" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-secondary w-100">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
