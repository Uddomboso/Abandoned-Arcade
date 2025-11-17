// leaderboard utility for guest and authenticated users
// handles score display and leaderboard functionality

class LeaderboardService {
    constructor() {
        const bodyElement = document.body;
        const isAuthenticated = bodyElement && bodyElement.dataset.userAuthenticated === 'true';
        this.isGuest = !isAuthenticated;
    }

    // check if user is guest
    checkGuestStatus() {
        const bodyElement = document.body;
        const isAuthenticated = bodyElement && bodyElement.dataset.userAuthenticated === 'true';
        this.isGuest = !isAuthenticated;
        return this.isGuest;
    }

    // display guest leaderboard (only shows guest's own score)
    // shows message to login for public leaderboard
    displayGuestLeaderboard(gameId, containerId) {
        // Re-check guest status
        if (!this.checkGuestStatus() || !window.GuestService) {
            return;
        }

        const container = document.getElementById(containerId);
        if (!container) return;

        const guestScore = window.GuestService.getBestScore(gameId);
        const guestId = window.GuestService.getGuestId();
        const numbers = guestId.replace('Guest', '');

        container.innerHTML = `
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Your Score</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Guest ${numbers}:</strong> ${guestScore.toLocaleString()}
                    </div>
                    <p class="text-muted small mb-2">
                        <em>Login to join the public leaderboard and compete with other players!</em>
                    </p>
                    <a href="/login" class="btn btn-sm btn-primary">Login to Join Leaderboard</a>
                </div>
            </div>
        `;
    }

    // save score from game (for guests, stores in localStorage)
    saveScore(gameId, score) {
        if (this.checkGuestStatus() && window.GuestService) {
            return window.GuestService.saveScore(gameId, score);
        }
        // For authenticated users, would make API call here
        return false;
    }

    // get best score for current user
    getBestScore(gameId) {
        if (this.checkGuestStatus() && window.GuestService) {
            return window.GuestService.getBestScore(gameId);
        }
        // For authenticated users, would fetch from API
        return 0;
    }

    // listen for score messages from game iframes
    // games can postMessage with score data
    listenForGameScores(gameId) {
        window.addEventListener('message', (event) => {
            // Verify origin for security
            // In production, check event.origin matches your domain
            
            if (event.data && event.data.type === 'gameScore' && event.data.gameId === gameId) {
                const score = parseInt(event.data.score);
                if (!isNaN(score) && score >= 0) {
                    this.saveScore(gameId, score);
                    // Update leaderboard display if exists
                    const leaderboardContainer = document.getElementById(`leaderboard-${gameId}`);
                    if (leaderboardContainer) {
                        this.displayGuestLeaderboard(gameId, `leaderboard-${gameId}`);
                    }
                }
            }
        });
    }
}

// create global instance
window.LeaderboardService = new LeaderboardService();

// export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LeaderboardService;
}

