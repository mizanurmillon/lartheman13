<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChurchProfile extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'denomination_id' => 'integer',
        'city_id' => 'integer',
        'state_id' => 'integer',

    ];

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    public function denomination()
    {
        return $this->belongsTo(Denomination::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

}
