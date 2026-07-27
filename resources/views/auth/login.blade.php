@extends('layouts.auth')

@section('title', 'Signin - InApp Inventory Dashboard')

@section('content')
<div class="card" style="max-width:420px; width:100%;">
  <div class="card-body p-5">
    <div class="text-center mb-3">
      <a href="{{ route('dashboard') }}" class="mb-4 d-inline-block">
        <img src="{{ asset('assets/images/logo-icon.svg') }}" alt="" width="36">
        <span class="ms-2"><img src="{{ asset('assets/images/logo.svg') }}" alt=""></span>
      </a>
      <h1 class="card-title mb-5 h5">Sign in to your account</h1>
    </div>

    <form class="needs-validation mt-3" method="POST" action="{{ route('auth.login.submit') }}" novalidate>
      @csrf
      <div class="mb-3">
        <label for="login" class="form-label">Email or Username</label>
        <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" placeholder="Email or Username" required autofocus>
        @error('login')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @else
          <div class="invalid-feedback">Please enter your email or username.</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="password" class="form-label d-flex justify-content-between">
          <span>Password</span>
          <a href="#" class="small link-primary">Forgot Password?</a>
        </label>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required minlength="6">
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @else
          <div class="invalid-feedback">Please provide a password (min 6 characters).</div>
        @enderror
      </div>

      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
          <input id="remember" class="form-check-input" type="checkbox" name="remember">
          <label class="form-check-label small" for="remember">Remember me</label>
        </div>
      </div>

      <button class="btn btn-primary w-100" type="submit">Sign in</button>
    </form>
  </div>
</div>
@endsection
