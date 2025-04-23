<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['name', 'plant_id'];

    public function plant()
    {
        return $this->belongsTo(Plant::class);
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

protected $casts = [
    'parameters' => 'array',
];



}
