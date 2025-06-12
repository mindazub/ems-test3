<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plant extends Model
{
    protected $fillable = [
        'name',
        'owner_email',
        'status',
        'capacity',
        'latitude',
        'longitude',
        'last_updated',
        'uuid',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        // Handle both uuid and uid lookups for backward compatibility
        return $this->where('uuid', $value)
                   ->orWhere('uid', $value)
                   ->first();
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function controllers()
    {
        return $this->hasMany(PlantController::class);
    }

    public function aggregatedDataSnapshots()
    {
        return $this->hasMany(AggregatedDataSnapshot::class);
    }
}
