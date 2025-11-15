<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// review model
// represents a user review for a game
// stores rating, comment, and approval status
class Review extends Model
{
    use HasFactory;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'user_id',       // foreign key to users table
        'game_id',       // foreign key to games table
        'rating',        // numeric rating (typically 1-5)
        'comment',       // text review comment
        'is_approved',   // whether review has been approved by moderator
    ];

    // type casting for database fields
    // automatically converts database values to appropriate php types
    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',  // convert to boolean
        ];
    }

    // relationship: review belongs to a user
    // each review is written by one user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // relationship: review belongs to a game
    // each review is for one specific game
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
