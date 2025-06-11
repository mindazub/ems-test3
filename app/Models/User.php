<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'uuid',
        'settings',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
            // Set default settings when creating a new user
            if (empty($model->settings)) {
                $model->settings = [
                    'time_format' => '24'
                ];
            }
        });
    }

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
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'settings' => 'array',
        ];
    }
    /**
     *  Admin role things
     *
     *  @return bool
     */

     public function isAdmin(): bool
     {
         return $this->role === 'admin';
     }


    public function hasRole(string $role)
    {
        return $this->role === $role;
    }

    /**
     * Get the user's preferred time format
     * 
     * @return string
     */
    public function getTimeFormat(): string
    {
        return $this->settings['time_format'] ?? '24';
    }

    /**
     * Format a time according to user's preference
     * 
     * @param string|\Carbon\Carbon $time
     * @return string
     */
    public function formatTime($time): string
    {
        if (is_string($time)) {
            $time = \Carbon\Carbon::parse($time);
        }
        
        if ($this->getTimeFormat() === '12') {
            return $time->format('g:i:s A'); // 2:30:45 PM
        } else {
            return $time->format('H:i:s'); // 14:30:45
        }
    }

    /**
     * Format a datetime according to user's preference
     * 
     * @param string|\Carbon\Carbon $datetime
     * @return string
     */
    public function formatDateTime($datetime): string
    {
        if (is_string($datetime)) {
            $datetime = \Carbon\Carbon::parse($datetime);
        }
        
        if ($this->getTimeFormat() === '12') {
            return $datetime->format('Y-m-d g:i:s A'); // 2025-06-11 2:30:45 PM
        } else {
            return $datetime->format('Y-m-d H:i:s'); // 2025-06-11 14:30:45
        }
    }
}
