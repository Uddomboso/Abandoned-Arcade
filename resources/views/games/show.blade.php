@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- Game Header -->
            <div class="card mb-4">
                <div class="row g-0">
                    <div class="col-md-4">
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
                    <div class="col-md-8">
                        <div class="card-body">
                            <h1 class="card-title">{{ $game->title }}</h1>
                            <p class="card-text">{{ $game->description }}</p>
                            <div class="mb-3">
                                <span class="badge bg-primary me-2">{{ $game->genre->name }}</span>
                                @if($game->rating > 0)
                                <span class="badge bg-warning text-dark me-2">{{ number_format($game->rating, 1) }} / 5.0 ({{ $game->rating_count }} reviews)</span>
                                @else
                                <span class="badge bg-secondary me-2">Not rated yet</span>
                                @endif
                                <span class="badge bg-info">{{ $game->play_count }} plays</span>
                            </div>
                            @if($game->developer)
                            <p class="card-text"><small class="text-muted">Developer: {{ $game->developer }}</small></p>
                            @endif
                            @if($game->publisher)
                            <p class="card-text"><small class="text-muted">Publisher: {{ $game->publisher }}</small></p>
                            @endif
                            @if($game->release_date)
                            <p class="card-text"><small class="text-muted">Released: {{ $game->release_date->format('F Y') }}</small></p>
                            @endif
                            <a href="{{ route('games.play', $game->id) }}" class="btn btn-primary btn-lg mt-3">Play Game</a>
                            @if($game->game_url && $game->source_type !== 'embedded')
                            <a href="{{ $game->game_url }}" target="_blank" class="btn btn-outline-primary btn-lg mt-3 ms-2">Open in New Tab</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Form -->
            @auth
            <div class="card mb-4">
                <div class="card-header">
                    <h3>Rate This Game</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @php
                        $userReview = $game->reviews->where('user_id', auth()->id())->first();
                    @endphp
                    
                    @if($userReview)
                        <div class="alert alert-info">
                            <strong>You already rated this game:</strong> {{ $userReview->rating }}/5
                            @if($userReview->comment)
                                <br><em>"{{ $userReview->comment }}"</em>
                            @endif
                        </div>
                    @else
                        <form id="ratingForm" method="POST" action="{{ route('reviews.store') }}">
                            @csrf
                            <input type="hidden" name="game_id" value="{{ $game->id }}">
                            
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select @error('rating') is-invalid @enderror" name="rating" id="rating" required>
                                    <option value="">Select a rating</option>
                                    <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 - Excellent</option>
                                    <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 - Very Good</option>
                                    <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 - Good</option>
                                    <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 - Fair</option>
                                    <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 - Poor</option>
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="comment" class="form-label">Review (Optional)</label>
                                <textarea class="form-control @error('comment') is-invalid @enderror" name="comment" id="comment" rows="3" maxlength="1000" placeholder="Share your thoughts about this game...">{{ old('comment') }}</textarea>
                                <small class="text-muted">Maximum 1000 characters</small>
                                @error('comment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Rating</button>
                        </form>
                    @endif
                </div>
            </div>
            @else
            <div class="card mb-4">
                <div class="card-body">
                    <p class="mb-2">Want to rate this game?</p>
                    <a href="{{ route('login') }}" class="btn btn-primary">Login to Rate</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Create Account</a>
                </div>
            </div>
            @endauth

            <!-- Reviews Section -->
            <div class="card">
                <div class="card-header">
                    <h3>Reviews ({{ $game->reviews->count() }})</h3>
                </div>
                <div class="card-body">
                    @if($game->reviews->count() > 0)
                        @foreach($game->reviews->take(10) as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ $review->user->name }}</strong>
                                <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                            </div>
                            @if($review->comment)
                            <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach
                    @else
                    <p class="text-muted">No reviews yet. Be the first to review!</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Related Games -->
            @if($relatedGames->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Related Games</h5>
                </div>
                <div class="card-body">
                    @foreach($relatedGames as $relatedGame)
                    <div class="d-flex mb-3">
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
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Game Stats -->
            <div class="card">
                <div class="card-header">
                    <h5>Game Stats</h5>
                </div>
                <div class="card-body">
                    <p><strong>Genre:</strong> {{ $game->genre->name }}</p>
                    <p><strong>Rating:</strong> {{ $game->rating > 0 ? number_format($game->rating, 1) . ' / 5.0' : 'Not rated yet' }}</p>
                    <p><strong>Total Reviews:</strong> {{ $game->rating_count }}</p>
                    <p><strong>Total Plays:</strong> {{ $game->play_count }}</p>
                    <p><strong>Added:</strong> {{ $game->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

