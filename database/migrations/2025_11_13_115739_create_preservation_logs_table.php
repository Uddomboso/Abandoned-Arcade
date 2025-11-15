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
        Schema::create('preservation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->string('original_url')->nullable()->comment('Original source URL where game was found');
            $table->string('developer')->nullable()->comment('Original developer name');
            $table->string('platform')->nullable()->comment('Original platform: Flash, HTML5, etc.');
            $table->integer('release_year')->nullable()->comment('Original release year');
            $table->text('preservation_notes')->nullable()->comment('How it was preserved, source info, etc.');
            $table->string('source_type')->default('html5')->comment('html5, ruffle, wasm, embedded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preservation_logs');
    }
};
