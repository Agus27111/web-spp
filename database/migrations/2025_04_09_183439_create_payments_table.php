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
            $table->foreignId('foundation_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_academic_year_id')->constrained('student_academic_years');
            $table->foreignId('fee_id')->constrained('fees');
            $table->string('month')->nullable();
            $table->date('payment_date');
            $table->integer('original_amount')->default(0);
            $table->integer('discount_applied')->default(0);
            $table->integer('paid_amount')->default(0);
            $table->string('payment_method');
            $table->json('applied_discounts')->nullable();
            $table->string('payment_proof')->nullable();
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
