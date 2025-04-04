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

}
