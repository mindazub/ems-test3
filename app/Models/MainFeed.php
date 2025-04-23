<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MainFeed extends Model
{
    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

}
