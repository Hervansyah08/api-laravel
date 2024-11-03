<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    protected $table = 'person';

    protected $fillable = ['name', 'email'];

    function education()
    {
        return $this->hasMany(Education::class);
    }
}
