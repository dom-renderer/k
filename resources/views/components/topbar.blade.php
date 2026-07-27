<!-- TOPBAR COMPONENT -->
@php
  $user = auth()->user();
  $userAvatar = ($user && $user->profile_picture_url) ? $user->profile_picture_url : asset('assets/images/avatar/avatar-1.jpg');
@endphp
<nav id="topbar" class="navbar bg-white border-bottom fixed-top topbar px-3">
  <button id="toggleBtn" class="d-none d-lg-inline-flex btn btn-light btn-icon btn-sm">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>

  <!-- MOBILE -->
  <button id="mobileBtn" class="btn btn-light btn-icon btn-sm d-lg-none me-2">
    <i class="ti ti-layout-sidebar-left-expand"></i>
  </button>

  <div class="ms-auto">
    <!-- Navbar nav -->
    <ul class="list-unstyled d-flex align-items-center mb-0 gap-2">
      <!-- Profile Dropdown -->
      <li class="dropdown">
        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
          <img src="{{ $userAvatar }}" alt="Profile" class="avatar avatar-sm rounded-circle border" style="width: 36px; height: 36px; object-fit: cover;" />
          <span class="fw-semibold text-dark d-none d-md-inline-block">{{ $user->name ?? 'User' }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-0 mt-2" style="min-width: 220px;">
          <div>
            <div class="d-flex gap-3 align-items-center border-bottom px-3 py-3 bg-light rounded-top">
              <img src="{{ $userAvatar }}" alt="" class="avatar avatar-md rounded-circle border" style="width: 42px; height: 42px; object-fit: cover;" />
              <div>
                <h6 class="mb-0 fw-bold text-dark">{{ $user->name ?? 'Guest User' }}</h6>
                <small class="text-muted d-block">{{ '@' . ($user->username ?? 'user') }}</small>
                @if($user && $user->roles->count())
                  <span class="badge bg-primary-subtle text-primary mt-1">{{ ucfirst($user->roles->first()->name) }}</span>
                @endif
              </div>
            </div>
            <div class="p-2 d-flex flex-column gap-1 small">
              <a href="{{ route('dashboard') }}" class="dropdown-item py-2 rounded">
                <i class="ti ti-home me-2"></i> Dashboard
              </a>
              @can('setting-list')
                <a href="{{ route('settings.index') }}" class="dropdown-item py-2 rounded">
                  <i class="ti ti-settings me-2"></i> App Settings
                </a>
              @endcan
              @auth
                <div class="dropdown-divider my-1"></div>
                <form method="POST" action="{{ route('auth.logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item py-2 rounded text-danger fw-semibold">
                    <i class="ti ti-logout me-2"></i> Log Out
                  </button>
                </form>
              @endauth
            </div>
          </div>
        </div>
      </li>
    </ul>
  </div>
</nav>
