<div id="layoutSidenav_nav">
  <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
      <div class="nav">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
          Users
        </a>

        <a class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}" href="{{ route('admin.questions') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-question"></i></div>
          Questions
        </a>

        <a class="nav-link {{ request()->routeIs('admin.polls.*') ? 'active' : '' }}" href="{{ route('admin.polls.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-poll"></i></div>
          Polls
        </a>

        <a class="nav-link {{ request()->routeIs('admin.login-details.*') ? 'active' : '' }}" href="{{ route('admin.login-details.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-history"></i></div>
          Login Details
        </a>

        <a class="nav-link {{ request()->routeIs('admin.previous-sessions.*') ? 'active' : '' }}" href="{{ route('admin.previous-sessions.index') }}">
          <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
          Previous Sessions
        </a>
        <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
  <div class="sb-nav-link-icon"><i class="fas fa-bullhorn"></i></div>
  Announcements
</a>
        <!-- Only show Admin User link for super admins -->
        @if(Auth::guard('admin')->user() && Auth::guard('admin')->user()->isSuperAdmin())
          <a class="nav-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}" href="{{ route('admin.admins.index') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-user-shield"></i></div>
            Admin Users
          </a>
        @endif
      </div>
    </div>
    <div class="sb-sidenav-footer">
      <div class="small">Logged in as:</div>
      {{ Auth::guard('admin')->user()->username ?? Auth::guard('admin')->user()->full_name }}
      <form method="POST" action="{{ route('admin.logout') }}" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-sm btn-danger w-100">Logout</button>
      </form>
    </div>
  </nav>
</div>