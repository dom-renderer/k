<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <title>@yield('title', 'Authentication - InApp Inventory')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/images/favicon_io/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/images/favicon_io/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/images/favicon_io/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('assets/images/favicon_io/site.webmanifest') }}">

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
