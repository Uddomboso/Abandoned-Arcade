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
            $table->string('workos_id')->nullable()->unique()->after('id');
            $table->string('workos_organization_id')->nullable()->after('workos_id');
            $table->boolean('is_workos_user')->default(false)->after('workos_organization_id');
            // Make password nullable for WorkOS users
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['workos_id', 'workos_organization_id', 'is_workos_user']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
