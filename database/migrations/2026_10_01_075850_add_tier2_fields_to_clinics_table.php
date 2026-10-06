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
            $table->string('color')->nullable()->default('#14b8a6')->after('icon');
            $table->foreignId('head_doctor_id')->nullable()->constrained('users')->nullOnDelete()->after('end_time');
            $table->integer('sort_order')->default(0)->after('head_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clinics', function (Blueprint $table) {
            $table->dropForeign(['head_doctor_id']);
            $table->dropColumn(['color', 'head_doctor_id', 'sort_order']);
        });
    }
};
