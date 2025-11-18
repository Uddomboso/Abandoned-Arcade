<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;

// web profile controller
// handles user profile pages and game collection display
// requires authentication to access
class ProfileController extends Controller
{
    // display user profile page
    // shows user information and recent save states
    public function show(Request $request)
    {
        // get current authenticated user
        $user = $request->user();

        // get recent save states with game information
        // limited to 10 most recently played
        $saveStates = $user->saveStates()
            ->with('game')
            ->latest('last_played_at')
            ->take(10)
            ->get();

        return view('profile.show', compact('user', 'saveStates'));
    }
}
