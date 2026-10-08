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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            
            // Core Relationships
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            // $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete(); // Optional for future

            // Visit Metadata
            $table->dateTime('visit_date');
            $table->string('type')->default('كشف جديد'); // كشف جديد, استشارة, متابعة
            $table->string('status')->default('قيد الانتظار'); // قيد الانتظار, جاري الكشف, مكتملة, ملغاة
            
            // Triage & Vitals (العلامات الحيوية - بواسطة التمريض)
            $table->string('blood_pressure')->nullable(); // الضغط
            $table->string('heart_rate')->nullable(); // النبض
            $table->string('temperature')->nullable(); // الحرارة
            $table->string('weight')->nullable(); // الوزن بالكيلو
            
            // Medical Record (بواسطة الطبيب)
            $table->text('chief_complaint')->nullable(); // شكوى المريض
            $table->text('diagnosis')->nullable(); // التشخيص الطبي
            $table->text('treatment_plan')->nullable(); // خطة العلاج والوصفة الطبية
            $table->text('doctor_notes')->nullable(); // ملاحظات سرية للطبيب
            
            // Billing Integration
            $table->decimal('consultation_fee', 10, 2)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
