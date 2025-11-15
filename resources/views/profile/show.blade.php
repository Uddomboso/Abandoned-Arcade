@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">👤 My Profile</h1>
        </div>
    </div>

    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 3rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <p class="text-muted small">Member since {{ $user->created_at->format('F Y') }}</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h5>My Stats</h5>
                </div>
                <div class="card-body">
                    <p><strong>Reviews Written:</strong> {{ $reviews->total() }}</p>
                    <p><strong>Games in Collection:</strong> {{ $saveStates->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>My Reviews</h5>
                    <a href="{{ route('games.index') }}" class="btn btn-sm btn-primary">Browse Games</a>
                </div>
                <div class="card-body">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <h6><a href="{{ route('games.show', $review->game->id) }}" class="text-decoration-none">{{ $review->game->title }}</a></h6>
                                <span class="badge bg-warning text-dark">{{ $review->rating }}/5</span>
                            </div>
                            @if($review->comment)
                            <p class="mb-2">{{ $review->comment }}</p>
                            @endif
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                        @endforeach

                        <div class="d-flex justify-content-center mt-3">
                            {{ $reviews->links() }}
                        </div>
                    @else
                    <p class="text-muted">You haven't written any reviews yet. <a href="{{ route('games.index') }}">Start reviewing games!</a></p>
                    @endif
                </div>
            </div>

            <!-- Recent Games -->
            @if($saveStates->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Recently Played</h5>
                    <a href="{{ route('profile.collection') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($saveStates->take(6) as $saveState)
                        <div class="col-md-4 mb-3">
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

