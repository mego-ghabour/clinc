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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('mrn')->unique(); // Medical Record Number
            $table->string('first_name');
            $table->string('father_name')->nullable();
            $table->string('grandfather_name')->nullable();
            $table->string('family_name');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('id_type')->nullable();
            $table->string('id_number')->nullable();
            $table->string('mobile')->nullable();
            $table->string('nationality')->default('سعودي');
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
