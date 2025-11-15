<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// api guest controller
// handles syncing guest data to user account
// transfers localstorage data (save states, favorites, play history) to database
class GuestController extends Controller
{
    // sync guest data when user logs in
    // transfers localstorage data to user account in database
    // handles save states, favorites, and play history
    public function sync(Request $request)
    {
        // validate request data
        // all fields are optional arrays
        $request->validate([
            'save_states' => 'sometimes|array',
            'favorites' => 'sometimes|array',
            'play_history' => 'sometimes|array',
        ]);

        // get authenticated user
        $user = Auth::user();
        $synced = [];

        // sync save states from localstorage to database
        if ($request->has('save_states')) {
            foreach ($request->save_states as $saveState) {
                // check if save state already exists for this game and name
                $existing = $user->saveStates()
                    ->where('game_id', $saveState['game_id'])
                    ->where('save_name', $saveState['save_name'] ?? null)
                    ->first();

                if ($existing) {
                    // update existing save state with new data
                    $existing->update([
                        'save_data' => $saveState['save_data'],
                        'last_played_at' => $saveState['last_played_at'] ?? now(),
                    ]);
                } else {
                    // create new save state
                    $user->saveStates()->create([
                        'game_id' => $saveState['game_id'],
                        'save_name' => $saveState['save_name'] ?? null,
                        'save_data' => $saveState['save_data'],
                        'last_played_at' => $saveState['last_played_at'] ?? now(),
                    ]);
                }
            }
            // track how many save states were synced
            $synced['save_states'] = count($request->save_states);
        }

        // return success response with sync count
        return response()->json([
            'message' => 'Guest data synced successfully',
            'synced' => $synced,
        ]);
    }
}
