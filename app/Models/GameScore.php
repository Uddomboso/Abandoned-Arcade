<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// game score model
// represents a high score achieved by a user in a game
// tracks leaderboard rankings for competitive gameplay
class GameScore extends Model
{
    use HasFactory;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'user_id',         // foreign key to users table
        'game_id',         // foreign key to games table
        'score',           // player's score for this game
        'achieved_at',     // timestamp when score was achieved
    ];

    // type casting for database fields
    // automatically converts database values to appropriate php types
    protected function casts(): array
    {
        return [
            'score' => 'integer',           // convert to integer
            'achieved_at' => 'datetime',     // convert to carbon date object
        ];
    }

    // relationship: score belongs to a user
    // each score is owned by one user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relationship: score belongs to a game
    // each score is for one specific game
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
