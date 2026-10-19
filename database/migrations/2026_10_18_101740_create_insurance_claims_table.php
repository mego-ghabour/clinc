<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_number')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->foreignId('insurance_company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('claim_date');
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('patient_copay', 10, 2)->default(0.00);
            $table->decimal('insurance_share', 10, 2)->default(0.00);
            $table->string('status')->default('معلقة'); // معلقة, موافقة مبدئية, مقبولة, مرفوضة, مسددة
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('settled_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
