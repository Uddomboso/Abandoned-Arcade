<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

// user model
// represents an authenticated user in the system
// handles authentication, api tokens, and user relationships
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    // mass assignable fields
    // these fields can be set via create or update methods
    protected $fillable = [
        'name',                    // user display name
        'email',                   // user email address
        'password',                // hashed password
        'workos_id',               // workos user identifier (if using workos auth)
        'workos_organization_id',  // workos organization identifier
        'is_workos_user',          // flag indicating if user authenticated via workos
    ];

    // hidden fields for serialization
    // these fields are excluded when converting model to array or json
    protected $hidden = [
        'password',         // never expose password hash
        'remember_token',   // never expose remember token
    ];

    // type casting for database fields
    // automatically converts database values to appropriate php types
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',  // convert to carbon date object
            'password' => 'hashed',              // automatically hash password on set
        ];
    }

    // relationship: user has many reviews
    // users can write multiple reviews for different games
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // relationship: user has many save states
    // users can save progress in multiple games
    public function saveStates(): HasMany
    {
        return $this->hasMany(SaveState::class);
    }
}
