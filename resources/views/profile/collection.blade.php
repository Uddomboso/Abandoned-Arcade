@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>📚 My Game Collection</h1>
            <p class="text-muted">All games you've played and saved</p>
        </div>
    </div>

    @if($saveStates->count() > 0)
    <div class="row">
        @foreach($saveStates as $saveState)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
                @if($saveState->game->image_url)
                <img src="{{ $saveState->game->image_url }}" class="card-img-top" alt="{{ $saveState->game->title }}" style="height: 200px; object-fit: cover;">
                @else
                <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                    <span class="text-white">No Image</span>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ Str::limit($saveState->game->title, 25) }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($saveState->game->description, 60) }}</p>
                    <div class="mb-2">
                        <span class="badge bg-primary">{{ $saveState->game->genre->name }}</span>
                        @if($saveState->save_name)
                        <span class="badge bg-info">{{ $saveState->save_name }}</span>
                        @endif
                    </div>
                    <small class="text-muted">Last played: {{ $saveState->last_played_at?->diffForHumans() ?? 'Never' }}</small>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="{{ route('games.show', $saveState->game->id) }}" class="btn btn-sm btn-primary w-100">Continue Playing</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $saveStates->links() }}
    </div>
    @else
    <div class="alert alert-info">
        <h4>Your collection is empty</h4>
        <p>Start playing games to build your collection! <a href="{{ route('games.index') }}" class="alert-link">Browse games</a></p>
    </div>
    @endif
</div>
@endsection

