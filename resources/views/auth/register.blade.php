@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">{{ __('Create Account') }}</h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Play as Guest:</strong> You can play games without an account! 
                        Your progress is saved in your browser. Create an account to sync across devices.
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="username" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    {{ __('Create Account') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <div class="text-center">
                        <p class="mb-0">Already have an account? 
                            <a href="{{ route('login') }}">{{ __('Login') }}</a>
                        </p>
                        <p class="mt-2 text-muted small">
                            Or continue as <a href="{{ route('home') }}">Guest</a> - your data is saved in your browser
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
