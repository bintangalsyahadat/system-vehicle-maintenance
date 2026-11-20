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
        Schema::create('ai_scan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->string('image_url')->nullable();
            $table->string('detected_indicator'); // Check Engine, Suhu Radiator, Aki, dll
            $table->string('severity_level')->default('CRITICAL'); // CRITICAL, WARNING, INFO
            $table->text('emergency_action'); // Protokol darurat bertahap
            $table->decimal('estimated_cost_min', 12, 2)->nullable();
            $table->decimal('estimated_cost_max', 12, 2)->nullable();
            $table->boolean('is_resolved')->default(false); // Sudah diperbaiki atau belum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_scan_histories');
    }
};
