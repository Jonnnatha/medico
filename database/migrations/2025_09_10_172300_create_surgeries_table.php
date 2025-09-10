<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->string('patient_name');
            $table->string('surgery_type')->nullable();
            $table->string('room')->nullable();
            $table->dateTime('starts_at');
            $table->unsignedInteger('duration_min');
            $table->dateTime('ends_at');
            $table->string('status')->default('scheduled');
            $table->boolean('is_conflict')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->foreignId('canceled_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgeries');
    }
};
