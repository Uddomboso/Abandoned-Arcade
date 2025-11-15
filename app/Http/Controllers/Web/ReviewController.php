<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// web review controller
// handles review creation from web interface
// validates input and updates game ratings
class ReviewController extends Controller
{
    // create new review
    // validates input and creates review record
    // prevents duplicate reviews from same user
    // updates game rating automatically
    public function store(Request $request)
    {
        // validate request data
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // check if user already reviewed this game
        // prevents multiple reviews from same user
        $existingReview = Review::where('user_id', Auth::id())
            ->where('game_id', $validated['game_id'])
            ->first();

        if ($existingReview) {
            // return error if review already exists
            return back()->withErrors(['rating' => 'You have already reviewed this game.'])->withInput();
        }

        // create review with auto approval
        $review = Review::create([
            'user_id' => Auth::id(),
            'game_id' => $validated['game_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => true,
        ]);

        // update game average rating based on all reviews
        $this->updateGameRating($validated['game_id']);

        // redirect back to game page with success message
        return redirect()->route('games.show', $validated['game_id'])
            ->with('success', 'Thank you for your rating!');
    }

    // update game rating based on all reviews
    // calculates average from all approved reviews
    // updates game rating and rating count fields
    private function updateGameRating(int $gameId): void
    {
        $game = Game::findOrFail($gameId);
        // get all approved reviews for this game
        $reviews = Review::where('game_id', $gameId)
            ->where('is_approved', true)
            ->get();

        if ($reviews->count() > 0) {
            // calculate average rating
            $averageRating = $reviews->avg('rating');
            // update game with new rating and count
            $game->update([
                'rating' => round($averageRating, 2),
                'rating_count' => $reviews->count(),
            ]);
        } else {
            // reset rating if no reviews
            $game->update([
                'rating' => 0,
                'rating_count' => 0,
            ]);
        }
    }
}
