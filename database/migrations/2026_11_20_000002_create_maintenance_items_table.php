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
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_type')->default('motor'); // motor / mobil
            $table->string('item_name'); // Oli Mesin, Oli Gardan, Busi, Kampas Rem, V-Belt
            $table->integer('interval_km'); // misal 2500, 8000, 12000, 24000
            $table->integer('interval_months')->nullable(); // misal 2 bulan, 8 bulan
            $table->decimal('routine_cost', 12, 2)->default(0); // Biaya preventif
            $table->decimal('neglect_cost', 12, 2)->default(0); // Biaya turun mesin akibat telat
            $table->text('neglect_impact_desc')->nullable(); // Penjelasan edukasi resiko & SDG
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_items');
    }
};
