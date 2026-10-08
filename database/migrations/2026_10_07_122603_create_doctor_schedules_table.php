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
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            
            // e.g. Sunday, Monday, or 0, 1, 2
            $table->string('day_of_week'); 
            
            // e.g. 08:00
            $table->time('start_time')->nullable(); 
            // e.g. 15:00
            $table->time('end_time')->nullable();
            
            // Is the doctor available this day? (true for working days, false for days off)
            $table->boolean('is_working')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
