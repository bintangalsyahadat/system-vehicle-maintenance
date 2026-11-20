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
        Schema::create('service_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_item_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date'); // Tanggal jatuh tempo
            $table->integer('custom_km')->nullable(); // Target KM khusus jika di-override user
            $table->boolean('is_snoozed')->default(false); // Apakah ditunda oleh user
            $table->string('status')->default('pending'); // pending, snoozed, completed, dismissed
            $table->string('channel')->default('whatsapp'); // whatsapp, web_push, in_app
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reminders');
    }
};
