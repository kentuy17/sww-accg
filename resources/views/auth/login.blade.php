@extends('adminlte::auth.login')

@section('title', 'SWW - Audit')

@section('auth_body')
  <form action="/login" method="post">
    @csrf

    {{-- Email field --}}
    <div class="input-group mb-3">
      <input type="text" name="username" class="form-control @error('email') is-invalid @enderror" value="{{ old('username') }}" placeholder="Username" autofocus>

      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
        </div>
      </div>

      @error('username')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div>

    {{-- Password field --}}
    <div class="input-group mb-3">
      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('adminlte::adminlte.password') }}">

      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
        </div>
      </div>

      @error('password')
        <span class="invalid-feedback" role="alert">
          <strong>{{ $message }}</strong>
        </span>
      @enderror
    </div>

    {{-- Login field --}}
    <div class="row">
      <div class="col-7">
        <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

          <label for="remember">
            {{ __('adminlte::adminlte.remember_me') }}
          </label>
        </div>
      </div>

      <div class="col-5">
        <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
          <span class="fas fa-sign-in-alt"></span>
          {{ __('adminlte::adminlte.sign_in') }}
        </button>
      </div>
    </div>

  </form>
@stop
