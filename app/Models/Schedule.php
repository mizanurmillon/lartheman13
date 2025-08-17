<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'church_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function churchProfile()
    {
        return $this->belongsTo(ChurchProfile::class, 'church_id');
    }

    public function AssingnMember()
    {
        return $this->hasMany(AssingnMember::class);
    }
}
