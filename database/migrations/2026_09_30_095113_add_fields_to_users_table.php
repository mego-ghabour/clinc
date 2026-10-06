<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('name')->nullable();
            $table->string('role')->default('طبيب')->after('password');
            $table->string('status')->default('نشط')->after('role');
            $table->timestamp('last_login_at')->nullable()->after('status');
            
            // Assuming we don't strictly need email for login based on the UI, we can make it nullable.
            // But we can't easily alter it to nullable if sqlite without doctrine/dbal. Let's just leave it alone or use a default.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'status', 'last_login_at']);
        });
    }
};
