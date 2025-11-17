{{-- game play page view --}}
{{-- displays game in iframe or ruffle player based on game type --}}
{{-- handles keyboard event forwarding for iframe games --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{-- game player container --}}
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $game->title }}</h4>
                    <a href="{{ route('games.show', $game->id) }}" class="btn btn-sm btn-outline-secondary">← Back to Game</a>
                </div>
                <div class="card-body p-0" style="position: relative;">
                    {{-- game container with black background --}}
                    <div id="game-container" style="width: 100%; min-height: 800px; background: #000; position: relative; overflow: hidden;">
                        {{-- flash game with ruffle player --}}
                        {{-- uses ruffle to play swf flash files in modern browsers --}}
                        @if($game->source_type === 'ruffle' || ($game->game_file_path && str_ends_with($game->game_file_path, '.swf')))
                            <div id="ruffle-container" style="width: 100%; height: 600px;"></div>
                            <script src="https://unpkg.com/@ruffle-rs/ruffle@latest/dist/ruffle.js"></script>
                            <script>
                                window.RufflePlayer = window.RufflePlayer || {};
                                window.addEventListener("DOMContentLoaded", (event) => {
                                    const ruffle = window.RufflePlayer.newest();
                                    const player = ruffle.createPlayer();
                                    const container = document.getElementById("ruffle-container");
                                    container.appendChild(player);
                                    @if($game->game_file_path)
                                        player.load("{{ asset('games/' . $game->game_file_path) }}");
                                    @elseif($game->game_url)
                                        player.load("{{ $game->game_url }}");
                                    @endif
                                });
                            </script>
                        {{-- html5 or embedded game --}}
                        {{-- loads game in iframe for isolation and security --}}
                        @elseif($game->source_type === 'html5' || $game->source_type === 'embedded')
                            @if($game->game_file_path)
                                {{-- local game file in iframe --}}
                                <iframe 
                                    id="game-iframe"
                                    src="{{ asset('games/' . $game->game_file_path) }}" 
                                    width="100%" 
                                    height="800" 
                                    frameborder="0"
                                    allowfullscreen
                                    allow="pointer-events; autoplay; fullscreen"
                                    style="border: none; display: block; margin: 0; padding: 0; width: 100%; height: 800px;"
                                    tabindex="0"
                                    scrolling="no">
                                </iframe>
                                <script>
                                    {{-- iframe keyboard and mouse event handling --}}
                                    {{-- ensures iframe can receive keyboard input and mouse events for game controls --}}
                                    const gameIframe = document.getElementById('game-iframe');
                                    
                                    {{-- forward keyboard events to iframe when loaded --}}
                                    gameIframe.addEventListener('load', function() {
                                        console.log('Game iframe loaded');
                                        
                                        {{-- Wait a bit for game to initialize --}}
                                        setTimeout(function() {
                                            {{-- ensure iframe content is ready and can receive events --}}
                                            try {
                                                const iframeDoc = gameIframe.contentDocument || gameIframe.contentWindow.document;
                                                const iframeWindow = gameIframe.contentWindow;
                                                const iframeBody = iframeDoc ? iframeDoc.body : null;
                                                
                                                if (iframeBody) {
                                                    iframeBody.style.userSelect = 'none';
                                                    iframeBody.style.touchAction = 'none';
                                                    console.log('Iframe body styles applied');
                                                    
                                                    {{-- Check if game script loaded --}}
                                                    const scripts = iframeDoc.querySelectorAll('script');
                                                    console.log('Scripts in iframe:', scripts.length);
                                                    
                                                    {{-- Check for puzzle elements after game starts --}}
                                                    setTimeout(function() {
                                                        const puzzleElements = iframeDoc.querySelectorAll('[class*="piece"], [data-id], .piece, [class*="Piece"]');
                                                        console.log('Found puzzle elements:', puzzleElements.length);
                                                        
                                                        {{-- Check if game initialized by looking for game container --}}
                                                        const gameContainer = iframeDoc.querySelector('.container, .board, .drawer');
                                                        console.log('Game container found:', !!gameContainer);
                                                        
                                                        {{-- Test mouse events --}}
                                                        iframeBody.addEventListener('mousedown', function(e) {
                                                            console.log('Mouse down in iframe:', e.target, e.clientX, e.clientY);
                                                        }, true);
                                                        
                                                        {{-- Intercept console errors from game --}}
                                                        if (iframeWindow.console && iframeWindow.console.error) {
                                                            const originalError = iframeWindow.console.error;
                                                            iframeWindow.console.error = function(...args) {
                                                                console.error('[Game]', ...args);
                                                                originalError.apply(iframeWindow.console, args);
                                                            };
                                                        }
                                                    }, 2000);
                                                }
                                                
                                                console.log('Iframe document accessed successfully');
                                            } catch (e) {
                                                console.error('Cannot access iframe document:', e);
                                            }
                                        }, 500);
                                        
                                        {{-- forward keydown events to iframe --}}
                                        window.addEventListener('keydown', function(e) {
                                            {{-- only forward if iframe is visible and focused --}}
                                            if (document.activeElement === gameIframe || 
                                                gameIframe.getBoundingClientRect().top < window.innerHeight) {
                                                try {
                                                    const iframeWindow = gameIframe.contentWindow;
                                                    if (iframeWindow) {
                                                        iframeWindow.dispatchEvent(new KeyboardEvent('keydown', {
                                                            keyCode: e.keyCode,
                                                            key: e.key,
                                                            code: e.code,
                                                            bubbles: true,
                                                            cancelable: true
                                                        }));
                                                    }
                                                } catch (err) {
                                                    {{-- cross origin restrictions might prevent event forwarding --}}
                                                    console.log('Cannot forward events to iframe');
                                                }
                                            }
                                        });
                                        
                                        {{-- forward keyup events to iframe --}}
                                        window.addEventListener('keyup', function(e) {
                                            if (document.activeElement === gameIframe || 
                                                gameIframe.getBoundingClientRect().top < window.innerHeight) {
                                                try {
                                                    const iframeWindow = gameIframe.contentWindow;
                                                    if (iframeWindow) {
                                                        iframeWindow.dispatchEvent(new KeyboardEvent('keyup', {
                                                            keyCode: e.keyCode,
                                                            key: e.key,
                                                            code: e.code,
                                                            bubbles: true,
                                                            cancelable: true
                                                        }));
                                                    }
                                                } catch (err) {
                                                    console.log('Cannot forward events to iframe');
                                                }
                                            }
                                        });
                                        
                                        console.log('Game iframe loaded and ready');
                                    });
                                    
                                    {{-- focus iframe immediately to ensure it can receive events --}}
                                    setTimeout(function() {
                                        gameIframe.focus();
                                        console.log('Iframe focused');
                                    }, 100);
                                    
                                    {{-- focus iframe when clicked to enable keyboard input --}}
                                    {{-- don't stop propagation - let events reach iframe naturally --}}
                                    gameIframe.addEventListener('click', function() {
                                        gameIframe.focus();
                                    });
                                    
                                    {{-- test if mouse events are reaching the iframe --}}
                                    gameIframe.addEventListener('mouseenter', function() {
                                        console.log('Mouse entered iframe');
                                    });
                                    
                                    gameIframe.addEventListener('mousedown', function() {
                                        console.log('Mouse down on iframe');
                                        gameIframe.focus();
                                    });
                                    
                                    {{-- log for debugging --}}
                                    console.log('Game iframe initialized, ready for mouse events');
                                </script>
                            @elseif($game->game_url)
                                {{-- external game url in iframe --}}
                                <iframe 
                                    src="{{ $game->game_url }}" 
                                    width="100%" 
                                    height="600" 
                                    frameborder="0"
                                    allowfullscreen
                                    style="border: none;">
                                </iframe>
                            @else
                                {{-- no game file available --}}
                                <div class="text-white text-center">
                                    <p>Game file not available</p>
                                    <a href="{{ route('games.show', $game->id) }}" class="btn btn-primary">Go Back</a>
                                </div>
                            @endif
                        @else
                            {{-- default fallback: try iframe with game url --}}
                            @if($game->game_url)
                                <iframe 
                                    src="{{ $game->game_url }}" 
                                    width="100%" 
                                    height="600" 
                                    frameborder="0"
                                    allowfullscreen>
                                </iframe>
                            @else
                                {{-- no game available --}}
                                <div class="text-white text-center">
                                    <p>Game not available</p>
                                    <a href="{{ route('games.show', $game->id) }}" class="btn btn-primary">Go Back</a>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- game information sidebar --}}
    <div class="row mt-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Game Information</h5>
                </div>
                <div class="card-body">
                    <p><strong>Description:</strong> {{ $game->description }}</p>
                    @if($game->developer)
                    <p><strong>Developer:</strong> {{ $game->developer }}</p>
                    @endif
                    {{-- display preservation log information if available --}}
                    @if($game->preservationLog->first())
                        @php $log = $game->preservationLog->first(); @endphp
                        @if($log->release_year)
                        <p><strong>Original Release:</strong> {{ $log->release_year }}</p>
                        @endif
                        @if($log->platform)
                        <p><strong>Platform:</strong> {{ $log->platform }}</p>
                        @endif
                        @if($log->preservation_notes)
                        <p><strong>Preservation Notes:</strong> {{ $log->preservation_notes }}</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    @auth
                    {{-- authenticated user actions --}}
                    <button class="btn btn-primary w-100 mb-2" onclick="saveGameState()">Save Progress</button>
                    <a href="{{ route('games.show', $game->id) }}" class="btn btn-outline-primary w-100 mb-2">Rate & Review</a>
                    @else
                    {{-- guest user message --}}
                    <p class="text-muted small">Login to save your progress and rate games</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Login</a>
                    @endauth
                </div>
            </div>
            
            {{-- leaderboard section --}}
            <div id="leaderboard-{{ $game->id }}"></div>
        </div>
    </div>
</div>

{{-- save game state function for authenticated users --}}
@auth
<script>
function saveGameState() {
    {{-- get game state from localstorage or game itself --}}
    const gameState = localStorage.getItem('game_state_{{ $game->id }}') || '{}';
    
    {{-- send save state to api --}}
    fetch('/api/saves', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('api_token'),
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            game_id: {{ $game->id }},
            save_data: JSON.parse(gameState)
        })
    })
    .then(response => response.json())
    .then(data => {
        alert('Game state saved!');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to save game state');
    });
}
</script>
@endauth

{{-- leaderboard initialization --}}
@guest
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.LeaderboardService) {
        // Display guest leaderboard
        window.LeaderboardService.displayGuestLeaderboard({{ $game->id }}, 'leaderboard-{{ $game->id }}');
        // Listen for score updates from game
        window.LeaderboardService.listenForGameScores({{ $game->id }});
    }
});
</script>
@endguest
@endsection
