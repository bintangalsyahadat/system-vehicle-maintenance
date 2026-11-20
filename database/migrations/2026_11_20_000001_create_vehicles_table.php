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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('vehicle_type')->default('motor'); // motor / mobil
            $table->string('brand'); // Honda, Yamaha, dll
            $table->string('model_name'); // Vario 160, NMAX, dll
            $table->string('license_plate')->nullable(); // B 4120 KXZ
            $table->integer('manufacture_year')->nullable();
            $table->integer('initial_odometer_km')->default(0);
            $table->float('daily_average_km')->default(20.0);
            $table->integer('last_recalibrate_km')->default(0);
            $table->date('last_recalibrate_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
