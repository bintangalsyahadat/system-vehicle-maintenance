<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'phone_whatsapp', 'whatsapp_notifications_enabled'])]
#[Hidden(['password', 'remember_token', 'whatsapp_verification_code'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Check whether the user has a verified WhatsApp number and enabled notifications.
     */
    public function canReceiveWhatsAppNotifications(): bool
    {
        return !empty($this->phone_whatsapp)
            && $this->whatsapp_verified_at !== null
            && (bool) $this->whatsapp_notifications_enabled;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'whatsapp_notifications_enabled' => 'boolean',
            'whatsapp_verified_at' => 'datetime',
            'whatsapp_verification_expires_at' => 'datetime',
        ];
    }
}
