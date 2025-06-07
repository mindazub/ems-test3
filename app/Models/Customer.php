<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $connection = 'edis_system_data';
    protected $table = 'customers';
    protected $primaryKey = 'uid';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'uid', 'created_at', 'updated_at', 'installer_id', 'email', 'username',
    ];
}
