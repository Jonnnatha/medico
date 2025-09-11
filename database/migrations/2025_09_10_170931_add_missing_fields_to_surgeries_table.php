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
        Schema::table('surgeries', function (Blueprint $table) {
            $table->string('surgery_type')->nullable();
            $table->string('room')->nullable();
            $table->unsignedInteger('duration_min')->nullable();
            $table->boolean('is_conflict')->default(false);
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->foreignId('canceled_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            $table->dropColumn(['surgery_type', 'room', 'duration_min', 'is_conflict']);
            $table->dropConstrainedForeignId('confirmed_by');
            $table->dropConstrainedForeignId('canceled_by');
        });
    }
};
