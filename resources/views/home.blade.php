@extends('layouts.app')

@section('content')
<div class="container">


    <!-- Genres -->
    @if($genres->count() > 0)
    <section class="mb-5">
        <h2 class="section-heading">browse by genre</h2>
        <div class="row g-3">
            @foreach($genres as $genre)
            <div class="col-md-3 col-sm-6 mb-3">
                <a href="{{ route('games.index', ['genre' => $genre->slug]) }}" class="text-decoration-none">
                    <div class="genre-card-neon">
                        <div class="genre-icon-circle">
                            <span class="genre-icon">🎮</span>
                        </div>
                        <h5 class="genre-title">{{ $genre->name }}</h5>
                        <p class="genre-count">{{ $genre->games_count ?? 0 }} games</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Featured Games -->
    @if($featuredGames->count() > 0)
    <section class="mb-5">
        <h2 class="section-heading">featured games</h2>
        <div class="row g-4">
            @foreach($featuredGames as $game)
            <div class="col-md-4">
                <div class="card game-card h-100">
                    @php
                        $previewPath = null;
                        if ($game->game_file_path) {
                            $gameDir = dirname($game->game_file_path);
                            if ($gameDir === '.' || $gameDir === '') {
                                $gameDir = pathinfo($game->game_file_path, PATHINFO_FILENAME);
                            }
                            $previewFile = public_path('games/' . $gameDir . '/preview.png');
                            if (file_exists($previewFile)) {
                                $previewPath = asset('games/' . $gameDir . '/preview.png');
                            }
                        }
                    @endphp
                    @if($game->image_url)
                    <img src="{{ $game->image_url }}" class="card-img-top" alt="{{ $game->title }}" style="height: 200px; object-fit: cover;">
                    @elseif($previewPath)
                    <img src="{{ $previewPath }}" class="card-img-top" alt="{{ $game->title }}" style="height: 200px; object-fit: cover;">
                    @else
                    <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background-color: #000000; border: 1px solid rgba(0, 234, 255, 0.2);">
                        <span class="text-muted">No Image</span>
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
        <h2 class="section-heading">latest games</h2>
        <div class="row g-4">
            @foreach($latestGames as $game)
            <div class="col-md-3 col-sm-6">
                <div class="card game-card h-100">
                    @php
                        $previewPath = null;
                        if ($game->game_file_path) {
                            $gameDir = dirname($game->game_file_path);
                            if ($gameDir === '.' || $gameDir === '') {
                                $gameDir = pathinfo($game->game_file_path, PATHINFO_FILENAME);
                            }
                            $previewFile = public_path('games/' . $gameDir . '/preview.png');
                            if (file_exists($previewFile)) {
                                $previewPath = asset('games/' . $gameDir . '/preview.png');
                            }
                        }
                    @endphp
                    @if($game->image_url)
                    <img src="{{ $game->image_url }}" class="card-img-top" alt="{{ $game->title }}" style="height: 150px; object-fit: cover;">
                    @elseif($previewPath)
                    <img src="{{ $previewPath }}" class="card-img-top" alt="{{ $game->title }}" style="height: 150px; object-fit: cover;">
                    @else
                    <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 150px; background-color: #000000; border: 1px solid rgba(0, 234, 255, 0.2);">
                        <span class="text-muted small">No Image</span>
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

</div>
@endsection
