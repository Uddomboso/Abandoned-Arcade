<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\SaveState;
use Illuminate\Http\Request;

// api save controller
// handles game save state operations via json api
// allows users to save, load, update, and delete game progress
class SaveController extends Controller
{
    // get list of save states for authenticated user
    // supports filtering by game
    // returns json response with save state data
    public function index(Request $request)
    {
        // start query with game relationship for current user
        $query = SaveState::with('game')
            ->where('user_id', $request->user()->id);

        // filter by game if game_id parameter provided
        if ($request->has('game_id')) {
            $query->where('game_id', $request->game_id);
        }

        // get save states ordered by most recently played
        $saveStates = $query->latest('last_played_at')->get();

        // format response with game information
        return response()->json([
            'data' => $saveStates->map(function ($saveState) {
                return [
                    'id' => $saveState->id,
                    'game' => [
                        'id' => $saveState->game->id,
                        'title' => $saveState->game->title,
                        'slug' => $saveState->game->slug,
                    ],
                    'save_name' => $saveState->save_name,
                    'last_played_at' => $saveState->last_played_at?->toISOString(),
                    'created_at' => $saveState->created_at->toISOString(),
                    'updated_at' => $saveState->updated_at->toISOString(),
                ];
            }),
        ]);
    }

    // create new save state
    // validates input and creates or updates save state
    // updates existing save if one already exists for user and game
    public function store(Request $request)
    {
        // validate request data
        $validated = $request->validate([
            'game_id' => 'required|exists:games,id',
            'save_name' => 'nullable|string|max:255',
            'save_data' => 'required|array',
        ]);

        // check if save state already exists for this user and game
        // if exists, update it instead of creating duplicate
        $existingSave = SaveState::where('user_id', $request->user()->id)
            ->where('game_id', $validated['game_id'])
            ->first();

        if ($existingSave) {
            // update existing save with new data
            $existingSave->update([
                'save_name' => $validated['save_name'] ?? $existingSave->save_name,
                'save_data' => $validated['save_data'],
                'last_played_at' => now(),
            ]);

            return response()->json([
                'message' => 'Save state updated successfully',
                'data' => [
                    'id' => $existingSave->id,
                    'game_id' => $existingSave->game_id,
                    'save_name' => $existingSave->save_name,
                    'last_played_at' => $existingSave->last_played_at->toISOString(),
                ],
            ]);
        }

        // create new save state
        $saveState = SaveState::create([
            'user_id' => $request->user()->id,
            'game_id' => $validated['game_id'],
            'save_name' => $validated['save_name'] ?? 'Auto Save',
            'save_data' => $validated['save_data'],
            'last_played_at' => now(),
        ]);

        return response()->json([
            'message' => 'Save state created successfully',
            'data' => [
                'id' => $saveState->id,
                'game_id' => $saveState->game_id,
                'save_name' => $saveState->save_name,
                'last_played_at' => $saveState->last_played_at->toISOString(),
            ],
        ], 201);
    }

    // get single save state details
    // loads save state with game relationship
    // includes full save data for loading game state
    public function show(Request $request, string $id)
    {
        // load save state with game, ensure it belongs to current user
        $saveState = SaveState::with('game')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        // return save state with full save data
        return response()->json([
            'id' => $saveState->id,
            'game' => [
                'id' => $saveState->game->id,
                'title' => $saveState->game->title,
                'slug' => $saveState->game->slug,
            ],
            'save_name' => $saveState->save_name,
            'save_data' => $saveState->save_data,
            'last_played_at' => $saveState->last_played_at?->toISOString(),
            'created_at' => $saveState->created_at->toISOString(),
            'updated_at' => $saveState->updated_at->toISOString(),
        ]);
    }

    // update existing save state
    // validates input and updates save state record
    // updates last played timestamp
    public function update(Request $request, string $id)
    {
        // find save state and ensure it belongs to current user
        $saveState = SaveState::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // validate request data
        $validated = $request->validate([
            'save_name' => 'sometimes|string|max:255',
            'save_data' => 'sometimes|array',
        ]);

        // update save state with new data and timestamp
        $saveState->update(array_merge($validated, [
            'last_played_at' => now(),
        ]));

        return response()->json([
            'message' => 'Save state updated successfully',
            'data' => [
                'id' => $saveState->id,
                'game_id' => $saveState->game_id,
                'save_name' => $saveState->save_name,
                'last_played_at' => $saveState->last_played_at->toISOString(),
            ],
        ]);
    }

    // delete save state
    // removes save state from database
    // ensures user can only delete their own saves
    public function destroy(Request $request, string $id)
    {
        // find save state and ensure it belongs to current user
        $saveState = SaveState::where('user_id', $request->user()->id)
            ->findOrFail($id);

        // delete save state
        $saveState->delete();

        return response()->json(['message' => 'Save state deleted successfully'], 200);
    }
}
