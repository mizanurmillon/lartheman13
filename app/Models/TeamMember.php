<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'church_profile_id' => 'integer',
        'i_confirm' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function churchProfile()
    {
        return $this->belongsTo(ChurchProfile::class);
    }
}
