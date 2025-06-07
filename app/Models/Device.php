<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Device extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'edis_system_data';

    protected $table = 'devices';


    protected $fillable = [
        'main_feed_id',
        'parent_device_id',
        'device_type',
        'manufacturer',
        'device_model',
        'device_status',
        'parent_device',
        'parameters',
        'uuid',
    ];

    protected $casts = [
        'parameters' => 'array',
        'parent_device' => 'boolean',
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

    public function mainFeed()
    {
        return $this->belongsTo(MainFeed::class);
    }

    public function parent()
    {
        return $this->belongsTo(Device::class, 'parent_device_id');
    }

    public function assignedDevices()
    {
        return $this->hasMany(Device::class, 'parent_device_id');
    }

    public function getPlantAttribute()
    {
        // $this->mainFeed could be null if not set
        return $this->mainFeed ? $this->mainFeed->plant : null;
    }

}
