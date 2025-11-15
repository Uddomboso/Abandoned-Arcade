@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="jumbotron bg-dark text-white rounded p-5 mb-5 mt-4 neon-border">
        <h1 class="display-4 neon-glow">Welcome to Abandoned Arcade</h1>
        <p class="lead">Discover and play amazing retro games. Build your collection, share reviews, and relive the classics!</p>
        <a class="btn btn-primary btn-lg" href="{{ route('games.index') }}" role="button">Browse Games</a>
    </div>

    <!-- Featured Games -->
    @if($featuredGames->count() > 0)
    <section class="mb-5">
        <h2 class="mb-4" style="color: #00D9FF;">Featured Games</h2>
        <div class="row">
            @foreach($featuredGames as $game)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($game->image_url)
                    <img src="{{ $game->image_url }}" class="card-img-top" alt="{{ $game->title }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                        <span class="text-white">No Image</span>
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $game->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($game->description, 100) }}</p>
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $game->genre->name }}</span>
                            @if($game->rating > 0)
                            <span class="badge bg-warning text-dark">{{ number_format($game->rating, 1) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('games.show', $game->id) }}" class="btn btn-sm btn-primary">View Game</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Latest Games -->
    @if($latestGames->count() > 0)
    <section class="mb-5">
        <h2 class="mb-4" style="color: #00D9FF;">Latest Games</h2>
        <div class="row">
            @foreach($latestGames as $game)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($game->image_url)
                    <img src="{{ $game->image_url }}" class="card-img-top" alt="{{ $game->title }}" style="height: 150px; object-fit: cover;">
                    @else
                    <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 150px;">
                        <span class="text-white small">No Image</span>
                    </div>
                    @endif
                    <div class="card-body">
                        <h6 class="card-title">{{ Str::limit($game->title, 30) }}</h6>
                        <p class="card-text small text-muted">{{ Str::limit($game->description, 60) }}</p>
                        <div class="mb-2">
                            <span class="badge bg-secondary small">{{ $game->genre->name }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent p-2">
                        <a href="{{ route('games.show', $game->id) }}" class="btn btn-sm btn-outline-primary w-100">View</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @else
    <div class="alert alert-info">
        <h4>No games yet!</h4>
        <p>Check back soon for amazing games to play.</p>
    </div>
    @endif

    <!-- Genres -->
    @if($genres->count() > 0)
    <section class="mb-5">
        <h2 class="mb-4" style="color: #00D9FF;">Browse by Genre</h2>
        <div class="row">
            @foreach($genres as $genre)
            <div class="col-md-3 mb-3">
                <a href="{{ route('games.index', ['genre' => $genre->slug]) }}" class="text-decoration-none">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $genre->name }}</h5>
                            <p class="card-text text-muted small">{{ $genre->games_count ?? 0 }} games</p>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
