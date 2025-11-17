@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>All Games</h1>
        </div>
        <div class="col-md-4">
            <form method="GET" action="{{ route('games.index') }}" class="d-flex">
                <input class="form-control me-2" type="search" name="search" placeholder="Search games..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
        </div>
    </div>

    <!-- Genre Filter -->
    @if($genres->count() > 0)
    <div class="mb-4">
        <a href="{{ route('games.index') }}" class="badge bg-secondary text-decoration-none me-2 mb-2 {{ !request('genre') ? 'bg-primary' : '' }}">All</a>
        @foreach($genres as $genre)
        <a href="{{ route('games.index', ['genre' => $genre->slug]) }}" 
           class="badge bg-secondary text-decoration-none me-2 mb-2 {{ request('genre') == $genre->slug ? 'bg-primary' : '' }}">
            {{ $genre->name }}
        </a>
        @endforeach
    </div>
    @endif

    <!-- Games Grid -->
    @if($games->count() > 0)
    <div class="row">
        @foreach($games as $game)
        <div class="col-md-3 mb-4">
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
                    <h5 class="card-title">{{ Str::limit($game->title, 30) }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($game->description, 80) }}</p>
                    <div class="mb-2">
                        <span class="badge bg-primary">{{ $game->genre->name }}</span>
                        @if($game->rating > 0)
                        <span class="badge bg-warning text-dark">{{ number_format($game->rating, 1) }}</span>
                        @endif
                        <span class="badge bg-info">{{ $game->play_count }} plays</span>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('games.show', $game->id) }}" class="btn btn-sm btn-primary w-100">Play Now</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $games->links() }}
    </div>
    @else
    <div class="alert alert-info">
        <h4>No games found</h4>
        <p>Try adjusting your search or filters.</p>
    </div>
    @endif
</div>
@endsection

