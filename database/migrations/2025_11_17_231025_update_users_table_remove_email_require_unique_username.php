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
        Schema::table('users', function (Blueprint $table) {
            // Make email nullable (not required)
            $table->string('email')->nullable()->change();
            // Remove unique constraint from email
            $table->dropUnique(['email']);
            // Add unique constraint to name (username)
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove unique constraint from name
            $table->dropUnique(['name']);
            // Restore unique constraint to email
            $table->unique('email');
            // Make email required again
            $table->string('email')->nullable(false)->change();
        });
    }
};
