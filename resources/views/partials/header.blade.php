<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Hello, {{ Auth::user()->full_name ?? Auth::user()->name }}</a>

            <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('webcast') ? 'active' : '' }}" href="{{ route('webcast') }}">Live Session</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('previous-sessions') ? 'active' : '' }}" href="{{ route('previous-sessions') }}">Previous Session</a>
                </li>
            </ul>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-logout">
                    <i class="bi bi-person-circle"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</header>