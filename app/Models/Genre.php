<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// genre model
// represents a game genre category
// used to organize and filter games by type
class Genre extends Model
{
    use HasFactory;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'name',         // genre name (e.g., "Arcade", "Puzzle")
        'slug',         // url friendly identifier
        'description',  // genre description
    ];

    // relationship: genre has many games
    // one genre can contain multiple games
    public function games(): HasMany
    {
        return $this->hasMany(Game::class);
    }
}
