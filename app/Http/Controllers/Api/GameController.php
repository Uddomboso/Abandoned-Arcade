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

    // autocomplete search endpoint
    // returns games starting with search term
    // limit is based on number of characters in search term
    public function autocomplete(Request $request)
    {
        $searchTerm = $request->get('q', '');
        
        if (empty($searchTerm)) {
            return response()->json(['games' => []]);
        }

        // limit results based on number of characters
        // 1 char = all games, 2 chars = 2 games, 3 chars = 3 games, etc.
        $limit = strlen($searchTerm);
        if ($limit === 1) {
            $limit = 100; // Show all games for single character
        }

        // search games containing the search term (case insensitive)
        // match titles that start with or contain the search term
        $searchTermLower = strtolower($searchTerm);
        $words = explode(' ', $searchTermLower);
        
        $query = Game::with('genre')
            ->where('is_active', true)
            ->where(function($q) use ($searchTermLower, $words) {
                // Match titles starting with the full search term
                $q->whereRaw('LOWER(title) LIKE ?', [$searchTermLower . '%']);
                
                // Also match if title contains the full search term
                $q->orWhereRaw('LOWER(title) LIKE ?', ['%' . $searchTermLower . '%']);
                
                // For multi-word searches, match if any word in the title starts with any search word
                if (count($words) > 1) {
                    foreach ($words as $word) {
                        if (strlen($word) > 0) {
                            $q->orWhereRaw('LOWER(title) LIKE ?', [$word . '%']);
                            $q->orWhereRaw('LOWER(title) LIKE ?', ['% ' . $word . '%']);
                        }
                    }
                } else {
                    // For single word, also check if it appears anywhere in the title
                    $q->orWhereRaw('LOWER(title) LIKE ?', ['%' . $searchTermLower . '%']);
                }
            })
            ->orderByRaw("CASE WHEN LOWER(title) LIKE ? THEN 1 ELSE 2 END", [$searchTermLower . '%'])
            ->orderBy('title', 'asc')
            ->limit($limit);

        $games = $query->get();

        // format response for autocomplete
        $results = $games->map(function ($game) {
            return [
                'id' => $game->id,
                'title' => $game->title,
                'genre' => $game->genre->name ?? 'Unknown',
                'url' => url('/games/' . $game->id),
            ];
        });

        // check if there are more results (case insensitive)
        $hasMore = Game::where('is_active', true)
            ->whereRaw('LOWER(title) LIKE ?', ['%' . $searchTermLower . '%'])
            ->count() > $limit;

        return response()->json([
            'games' => $results,
            'has_more' => $hasMore,
            'search_term' => $searchTerm,
        ]);
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
