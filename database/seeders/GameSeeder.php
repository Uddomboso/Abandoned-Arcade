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
        
        // Pacman
        Game::updateOrCreate(
            ['slug' => 'pacman'],
            [
                'title' => 'Pacman',
                'description' => 'Classic Pacman arcade game - eat dots and avoid ghosts! Navigate mazes and reach high scores.',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(1980),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'pacmanm/pacman/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => true,
                'is_active' => true,
            ]
        );
        
        // Tetris
        Game::updateOrCreate(
            ['slug' => 'tetris'],
            [
                'title' => 'Tetris',
                'description' => 'Classic Tetris puzzle game - rotate and drop falling blocks to create complete lines!',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(1984),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'tetrism/tetris/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => true,
                'is_active' => true,
            ]
        );
        
        // Spider Solitaire
        Game::updateOrCreate(
            ['slug' => 'spider'],
            [
                'title' => 'Spider Solitaire',
                'description' => 'Classic Spider Solitaire card game - build sequences of cards in descending order to complete the game.',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(1949),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'spiderm/spider/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
        
        // Snake Game (New version)
        Game::updateOrCreate(
            ['slug' => 'snake-game'],
            [
                'title' => 'Snake Game',
                'description' => 'Classic Snake game - eat food and grow longer! Avoid hitting the walls or yourself.',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(1976),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'snakem/snake/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => true,
                'is_active' => true,
            ]
        );
        
        // Bounce
        Game::updateOrCreate(
            ['slug' => 'bounce'],
            [
                'title' => 'Bounce',
                'description' => 'Classic Bounce arcade game - break bricks with a bouncing ball! Keep the ball in play and clear all bricks.',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(1976),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'bouncem/bounce/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
        
        // Chrome Dino
        Game::updateOrCreate(
            ['slug' => 'chrome-dino'],
            [
                'title' => 'Chrome Dino',
                'description' => 'Chrome Dino endless runner game - jump over cacti and avoid obstacles! Run as far as you can.',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(2014),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'chromeDino/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
        
        // Jigsaw Puzzle Game
        Game::updateOrCreate(
            ['slug' => 'jigsaw'],
            [
                'title' => 'Jigsaw',
                'description' => 'Classic jigsaw puzzle game - solve puzzles by arranging pieces to complete beautiful images.',
                'developer' => 'TomMalbran',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now(),
                'genre_id' => $puzzleGenre->id,
                'game_file_path' => 'puzzlem/puzzle/index.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
        
        // Bubble Shooter
        Game::updateOrCreate(
            ['slug' => 'bubble-shooter'],
            [
                'title' => 'Bubble Shooter',
                'description' => 'Classic Bubble Shooter arcade game - aim and shoot colored bubbles to match and pop them! Clear the board to advance.',
                'developer' => 'rembound',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(1994),
                'genre_id' => $arcadeGenre->id,
                'game_file_path' => 'bubbleshooter/bubble-shooter.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
        
        // Match 3
        Game::updateOrCreate(
            ['slug' => 'match3'],
            [
                'title' => 'Match 3',
                'description' => 'Classic Match 3 puzzle game - swap and match colorful gems to clear the board! Create combos and reach high scores.',
                'developer' => 'rembound',
                'publisher' => 'Abandoned Arcade',
                'release_date' => now()->setYear(2001),
                'genre_id' => $puzzleGenre->id,
                'game_file_path' => 'match3/match3.html',
                'image_url' => null, // Will use preview.png detection
                'source_type' => 'html5',
                'rating' => 0,
                'rating_count' => 0,
                'play_count' => 0,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
    }
}

