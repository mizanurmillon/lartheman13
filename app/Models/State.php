<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'city_id' => 'integer',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
