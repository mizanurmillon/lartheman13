<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'report_incident_id' => 'integer',
    ];

    public function reportIncident()
    {
        return $this->belongsTo(ReportIncident::class);
    }
}
