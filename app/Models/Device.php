<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'main_feed_id',
        'parent_device_id',
        'device_type',
        'manufacturer',
        'device_model',
        'device_status',
        'parent_device',
        'parameters',
    ];

    protected $casts = [
        'parameters' => 'array',
        'parent_device' => 'boolean',
    ];

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
}
