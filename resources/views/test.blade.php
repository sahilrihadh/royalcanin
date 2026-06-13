<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}" class="text-xl font-bold text-gray-800">
                        Webinar Solution
                    </a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-gray-600">
                        {{ Auth::user() ? (Auth::user()->full_name ?: Auth::user()->email_id) : 'User' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Register
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>