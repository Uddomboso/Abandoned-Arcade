<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Http\Request;

// page controller for static pages
// handles home page and other general page views
class PageController extends Controller
{
    // display home page
    // shows latest games and genre list
    public function home()
    {
        // get latest games (most recently added)
        // loads genre relationship and limits to 12 games
        $latestGames = Game::with('genre')
            ->where('is_active', true)
            ->latest()
            ->take(12)
            ->get();

        // get all genres with game count
        // used for genre navigation on home page
        $genres = Genre::withCount('games')->get();

        return view('home', compact('latestGames', 'genres'));
    }
}
