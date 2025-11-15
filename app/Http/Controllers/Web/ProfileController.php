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
    // shows user information, reviews, and recent save states
    public function show(Request $request)
    {
        // get current authenticated user
        $user = $request->user();
        
        // get user reviews with game information
        // paginated with 10 reviews per page
        $reviews = $user->reviews()
            ->with('game')
            ->latest()
            ->paginate(10);

        // get recent save states with game information
        // limited to 10 most recently played
        $saveStates = $user->saveStates()
            ->with('game')
            ->latest('last_played_at')
            ->take(10)
            ->get();

        return view('profile.show', compact('user', 'reviews', 'saveStates'));
    }

    // display user game collection
    // shows all games user has saved progress in
    public function collection(Request $request)
    {
        // get current authenticated user
        $user = $request->user();
        
        // get all save states with game information
        // paginated with 20 save states per page
        $saveStates = $user->saveStates()
            ->with('game')
            ->latest('last_played_at')
            ->paginate(20);

        return view('profile.collection', compact('user', 'saveStates'));
    }
}
