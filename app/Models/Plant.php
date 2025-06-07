<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Plant extends Model
{
    protected $connection = 'edis_system_data';
    protected $table = 'plants';

    protected $fillable = [
        'uid',
        'latitude',
        'longitude',
        'status',
        'capacity',
        'owner',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uid';
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = (string) Str::uuid();
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(Customer::class, 'owner', 'uid');
    }
}
