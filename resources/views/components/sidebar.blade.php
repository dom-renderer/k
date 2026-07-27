<!-- SIDEBAR COMPONENT -->
@php
  $appLogo = \App\Models\Setting::get('app_logo');
@endphp
<aside id="sidebar" class="sidebar">
  <div class="logo-area">
    <a href="{{ route('dashboard') }}" class="d-inline-flex align-items-center">
      @if ($appLogo)
        <img src="{{ $appLogo }}" alt="App Logo" style="max-height: 32px; max-width: 140px; object-fit: contain;">
      @else
        <img src="{{ asset('assets/images/logo-icon.svg') }}" alt="" width="24">
        <span class="logo-text ms-2"><img src="{{ asset('assets/images/logo.svg') }}" alt=""></span>
      @endif
    </a>
  </div>
  <ul class="nav flex-column">
    <li class="px-4 py-2"><small class="nav-text">Main</small></li>
    <li>
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="ti ti-home"></i><span class="nav-text">Dashboard</span>
      </a>
    </li>

    @can('inventory-list')
    <li>
      <a class="nav-link {{ request()->routeIs('inventory.index') ? 'active' : '' }}" href="{{ route('inventory.index') }}">
        <i class="ti ti-box-seam"></i><span class="nav-text">Inventory</span>
      </a>
    </li>
    @endcan

    @can('user-list')
    <li>
      <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
        <i class="ti ti-users"></i><span class="nav-text">Users</span>
      </a>
    </li>
    @endcan

    @can('role-list')
    <li>
      <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
        <i class="ti ti-shield"></i><span class="nav-text">Roles</span>
      </a>
    </li>
    @endcan

    @can('sector-list')
    <li>
      <a class="nav-link {{ request()->routeIs('sectors.*') ? 'active' : '' }}" href="{{ route('sectors.index') }}">
        <i class="ti ti-category"></i><span class="nav-text">Sectors</span>
      </a>
    </li>
    @endcan

    @can('equipment-list')
    <li>
      <a class="nav-link {{ request()->routeIs('equipment.*') ? 'active' : '' }}" href="{{ route('equipment.index') }}">
        <i class="ti ti-tools"></i><span class="nav-text">Equipment</span>
      </a>
    </li>
    @endcan

    @can('inventory-create')
    <li>
      <a class="nav-link {{ request()->routeIs('products.create') ? 'active' : '' }}" href="{{ route('products.create') }}">
        <i class="ti ti-plus"></i><span class="nav-text">Add Product</span>
      </a>
    </li>
    @endcan

    @can('report-list')
    <li>
      <a class="nav-link {{ request()->routeIs('reports.index') ? 'active' : '' }}" href="{{ route('reports.index') }}">
        <i class="ti ti-receipt"></i><span class="nav-text">Reports</span>
      </a>
    </li>
    @endcan

    @can('setting-list')
    <li>
      <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
        <i class="ti ti-settings"></i><span class="nav-text">Settings</span>
      </a>
    </li>
    @endcan

    <li>
      <a class="nav-link {{ request()->routeIs('error.404') ? 'active' : '' }}" href="{{ route('error.404') }}">
        <i class="ti ti-alert-circle"></i><span class="nav-text">404 Error</span>
      </a>
    </li>
    <li>
      <a class="nav-link {{ request()->routeIs('docs.index') ? 'active' : '' }}" href="{{ route('docs.index') }}">
        <i class="ti ti-file-text"></i><span class="nav-text">Docs</span>
      </a>
    </li>

    <li class="px-4 pt-4 pb-2"><small class="nav-text">Account</small></li>
    @auth
      <li>
        <form method="POST" action="{{ route('auth.logout') }}">
          @csrf
          <button type="submit" class="nav-link bg-transparent border-0 w-100 text-start">
            <i class="ti ti-logout"></i><span class="nav-text">Log out</span>
          </button>
        </form>
      </li>
    @else
      <li>
        <a class="nav-link {{ request()->routeIs('auth.login') ? 'active' : '' }}" href="{{ route('auth.login') }}">
          <i class="ti ti-login"></i><span class="nav-text">Log in</span>
        </a>
      </li>
    @endauth
  </ul>
</aside>
