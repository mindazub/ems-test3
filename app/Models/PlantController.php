<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlantController extends Model
{
    protected $fillable = [
        'plant_id',
        'name',
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

    public function plant()
    {
        return $this->belongsTo(Plant::class);
    }

    public function mainFeeds()
    {
        return $this->hasMany(MainFeed::class);
    }
}
