<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameScore;
use Illuminate\Http\Request;

// api score controller
// handles game score submission and retrieval
// allows users to submit high scores for games
class ScoreController extends Controller
{
    // submit a score for a game
    // creates new score or updates if higher score exists
    public function store(Request $request)
    {
        // validate request data
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'score' => 'required|integer|min:0',
        ]);

        // check if user already has a score for this game
        $existingScore = GameScore::where('user_id', auth()->id())
            ->where('game_id', $validated['game_id'])
            ->first();

        if ($existingScore) {
            // only update if new score is higher
            if ($validated['score'] > $existingScore->score) {
                $existingScore->update([
                    'score' => $validated['score'],
                    'achieved_at' => now(),
                ]);

                return response()->json([
                    'message' => 'New high score achieved!',
                    'data' => [
                        'id' => $existingScore->id,
                        'game_id' => $existingScore->game_id,
                        'score' => $existingScore->score,
                        'achieved_at' => $existingScore->achieved_at->toISOString(),
                        'is_new_high_score' => true,
                    ],
                ]);
            } else {
                // score is not higher, return existing score
                return response()->json([
                    'message' => 'Score submitted, but not a new high score',
                    'data' => [
                        'id' => $existingScore->id,
                        'game_id' => $existingScore->game_id,
                        'score' => $existingScore->score,
                        'achieved_at' => $existingScore->achieved_at->toISOString(),
                        'is_new_high_score' => false,
                    ],
                ]);
            }
        }

        // create new score if user doesn't have one yet
        $gameScore = GameScore::create([
            'user_id' => auth()->id(),
            'game_id' => $validated['game_id'],
            'score' => $validated['score'],
            'achieved_at' => now(),
        ]);

        return response()->json([
            'message' => 'Score saved successfully',
            'data' => [
                'id' => $gameScore->id,
                'game_id' => $gameScore->game_id,
                'score' => $gameScore->score,
                'achieved_at' => $gameScore->achieved_at->toISOString(),
                'is_new_high_score' => true,
            ],
        ], 201);
    }
}
