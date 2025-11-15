<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Game;
use App\Models\Review;
use Illuminate\Http\Request;

// api review controller
// handles game review operations via json api
// allows users to create, read, update, and delete reviews
class ReviewController extends Controller
{
    // get list of reviews
    // supports filtering by game or user
    // returns paginated json response
    public function index(Request $request)
    {
        // start query with relationships and only approved reviews
        $query = Review::with(['user', 'game'])
            ->where('is_approved', true);

        // filter by game if game_id parameter provided
        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        // filter by user if user_id parameter provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // paginate results with configurable page size
        $reviews = $query->latest()->paginate($request->get('per_page', 15));

        return ReviewResource::collection($reviews);
    }

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
        $existingReview = Review::where('user_id', $request->user()->id)
            ->where('game_id', $validated['game_id'])
            ->first();

        if ($existingReview) {
            return response()->json([
                'message' => 'You have already reviewed this game',
            ], 422);
        }

        // create review with auto approval
        $review = Review::create([
            'user_id' => $request->user()->id,
            'game_id' => $validated['game_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => true, // auto approve for now
        ]);

        // update game average rating based on all reviews
        $this->updateGameRating($validated['game_id']);

        return new ReviewResource($review->load('user'));
    }

    // get single review details
    // loads review with user and game relationships
    public function show(string $id)
    {
        $review = Review::with(['user', 'game'])
            ->where('is_approved', true)
            ->findOrFail($id);

        return new ReviewResource($review);
    }

    // update existing review
    // validates input and updates review record
    // updates game rating after review change
    public function update(Request $request, string $id)
    {
        $review = Review::findOrFail($id);

        // ensure user can only update their own reviews
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // validate request data
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // update review
        $review->update($validated);

        // update game average rating
        $this->updateGameRating($review->game_id);

        return new ReviewResource($review->load('user'));
    }

    // delete review
    // removes review from database
    // updates game rating after deletion
    public function destroy(Request $request, string $id)
    {
        $review = Review::findOrFail($id);

        // ensure user can only delete their own reviews
        if ($review->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $gameId = $review->game_id;
        $review->delete();

        // update game average rating after review deletion
        $this->updateGameRating($gameId);

        return response()->json(['message' => 'Review deleted successfully']);
    }

    // update game average rating
    // calculates average from all approved reviews
    // updates game rating and rating count
    private function updateGameRating($gameId)
    {
        $game = Game::findOrFail($gameId);
        $reviews = Review::where('game_id', $gameId)
            ->where('is_approved', true)
            ->get();

        if ($reviews->count() > 0) {
            $averageRating = $reviews->avg('rating');
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
