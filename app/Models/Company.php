<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'project_id'];


    public function plants()
    {
        return $this->hasMany(Plant::class);
    }

}
