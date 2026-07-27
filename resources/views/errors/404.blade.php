@extends('layouts.auth')

@section('title', '404 Error - ' . \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))

@section('content')
@php
  $appLogo = \App\Models\Setting::get('app_logo');
@endphp
<div class="card border-0 bg-transparent" style="max-width: 500px; width: 100%;">
  <div class="card-body text-center">
    <div class="mb-4">
      <a href="{{ route('dashboard') }}" class="d-inline-block mb-4">
        @if ($appLogo)
          <img src="{{ $appLogo }}" alt="Logo" style="max-height: 48px; max-width: 180px; object-fit: contain;">
        @else
          <img src="{{ asset('assets/images/logo-icon.svg') }}" alt="" width="36">
          <span class="ms-2"><img src="{{ asset('assets/images/logo.svg') }}" alt=""></span>
        @endif
      </a>
    </div>

    <h1 class="display-1 fw-bold text-primary mb-2">404</h1>
    <h2 class="card-title h4 mb-3">Page Not Found</h2>
    <p class="text-muted mb-4">Sorry, the page you're looking for doesn't exist or has been moved.</p>

    <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
  </div>
</div>
@endsection
