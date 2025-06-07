<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlantController extends Model
{
        /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $connection = 'edis_system_data';

    protected $table = 'controllers';

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
        // plant_id is a string (uid/uuid), not integer
        return $this->belongsTo(Plant::class, 'plant_id', 'uid');
    }

    public function mainFeeds()
    {
        return $this->hasMany(MainFeed::class);
    }
}
