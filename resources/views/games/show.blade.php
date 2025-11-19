@extends('layouts.app')

@section('content')
<div class="container px-2 px-md-3">
    <div class="row g-0 g-md-3">
        <div class="col-12 col-md-8">
            <!-- Game Header -->
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-12 col-md-4">
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
                        <img src="{{ $game->image_url }}" class="img-fluid rounded-start" alt="{{ $game->title }}" style="height: 100%; object-fit: cover;">
                        @elseif($previewPath)
                        <img src="{{ $previewPath }}" class="img-fluid rounded-start" alt="{{ $game->title }}" style="height: 100%; object-fit: cover;">
                        @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 100%; min-height: 300px;">
                            <span class="text-white">No Image</span>
                        </div>
                        @endif
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="card-body">
                            <h1 class="card-title text-white" style="font-size: clamp(1.5rem, 4vw, 2.5rem);">{{ $game->title }}</h1>
                            <p class="card-text text-white" style="font-size: clamp(0.875rem, 2vw, 1rem); line-height: 1.6;">{{ $game->description }}</p>
                            <div class="mb-3">
                                <span class="badge bg-primary me-2 mb-1">{{ $game->genre->name }}</span>
                                <span class="badge bg-info mb-1">{{ $game->play_count }} plays</span>
                            </div>
                            @if($game->developer)
                            <p class="card-text"><small class="text-white-50" style="font-size: clamp(0.75rem, 2vw, 0.9rem);">Developer: {{ $game->developer }}</small></p>
                            @endif
                            @if($game->publisher)
                            <p class="card-text"><small class="text-white-50" style="font-size: clamp(0.75rem, 2vw, 0.9rem);">Publisher: {{ $game->publisher }}</small></p>
                            @endif
                            @if($game->release_date)
                            <p class="card-text"><small class="text-white-50" style="font-size: clamp(0.75rem, 2vw, 0.9rem);">Released: {{ $game->release_date->format('F Y') }}</small></p>
                            @endif
                            <div class="d-flex flex-column flex-sm-row gap-2 mt-3">
                                <a href="{{ route('games.play', $game->id) }}" class="btn btn-primary btn-lg">Play Game</a>
                                @if($game->game_url && $game->source_type !== 'embedded')
                                <a href="{{ $game->game_url }}" target="_blank" class="btn btn-outline-primary btn-lg">Open in New Tab</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @guest
            <div class="card mb-4">
                <div class="card-body">
                    <p class="mb-2">Want to see your rank?</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Sign up</a>
                </div>
            </div>
            @endguest
        </div>

        <!-- Sidebar -->
        <div class="col-12 col-md-4 mt-4 mt-md-0">
            <!-- Related Games -->
            @if($relatedGame)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Related Games</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        @php
                            $relatedPreviewPath = null;
                            if ($relatedGame->game_file_path) {
                                $relatedGameDir = dirname($relatedGame->game_file_path);
                                if ($relatedGameDir === '.' || $relatedGameDir === '') {
                                    $relatedGameDir = pathinfo($relatedGame->game_file_path, PATHINFO_FILENAME);
                                }
                                $relatedPreviewFile = public_path('games/' . $relatedGameDir . '/preview.png');
                                if (file_exists($relatedPreviewFile)) {
                                    $relatedPreviewPath = asset('games/' . $relatedGameDir . '/preview.png');
                                } else {
                                    $relatedParentDir = dirname($relatedGameDir);
                                    if ($relatedParentDir !== '.' && $relatedParentDir !== '') {
                                        $relatedParentPreviewFile = public_path('games/' . $relatedParentDir . '/preview.png');
                                        if (file_exists($relatedParentPreviewFile)) {
                                            $relatedPreviewPath = asset('games/' . $relatedParentDir . '/preview.png');
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if($relatedGame->image_url)
                        <img src="{{ $relatedGame->image_url }}" class="me-3" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $relatedGame->title }}">
                        @elseif($relatedPreviewPath)
                        <img src="{{ $relatedPreviewPath }}" class="me-3" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $relatedGame->title }}">
                        @else
                        <div class="bg-secondary me-3" style="width: 80px; height: 80px;"></div>
                        @endif
                        <div>
                            <h6><a href="{{ route('games.show', $relatedGame->id) }}" class="text-decoration-none">{{ Str::limit($relatedGame->title, 25) }}</a></h6>
                            @if($relatedGame->rating > 0)
                            <small class="text-muted">{{ number_format($relatedGame->rating, 1) }}/5.0</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Top Players -->
            <div class="card">
                <div class="card-header">
                    <h5>Top Players</h5>
                </div>
                <div class="card-body">
                    @if($leaderboard->count() > 0)
                        <ol class="list-group list-group-numbered">
                            @foreach($leaderboard as $index => $player)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $player->user->name }}</div>
                                        <small class="text-muted">
                                            {{ $player->play_count }} {{ Str::plural('play', $player->play_count) }}
                                            @if($player->last_played)
                                                • {{ \Carbon\Carbon::parse($player->last_played)->diffForHumans() }}
                                            @endif
                                        </small>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">#{{ $index + 1 }}</span>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-muted mb-0">No players yet. Be the first to play!</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

