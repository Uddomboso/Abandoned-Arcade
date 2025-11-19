@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">My Profile</h1>
        </div>
    </div>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-12 col-md-4 mb-4 mb-md-0">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: clamp(80px, 15vw, 100px); height: clamp(80px, 15vw, 100px); font-size: clamp(2rem, 5vw, 3rem);">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h4 style="font-size: clamp(1.25rem, 4vw, 1.5rem);">{{ $user->name }}</h4>
                    <p class="text-muted small">Member since {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>

        </div>

        <!-- Recent Games -->
        <div class="col-12 col-md-8">
            @if($saveStates->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h5>Recently Played</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($saveStates->take(6) as $saveState)
                        <div class="col-6 col-md-4 mb-3">
                            <div class="card">
                                @if($saveState->game->image_url)
                                <img src="{{ $saveState->game->image_url }}" class="card-img-top" alt="{{ $saveState->game->title }}" style="height: 120px; object-fit: cover;">
                                @else
                                <div class="bg-secondary" style="height: 120px;"></div>
                                @endif
                                <div class="card-body p-2">
                                    <h6 class="card-title small"><a href="{{ route('games.show', $saveState->game->id) }}" class="text-decoration-none">{{ Str::limit($saveState->game->title, 20) }}</a></h6>
                                    <small class="text-muted">{{ $saveState->last_played_at?->diffForHumans() ?? 'Never' }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

