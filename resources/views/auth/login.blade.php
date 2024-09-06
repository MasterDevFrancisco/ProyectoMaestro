@extends('layouts.app')

@section('content')
    <div class="container d-flex align-items-center" style="min-height: 91.8vh;">
        <div class="row justify-content-center w-100">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header text-center"
                        style="background-color: #2A2A2A; color: white; border: 2px solid #2A2A2A;">
                        {{ __('Iniciar Sesión') }}</div>

                    <div class="card-body" style="border: 2px solid #2A2A2A; padding: 20px;">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group mb-3">
                                <center>
                                    <label for="email"
                                        class="col-form-label text-md-end">{{ __('Correo Electrónico') }}</label>
                                </center>
                                <div>
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus
                                        style="background-color: #051824; color: white;">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <center>
                                    <label for="password" class="col-form-label text-md-end">{{ __('Contraseña') }}</label>
                                </center>
                                <div>
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password"
                                        style="background-color: #051824; color: white;">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Recuérdame') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mb-0 text-center">
                                <div>
                                    <button type="submit" class="btn btn-primary mb-2">
                                        {{ __('Iniciar Sesión') }}
                                    </button>
                                </div>
                                @if (Route::has('password.request'))
                                    <div>
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('¿Olvidaste tu contraseña?') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
