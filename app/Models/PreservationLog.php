<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// preservation log model
// tracks archival and preservation information about games
// stores metadata about original source, platform, and preservation notes
class PreservationLog extends Model
{
    use HasFactory;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'game_id',            // foreign key to games table
        'original_url',        // original url where game was found
        'developer',           // original game developer
        'platform',            // original platform (e.g., "Flash", "HTML5")
        'release_year',        // year game was originally released
        'preservation_notes',  // notes about preservation process
        'source_type',         // type of source (e.g., "archive.org", "github")
    ];

    // relationship: preservation log belongs to a game
    // each log entry is associated with one game
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }
}
