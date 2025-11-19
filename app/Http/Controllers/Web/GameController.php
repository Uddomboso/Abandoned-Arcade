<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use App\Models\SaveState;
use Illuminate\Support\Facades\DB;
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

        // search by title or description if search parameter provided (case insensitive)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = trim($request->search);
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($searchTerm) . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($searchTerm) . '%']);
            });
        }

        // sort games by specified field and order
        // defaults to created_at descending (newest first)
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // paginate results with 12 games per page
        $games = $query->paginate(12);
        // get all genres for filter sidebar, excluding "others" category
        $genres = Genre::where('slug', '!=', 'other')
            ->where('slug', '!=', 'others')
            ->get();

        return view('games.index', compact('games', 'genres'));
    }

    // display single game details page
    // shows game information and related games
    public function show(string $id)
    {
        // load game with genre relationship
        $game = Game::with('genre')
            ->where('is_active', true)
            ->findOrFail($id);

        // get one related game from same genre
        // excludes current game
        $relatedGame = Game::with('genre')
            ->where('is_active', true)
            ->where('genre_id', $game->genre_id)
            ->where('id', '!=', $game->id)
            ->first();

        // get leaderboard ranking of users by their play counts
        // ranked by number of plays (save states), then by most recent play
        $leaderboard = SaveState::where('game_id', $game->id)
            ->select('user_id', DB::raw('COUNT(*) as play_count'), DB::raw('MAX(last_played_at) as last_played'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('play_count', 'desc')
            ->orderBy('last_played', 'desc')
            ->limit(10)
            ->get();

        return view('games.show', compact('game', 'relatedGame', 'leaderboard'));
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
