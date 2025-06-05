<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AggregatedDataSnapshot extends Model
{
    protected $fillable = [
        'plant_id',
        'data',
        'uuid',
    ];

    protected $casts = [
        'data' => 'array',
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
}
