<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('radiology_requests', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained()->nullOnDelete(); // Which visit requested this
            
            // Request Details
            $table->string('type'); // e.g. X-Ray, MRI, CT Scan, Ultrasound
            $table->string('body_part')->nullable(); // e.g. Chest, Left Arm
            $table->text('clinical_notes')->nullable(); // Doctor's notes for the radiologist
            
            // Status
            $table->string('status')->default('pending'); // pending, completed, cancelled
            
            // Results (Filled by Radiologist)
            $table->text('report_text')->nullable(); // The official medical report
            $table->string('image_path')->nullable(); // Uploaded image (DICOM/JPG/PNG)
            $table->foreignId('radiologist_id')->nullable()->constrained('users')->nullOnDelete(); // Who did the scan
            $table->dateTime('completed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radiology_requests');
    }
};
