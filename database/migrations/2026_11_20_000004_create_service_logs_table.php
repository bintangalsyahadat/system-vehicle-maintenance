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
        Schema::create('service_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_item_id')->nullable()->constrained()->nullOnDelete();
            $table->date('service_date');
            $table->integer('odometer_at_service');
            $table->decimal('actual_cost', 12, 2)->default(0);
            $table->string('workshop_name')->nullable(); // AHASS, Bengkel Mandiri, dll
            $table->text('notes')->nullable();
            $table->string('receipt_image_url')->nullable(); // Foto nota bengkel jika ada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_logs');
    }
};
