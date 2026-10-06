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
        Schema::table('clinics', function (Blueprint $table) {
            $table->integer('daily_quota')->nullable()->after('notes');
            $table->time('start_time')->nullable()->after('daily_quota');
            $table->time('end_time')->nullable()->after('start_time');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropColumn(['daily_quota', 'start_time', 'end_time']);
            $table->dropSoftDeletes();
        });
    }
};
