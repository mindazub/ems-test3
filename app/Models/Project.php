<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'start_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }

    protected $casts = [
        'start_date' => 'date',
    ];

}
