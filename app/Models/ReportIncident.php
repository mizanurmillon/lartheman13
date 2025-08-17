<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportIncident extends Model
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
        'category_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function churchProfile()
    {
        return $this->belongsTo(ChurchProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
