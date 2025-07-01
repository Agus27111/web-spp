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
        Schema::create('foundation_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); // Hapus ->unique()
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['email', 'status'], 'unique_email_for_pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foundation_requests');
    }
};
