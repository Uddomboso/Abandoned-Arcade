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

