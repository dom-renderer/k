<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'Authentication - ' . \App\Models\Setting::get('app_title', 'InApp Inventory'))</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @php
    $appFavicon = \App\Models\Setting::get('app_favicon');
    $primaryColor = \App\Models\Setting::get('primary_color', '#2563eb');
    $primaryTextColor = \App\Models\Setting::get('primary_text_color', '#ffffff');
    $rgbValues = sscanf($primaryColor, "#%02x%02x%02x");
    $rgbStr = is_array($rgbValues) && count($rgbValues) === 3 ? implode(',', $rgbValues) : '37,99,235';
  @endphp

  @if ($appFavicon)
    <link rel="icon" type="image/png" href="{{ $appFavicon }}">
  @else
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon_io/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/images/favicon_io/site.webmanifest') }}">
  @endif

  <!-- Dynamic Theme Color Overrides -->
  <style>
    :root {
      --bs-primary: {{ $primaryColor }} !important;
      --bs-primary-rgb: {{ $rgbStr }} !important;
      --bs-primary-bg-subtle: {{ $primaryColor }}18 !important;
      --bs-primary-text-emphasis: {{ $primaryTextColor }} !important;
      --bs-link-color: {{ $primaryColor }} !important;
      --bs-link-hover-color: {{ $primaryColor }} !important;
    }

    /* Buttons */
    .btn-primary {
      background-color: {{ $primaryColor }} !important;
      border-color: {{ $primaryColor }} !important;
      color: {{ $primaryTextColor }} !important;
    }
    .btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active {
      background-color: {{ $primaryColor }} !important;
      border-color: {{ $primaryColor }} !important;
      filter: brightness(0.9);
      color: {{ $primaryTextColor }} !important;
    }

    /* Backgrounds & Text */
    .bg-primary {
      background-color: {{ $primaryColor }} !important;
      color: {{ $primaryTextColor }} !important;
    }
    .text-primary, .link-primary {
      color: {{ $primaryColor }} !important;
    }

    /* Form Controls Focus State */
    .form-control:focus,
    .form-select:focus {
      border-color: {{ $primaryColor }} !important;
      box-shadow: 0 0 0 0.25rem {{ $primaryColor }}35 !important;
    }

    /* Checkboxes, Radios, & Switches */
    .form-check-input:checked {
      background-color: {{ $primaryColor }} !important;
      border-color: {{ $primaryColor }} !important;
    }
    .form-check-input:focus {
      border-color: {{ $primaryColor }} !important;
      box-shadow: 0 0 0 0.25rem {{ $primaryColor }}35 !important;
    }
  </style>

  @vite(['resources/scss/style.scss', 'resources/js/main.js'])
  @stack('styles')
</head>

<body class="bg-light">
  <div class="container d-flex flex-column justify-content-center align-items-center min-vh-100 py-5">
    @yield('content')
  </div>

  @stack('scripts')
</body>

</html>
