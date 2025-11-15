<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or get genres
        $arcadeGenre = Genre::firstOrCreate(
            ['slug' => 'arcade'],
            ['name' => 'Arcade', 'description' => 'Classic arcade games']
        );
        
        $puzzleGenre = Genre::firstOrCreate(
            ['slug' => 'puzzle'],
            ['name' => 'Puzzle', 'description' => 'Puzzle and brain games']
        );
        
        $actionGenre = Genre::firstOrCreate(
            ['slug' => 'action'],
            ['name' => 'Action', 'description' => 'Action-packed games']
        );
        
        // Snake Game
        Game::firstOrCreate(
            ['slug' => 'snake-game'],
            [
                'title' => 'Snake Game',
                'description' => 'Classic Snake game - eat food and grow longer! Avoid hitting the walls or yourself.',
                'developer' => 'Classic',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->subYears(30),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'snake.html',
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => true,
                'is_active' => true,
            ]
        );
        
        // Add more games as you download them
        // Example structure:
        /*
        Game::firstOrCreate(
            ['slug' => 'pong-game'],
            [
                'title' => 'Pong',
                'description' => 'Classic Pong game',
                'developer' => 'Classic',
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'pong.html', // or game_url for external
                'source_type' => 'html5',
                'is_featured' => true,
                'is_active' => true,
            ]
        );
        */
    }
}

