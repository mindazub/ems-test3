<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];


    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function mainFeeds()
    {
        return $this->hasMany(MainFeed::class);
    }


}
