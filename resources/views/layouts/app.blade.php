{{-- main application layout template --}}
{{-- provides base html structure, navigation, and content area --}}
{{-- includes dark theme styling and neon blue accents --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- csrf token for laravel security --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    {{-- fonts --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- compiled assets via vite --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body data-user-authenticated="{{ auth()->check() ? 'true' : 'false' }}" style="background-color: #000000;">
    <div id="app">
        {{-- main navigation bar --}}
        <nav class="navbar navbar-expand-md navbar-dark shadow-sm">
            <div class="container">
                {{-- brand logo with icon --}}
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('assets/icon.png') }}" alt="Abandoned Arcade" style="height: 40px;">
                </a>
                {{-- mobile menu toggle button --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- search bar with autocomplete --}}
                    <div class="navbar-search-wrapper w-100 w-md-auto me-auto me-md-3 mb-2 mb-md-0" style="max-width: 100%; position: relative;">
                        <form method="GET" action="{{ route('games.index') }}" class="d-flex" id="navbar-search-form">
                            <input 
                                id="navbar-search-input"
                                class="form-control form-control-sm" 
                                type="search" 
                                name="search" 
                                placeholder="Search games..." 
                                value="{{ request('search') }}" 
                                aria-label="Search games"
                                autocomplete="off">
                            <button class="btn btn-outline-primary btn-sm ms-2" type="submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                </svg>
                            </button>
                        </form>
                        {{-- autocomplete dropdown --}}
                        <div id="navbar-search-dropdown" class="search-dropdown" style="display: none;"></div>
                    </div>
                    
                    {{-- right side navigation --}}
                    <ul class="navbar-nav ms-auto">
                        {{-- guest user display --}}
                        @guest
                            <li class="nav-item">
                                <span class="nav-link text-muted small" id="guest-display">
                                    <span class="badge bg-secondary">Guest</span>
                                    <small class="ms-1" id="guest-id">...</small>
                                </span>
                            </li>
                            {{-- home link --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            {{-- login link --}}
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            {{-- register link --}}
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        {{-- authenticated user display --}}
                        @else
                            {{-- home link --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('home') }}">Home</a>
                            </li>
                            {{-- profile link --}}
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.show') }}">Profile</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">My Profile</a>
                                    <div class="dropdown-divider"></div>
                                    {{-- logout link with form submission --}}
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    {{-- hidden logout form for csrf protection --}}
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        {{-- main content area --}}
        {{-- dark background with minimum height for full page coverage --}}
        <main class="py-4" style="background-color: #000000; min-height: calc(100vh - 56px);">
            @yield('content')
        </main>
    </div>
</body>
</html>
