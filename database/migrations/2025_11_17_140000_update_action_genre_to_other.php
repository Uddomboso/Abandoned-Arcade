<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if genres table exists first
        if (!Schema::hasTable('genres')) {
            return;
        }
        
        try {
            // Delete Action genre if it exists
            DB::table('genres')->where('slug', 'action')->delete();
            
            // Ensure Other genre exists
            if (!DB::table('genres')->where('slug', 'other')->exists()) {
                DB::table('genres')->insert([
                    'name' => 'Other',
                    'slug' => 'other',
                    'description' => 'Other games',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            // If migration fails, log and continue
            \Log::warning('Migration update_action_genre_to_other failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert Other genre back to Action
        DB::table('genres')
            ->where('slug', 'other')
            ->update([
                'name' => 'Action',
                'slug' => 'action',
                'description' => 'Action-packed games',
                'updated_at' => now()
            ]);
    }
};

