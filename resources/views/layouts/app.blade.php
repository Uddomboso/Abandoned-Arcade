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
                {{-- brand logo with neon glow effect --}}
                <a class="navbar-brand neon-glow" href="{{ route('home') }}">
                    Abandoned Arcade
                </a>
                {{-- mobile menu toggle button --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- left side navigation links --}}
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('games.index') }}">Games</a>
                        </li>
                        {{-- profile link only shown to authenticated users --}}
                        @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('profile.show') }}">Profile</a>
                        </li>
                        @endauth
                    </ul>

                    {{-- right side navigation (auth links) --}}
                    <ul class="navbar-nav ms-auto">
                        {{-- guest user display --}}
                        @guest
                            <li class="nav-item">
                                <span class="nav-link text-muted small">
                                    <span class="badge bg-secondary">Guest Mode</span>
                                    <small class="ms-1">Data saved in browser</small>
                                </span>
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
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">My Profile</a>
                                    <a class="dropdown-item" href="{{ route('profile.collection') }}">My Collection</a>
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
