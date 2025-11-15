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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('developer')->nullable();
            $table->string('publisher')->nullable();
            $table->date('release_date')->nullable();
            $table->string('image_url')->nullable();
            $table->string('game_url')->nullable(); // URL to play the game
            $table->foreignId('genre_id')->constrained()->onDelete('cascade');
            $table->decimal('rating', 3, 2)->default(0)->comment('Average rating from reviews');
            $table->integer('rating_count')->default(0);
            $table->integer('play_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
