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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('whatsapp_notifications_enabled')->default(false)->after('phone_whatsapp');
            $table->timestamp('whatsapp_verified_at')->nullable()->after('whatsapp_notifications_enabled');
            $table->string('whatsapp_verification_code', 6)->nullable()->after('whatsapp_verified_at');
            $table->timestamp('whatsapp_verification_expires_at')->nullable()->after('whatsapp_verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_notifications_enabled',
                'whatsapp_verified_at',
                'whatsapp_verification_code',
                'whatsapp_verification_expires_at',
            ]);
        });
    }
};
