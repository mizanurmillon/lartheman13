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
    ];

    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }
}
