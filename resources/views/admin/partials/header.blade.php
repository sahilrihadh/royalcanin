<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
  <!-- Navbar Brand-->
  <a class="navbar-brand ps-3" href="{{ route('admin.dashboard') }}">
    <img src="{{ asset('assets/img/rc-logo.png') }}" width="100px" class="img-fluid" alt="Royal Canin">
  </a>
  <!-- Sidebar Toggle-->
  <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

  <!-- Logged-in admin + logout -->
  <div class="ml-auto flex items-center gap-3 pr-3">
    <span class="hidden sm:inline text-sm text-[#09090b] opacity-70">
      {{ Auth::guard('admin')->user()->username ?? Auth::guard('admin')->user()->full_name }}
    </span>
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg">
        <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Logout</span>
      </button>
    </form>
  </div>
</nav>
