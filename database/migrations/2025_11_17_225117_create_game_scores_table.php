<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Indicates if the migration should be run within a database transaction.
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->bigInteger('score')->comment('Player score for this game');
            $table->timestamp('achieved_at')->useCurrent();
            $table->timestamps();
            
            // Index for efficient leaderboard queries
            $table->index(['game_id', 'score']);
            // Ensure one score per user per game (keep highest)
            $table->unique(['user_id', 'game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_scores');
    }
};
