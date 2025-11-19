{{-- game play page view --}}
{{-- displays game in iframe or ruffle player based on game type --}}
{{-- handles keyboard event forwarding for iframe games --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-0 px-md-3">
    <div class="row g-0 g-md-3">
        {{-- game player container --}}
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 px-2 px-md-3">
                    <h4 class="mb-0" style="font-size: clamp(1rem, 4vw, 1.5rem);">{{ $game->title }}</h4>
                    <a href="{{ route('games.show', $game->id) }}" class="btn btn-sm btn-outline-secondary">← Back to Game</a>
                </div>
                <div class="card-body p-0" style="position: relative;">
                    {{-- game container with black background --}}
                    <div id="game-container" style="width: 100%; min-height: 400px; height: 60vh; max-height: 800px; background: #000; position: relative; overflow: hidden; touch-action: manipulation;">
                        {{-- flash game with ruffle player --}}
                        {{-- uses ruffle to play swf flash files in modern browsers --}}
                        @if($game->source_type === 'ruffle' || ($game->game_file_path && str_ends_with($game->game_file_path, '.swf')))
                            <div id="ruffle-container" style="width: 100%; height: 100%; min-height: 400px;"></div>
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
                                    height="100%"
                                    frameborder="0"
                                    allowfullscreen
                                    allow="pointer-events; autoplay; fullscreen; accelerometer; gyroscope"
                                    style="border: none; display: block; margin: 0; padding: 0; width: 100%; height: 100%; min-height: 400px; touch-action: manipulation; -webkit-touch-callout: none; -webkit-user-select: none; user-select: none;"
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
                                                    iframeBody.style.touchAction = 'manipulation';
                                                    iframeBody.style.webkitUserSelect = 'none';
                                                    iframeBody.style.webkitTouchCallout = 'none';
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
                                    
                                    {{-- focus iframe when clicked/touched to enable keyboard input --}}
                                    {{-- don't stop propagation - let events reach iframe naturally --}}
                                    gameIframe.addEventListener('click', function() {
                                        gameIframe.focus();
                                    });
                                    
                                    {{-- touch events for mobile --}}
                                    gameIframe.addEventListener('touchstart', function(e) {
                                        gameIframe.focus();
                                        console.log('Touch start on iframe');
                                    }, { passive: true });
                                    
                                    gameIframe.addEventListener('touchend', function(e) {
                                        gameIframe.focus();
                                        console.log('Touch end on iframe');
                                    }, { passive: true });
                                    
                                    {{-- test if mouse events are reaching the iframe --}}
                                    gameIframe.addEventListener('mouseenter', function() {
                                        console.log('Mouse entered iframe');
                                    });
                                    
                                    gameIframe.addEventListener('mousedown', function() {
                                        console.log('Mouse down on iframe');
                                        gameIframe.focus();
                                    });
                                    
                                    {{-- log for debugging --}}
                                    console.log('Game iframe initialized, ready for mouse and touch events');
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
        <div class="col-12 col-md-4 mt-3 mt-md-0 px-2 px-md-0">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Quick Actions</h5>
                </div>
                <div class="card-body">
                    @auth
                    {{-- authenticated user actions --}}
                    <button id="saveProgressBtn" class="btn btn-primary w-100 mb-2" onclick="saveGameState()">Save Progress</button>
                    @else
                    {{-- guest user message --}}
                    <p class="text-muted small">Login to save your progress</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100">Login</a>
                    @endauth
                </div>
            </div>
            
            {{-- leaderboard section --}}
            <div id="leaderboard-{{ $game->id }}"></div>
        </div>
    </div>
</div>

{{-- Save Game Modal --}}
@auth
<div class="modal fade" id="saveGameModal" tabindex="-1" aria-labelledby="saveGameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveGameModalLabel">Game Ended - Save Your Progress?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Would you like to save your game progress?</p>
                <div class="mb-3">
                    <label for="saveName" class="form-label">Save Name (Optional)</label>
                    <input type="text" class="form-control" id="saveName" placeholder="e.g., Level 3, High Score Run">
                </div>
                <div class="alert alert-info mb-0">
                    <small><strong>Note:</strong> Your score has been automatically saved to the leaderboard!</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Skip</button>
                <button type="button" class="btn btn-primary" onclick="saveGameState()">Save Progress</button>
            </div>
        </div>
    </div>
</div>
@endauth

{{-- save game state function for authenticated users --}}
@auth
<script>
let isSaving = false; {{-- prevent multiple simultaneous saves --}}

{{-- Show score notification (non-intrusive) --}}
function showScoreNotification(message, type) {
    {{-- Create notification element --}}
    const notification = document.createElement('div');
    notification.className = 'alert alert-' + (type === 'success' ? 'success' : 'info') + ' alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.body.appendChild(notification);
    
    {{-- Auto-remove after 3 seconds --}}
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 3000);
}

{{-- Helper function for games to submit scores directly --}}
{{-- Games can call this from their iframe: parent.submitGameScore(score) --}}
{{-- Or games can send postMessage with {type: 'gameEnd', score: 123, gameId: {{ $game->id }}} --}}
window.submitGameScore = function(score, showNotification = true) {
    if (!score || score < 0) {
        console.warn('Invalid score provided:', score);
        return Promise.reject('Invalid score');
    }
    
    return fetch('{{ route("scores.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            game_id: {{ $game->id }},
            score: parseInt(score)
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                console.error('Score submission error:', err);
                return Promise.reject(err);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Score submitted successfully', data);
        if (showNotification) {
            if (data.data && data.data.is_new_high_score) {
                showScoreNotification('New high score! ' + data.data.score.toLocaleString(), 'success');
            } else {
                showScoreNotification('Score saved: ' + data.data.score.toLocaleString(), 'info');
            }
        }
        return data;
    })
    .catch(error => {
        console.error('Error submitting score:', error);
        if (showNotification) {
            const errorMessage = error.message || (typeof error === 'string' ? error : 'Failed to submit score');
            showScoreNotification(errorMessage, 'danger');
        }
        return Promise.reject(error);
    });
};


function saveGameState(gameData = null) {
    {{-- prevent double-clicks and multiple saves --}}
    if (isSaving) {
        console.log('Save already in progress, please wait...');
        return;
    }
    
    isSaving = true;
    const saveBtn = document.getElementById('saveProgressBtn');
    if (saveBtn && !gameData) { {{-- Only disable button for manual saves --}}
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
    }
    
    {{-- get game state from parameter, localstorage, or game itself --}}
    let gameState = gameData;
    if (!gameState) {
        const stored = localStorage.getItem('game_state_{{ $game->id }}');
        gameState = stored ? JSON.parse(stored) : {};
    }
    const saveData = typeof gameState === 'string' ? JSON.parse(gameState) : (gameState || {});
    
    {{-- send save state to web route using session auth --}}
    fetch('{{ route("saves.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            game_id: {{ $game->id }},
            save_data: saveData
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                console.error('Save error response:', err);
                return Promise.reject(err);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Game state saved successfully', data);
        {{-- Show success message --}}
        if (!gameData) { {{-- Only show alert if manually triggered --}}
            alert('Game state saved!');
        }
    })
    .catch(error => {
        console.error('Error saving game state:', error);
        const errorMessage = error.message || (typeof error === 'string' ? error : 'Failed to save game state');
        if (!gameData) { {{-- Only show alert if manually triggered --}}
            alert(errorMessage);
        }
    })
    .finally(() => {
        {{-- re-enable button and reset state --}}
        isSaving = false;
        if (saveBtn && !gameData) {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Progress';
        }
    });
}


{{-- Listen for game end events and automatically submit scores --}}
document.addEventListener('DOMContentLoaded', function() {
    {{-- Listen for custom game end event from iframe --}}
    window.addEventListener('message', function(event) {
        {{-- Check if message is from game iframe and indicates game end --}}
        if (event.data && event.data.type === 'gameEnd' && event.data.gameId === {{ $game->id }}) {
            console.log('Game ended, processing score and save...');
            
            {{-- Store game data for saving --}}
            if (event.data.gameData) {
                localStorage.setItem('game_state_{{ $game->id }}', JSON.stringify(event.data.gameData));
            }
            
            {{-- Automatically submit score if provided by game --}}
            if (event.data.score !== undefined && event.data.score > 0) {
                console.log('Auto-submitting score:', event.data.score);
                {{-- Auto-submit without showing notification (silent) --}}
                submitGameScore(event.data.score, false)
                    .then((data) => {
                        console.log('Score automatically submitted successfully');
                        {{-- Only show notification if it's a new high score --}}
                        if (data.data && data.data.is_new_high_score) {
                            showScoreNotification('New high score! ' + data.data.score.toLocaleString(), 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Failed to auto-submit score:', error);
                        {{-- Show error notification if auto-submit fails --}}
                        showScoreNotification('Failed to save score. Please try again.', 'danger');
                    });
            }
            
            {{-- Show save modal for game state (optional) --}}
            @auth
            if (event.data.gameData) {
                {{-- Show save modal for game progress --}}
                const modal = new bootstrap.Modal(document.getElementById('saveGameModal'));
                modal.show();
            }
            @endauth
        }
    });
    
    {{-- Also listen for game over events in iframe --}}
    const gameIframe = document.getElementById('game-iframe');
    if (gameIframe) {
        gameIframe.addEventListener('load', function() {
            try {
                const iframeWindow = gameIframe.contentWindow;
                if (iframeWindow) {
                    {{-- Try to hook into game's game over event --}}
                    {{-- This will depend on the specific game implementation --}}
                    console.log('Game iframe loaded, ready to listen for game end events');
                }
            } catch (e) {
                console.log('Cannot access iframe for game end detection');
            }
        });
    }
});
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


