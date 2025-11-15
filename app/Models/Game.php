<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PreservationLog;

// game model
// represents a game in the arcade collection
// stores game information, metadata, and relationships
class Game extends Model
{
    use HasFactory;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'title',              // game title
        'slug',               // url friendly identifier
        'description',        // game description
        'developer',          // game developer name
        'publisher',          // game publisher name
        'release_date',       // original release date
        'image_url',          // url to game thumbnail image
        'game_url',           // external url to play game (if hosted elsewhere)
        'game_file_path',     // path to local game file (relative to public/games/)
        'source_type',        // type of game: html5, ruffle, wasm, embedded
        'genre_id',           // foreign key to genres table
        'rating',             // average user rating
        'rating_count',       // number of ratings received
        'play_count',         // number of times game has been played
        'is_featured',        // whether game appears in featured section
        'is_active',          // whether game is active and visible
    ];

    // type casting for database fields
    // automatically converts database values to appropriate php types
    protected function casts(): array
    {
        return [
            'release_date' => 'date',      // convert to carbon date object
            'rating' => 'decimal:2',        // decimal with 2 decimal places
            'is_featured' => 'boolean',    // convert to boolean
            'is_active' => 'boolean',      // convert to boolean
        ];
    }

    // relationship: game belongs to a genre
    // each game is categorized under one genre
    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class);
    }

    // relationship: game has many reviews
    // users can leave multiple reviews for a game
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // relationship: game has many save states
    // users can save their progress in games
    public function saveStates(): HasMany
    {
        return $this->hasMany(SaveState::class);
    }

    // relationship: game has many preservation logs
    // tracks preservation and archival information about the game
    public function preservationLog(): HasMany
    {
        return $this->hasMany(PreservationLog::class);
    }
}
