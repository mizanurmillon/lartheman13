<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    protected $guarded = [];
    protected $casts = [
        'share_regionally' => 'boolean',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reportIncidents()
    {
        return $this->hasMany(ReportIncident::class);
    }
}
