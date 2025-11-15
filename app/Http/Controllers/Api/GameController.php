<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;

// api game controller
// provides json api endpoints for game operations
// used by frontend applications and mobile apps
class GameController extends Controller
{
    // get list of games
    // supports filtering, searching, and sorting
    // returns paginated json response
    public function index(Request $request)
    {
        // start query with genre relationship and only active games
        $query = Game::with('genre')->where('is_active', true);

        // filter by genre if genre parameter provided
        if ($request->has('genre')) {
            $query->whereHas('genre', function ($q) use ($request) {
                $q->where('slug', $request->genre);
            });
        }

        // filter by featured status if featured parameter provided
        if ($request->has('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        // search by title if search parameter provided
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // sort games by specified field and order
        // defaults to created_at descending
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // paginate results with configurable page size
        // defaults to 15 games per page
        $games = $query->paginate($request->get('per_page', 15));

        // return games as json resource collection
        return GameResource::collection($games);
    }

    // create new game
    // validates input and creates game record
    // requires authentication
    public function store(Request $request)
    {
        // validate request data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games',
            'description' => 'required|string',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'image_url' => 'nullable|url',
            'game_url' => 'nullable|url',
            'genre_id' => 'required|exists:genres,id',
            'is_featured' => 'boolean',
        ]);

        // create game with validated data
        $game = Game::create($validated);

        // return created game as json resource with genre loaded
        return new GameResource($game->load('genre'));
    }

    // get single game details
    // loads game with genre and reviews
    // increments play count for analytics
    public function show(string $id)
    {
        // load game with relationships
        $game = Game::with(['genre', 'reviews.user'])
            ->where('is_active', true)
            ->findOrFail($id);

        // increment play count for analytics
        $game->increment('play_count');

        // return game as json resource
        return new GameResource($game);
    }

    // update existing game
    // validates input and updates game record
    // requires authentication
    public function update(Request $request, string $id)
    {
        $game = Game::findOrFail($id);

        // validate request data
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:games,slug,' . $id,
            'description' => 'sometimes|string',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'image_url' => 'nullable|url',
            'game_url' => 'nullable|url',
            'genre_id' => 'sometimes|exists:genres,id',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // update game with validated data
        $game->update($validated);

        // return updated game as json resource
        return new GameResource($game->load('genre'));
    }

    // delete game
    // removes game from database
    // requires authentication
    public function destroy(string $id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        // return success response
        return response()->json(['message' => 'Game deleted successfully']);
    }
}
