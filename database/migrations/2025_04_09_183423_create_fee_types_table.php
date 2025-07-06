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
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('foundation_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('frequency')->default('monthly');
             $table->foreignId('class_id')->nullable()->change();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
