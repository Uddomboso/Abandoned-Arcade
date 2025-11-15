<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// save state model
// represents a saved game state for a user
// stores game progress data that can be loaded later
class SaveState extends Model
{
    use HasFactory;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'user_id',         // foreign key to users table
        'game_id',         // foreign key to games table
        'save_name',       // optional name for the save (e.g., "Level 3")
        'save_data',       // json data containing game state
        'last_played_at',  // timestamp of when game was last played
    ];

    // type casting for database fields
    // automatically converts database values to appropriate php types
    protected function casts(): array
    {
        return [
            'save_data' => 'array',        // automatically encode/decode json
            'last_played_at' => 'datetime', // convert to carbon date object
        ];
    }

    // relationship: save state belongs to a user
    // each save state is owned by one user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relationship: save state belongs to a game
    // each save state is for one specific game
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
