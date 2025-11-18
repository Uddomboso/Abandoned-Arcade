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
                        <h5 class="genre-title">{{ $genre->name }}</h5>
                        <p class="genre-count">{{ $genre->games_count ?? 0 }} games</p>
                    </div>
                </a>
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
                            // Check in game directory first
                            $previewFile = public_path('games/' . $gameDir . '/preview.png');
                            if (file_exists($previewFile)) {
                                $previewPath = asset('games/' . $gameDir . '/preview.png');
                            } else {
                                // Check in parent directory (for games like puzzlem/puzzle/index.html)
                                $parentDir = dirname($gameDir);
                                if ($parentDir !== '.' && $parentDir !== '') {
                                    $parentPreviewFile = public_path('games/' . $parentDir . '/preview.png');
                                    if (file_exists($parentPreviewFile)) {
                                        $previewPath = asset('games/' . $parentDir . '/preview.png');
                                    }
                                }
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
