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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_academic_year_id')->constrained('student_academic_years');
            $table->foreignId('fee_id')->constrained('fees');
            $table->string('month')->nullable();
            $table->date('payment_date');
            $table->integer('original_amount');
            $table->integer('discount_applied');
            $table->integer('paid_amount');
            $table->string('payment_method');
            $table->string('receipt_pdf')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
