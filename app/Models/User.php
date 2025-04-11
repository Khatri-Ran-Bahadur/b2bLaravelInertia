<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_role',
        'status',
        'fcm_token',
        'firebase_id',
        'google_id',
        'apple_id',
        'image',
        'username',
        'phone',
        'email_verified_at',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Set temporary username as user ID
            $user->username = 'user_' . $user->id;
        });

        static::updating(function ($user) {
            // Generate proper username only when profile is being updated
            if ($user->isDirty(['first_name', 'last_name']) && !empty($user->first_name)) {
                $user->username = $user->generateUniqueUsername();
            }
        });
    }

    protected function generateUniqueUsername()
    {
        // Get the first name and last name, use empty string if null
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';

        // Generate base username
        $baseUsername = Str::lower(
            Str::ascii($firstName) .
                ($lastName ? '-' . Str::ascii($lastName) : '')
        );

        // Remove any special characters and spaces
        $baseUsername = preg_replace('/[^a-z0-9.]/', '', $baseUsername);

        // If base username is empty, use a fallback
        if (empty($baseUsername)) {
            $baseUsername = 'user_' . $this->id;
        }

        $username = $baseUsername;
        $count = 1;

        // Keep trying until we find a unique username
        while (static::where('username', $username)
            ->where('id', '!=', $this->id)
            ->exists()
        ) {
            $username = $baseUsername . $count;
            $count++;
        }

        return $username;
    }

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_users')
            ->withPivot(['role', 'status'])
            ->withTimestamps();
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }
}
