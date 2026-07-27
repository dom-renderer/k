<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="UTF-8" />
  <title>@yield('title', \App\Models\Setting::get('app_title', 'InApp Inventory Dashboard'))</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

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
    .btn-outline-primary {
      color: {{ $primaryColor }} !important;
      border-color: {{ $primaryColor }} !important;
    }
    .btn-outline-primary:hover {
      background-color: {{ $primaryColor }} !important;
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
    .bg-primary-subtle, .bg-light-primary {
      background-color: {{ $primaryColor }}18 !important;
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

    /* Sidebar Navigation Links & Active / Hover States */
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
      color: {{ $primaryColor }} !important;
      background-color: {{ $primaryColor }}18 !important;
    }
    .sidebar .nav-link:hover .ti,
    .sidebar .nav-link.active .ti {
      color: {{ $primaryColor }} !important;
    }

    /* DataTables & Pagination */
    .page-item.active .page-link {
      background-color: {{ $primaryColor }} !important;
      border-color: {{ $primaryColor }} !important;
      color: {{ $primaryTextColor }} !important;
    }
    .page-link {
      color: {{ $primaryColor }};
    }

    .iti { width: 100%; }
    .error { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
    .select2-container--bootstrap-5 { width: 100% !important; }
  </style>

  <!-- DataTables CSS, intl-tel-input CSS & Select2 CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css">

  @vite(['resources/scss/style.scss', 'resources/js/main.js'])
  @stack('styles')
</head>

<body>
  <div id="overlay" class="overlay"></div>

  <x-topbar />
  <x-sidebar />

  <!-- MAIN CONTENT -->
  <main id="content" class="content py-10">
    <div class="container-fluid">
      @yield('content')
      <x-footer />
    </div>
  </main>

  <!-- jQuery, DataTables, intl-tel-input, Select2, jQuery Validate, SweetAlert2 -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  @stack('scripts')
</body>

</html>
