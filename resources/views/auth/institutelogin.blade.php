@extends('layouts.empty')

@section('content')
<div class="card">
    <div class="card-header text-center fs-4 fw-bold">{{ __('Institute Login') }}</div>

    <div class="card-body">
        @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('institute.login.submit') }}">
            @csrf

            <div class="row mb-3">
                <label for="Iemail" class="col-md-4 col-form-label text-md-end d-none">{{ __('Email')  }}</label>
                <div class="col-md-12">
                    <input id="rollnumber" type="text" class="form-control @error('Iemail') is-invalid @enderror" placeholder="{{ __('Email') }}" name="Iemail" value="{{ old('Iemail') }}" required autocomplete="off" autofocus>

                    @error('rollnumber')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <label for="password" class="col-md-4 col-form-label text-md-end d-none">{{ __('Password') }}</label>

                <div class="col-md-12">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('Password') }}" name="password" required autocomplete="off" autocomplete="current-password">

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} autocomplete="off">

                        <label class="form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    @if (Route::has('institute.password.request'))
                    <a class="" href="{{ route('institute.password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                    @endif
                </div>
            </div>

            <div class="row mb-0">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-dark w-100">
                        {{ __('Login') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection