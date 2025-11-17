<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Http\Request;

// game controller for web routes
// handles displaying games, game details, and game play page
class GameController extends Controller
{
    // display list of all active games
    // supports filtering by genre, search, and sorting
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

        // search by title or description if search parameter provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // sort games by specified field and order
        // defaults to created_at descending (newest first)
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // paginate results with 12 games per page
        $games = $query->paginate(12);
        // get all genres for filter sidebar
        $genres = Genre::all();

        return view('games.index', compact('games', 'genres'));
    }

    // display single game details page
    // shows game information, reviews, and related games
    public function show(string $id)
    {
        // load game with genre and reviews (including review authors)
        $game = Game::with(['genre', 'reviews.user'])
            ->where('is_active', true)
            ->findOrFail($id);

        // get related games from same genre
        // excludes current game and limits to 4 results
        $relatedGames = Game::with('genre')
            ->where('is_active', true)
            ->where('genre_id', $game->genre_id)
            ->where('id', '!=', $game->id)
            ->take(4)
            ->get();

        return view('games.show', compact('game', 'relatedGames'));
    }

    // display game play page
    // loads game in iframe and increments play count
    public function play(string $id)
    {
        // load game with preservation log information
        $game = Game::with('preservationLog')->findOrFail($id);
        
        // increment play count for analytics
        $game->increment('play_count');

        return view('games.play', compact('game'));
    }
}
