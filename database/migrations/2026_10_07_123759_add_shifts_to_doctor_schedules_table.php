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
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->json('shifts')->nullable()->after('is_working'); // Stores array of ['start' => '08:00', 'end' => '12:00']
            $table->integer('consultation_duration')->default(15)->after('shifts'); // in minutes
            
            $table->dropColumn(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctor_schedules', function (Blueprint $table) {
            $table->dropColumn(['shifts', 'consultation_duration']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
        });
    }
};
