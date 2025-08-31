<?php

namespace App\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use App\Models\ReportIncident;
use App\Models\Category;
use App\Models\IncidentType;
use App\Models\Location;
use App\Models\ChurchProfile;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Fetch filter options
        $categories    = Category::all();
        $incidentTypes = IncidentType::all();
        $locations     = Location::all();
        $churches      = ChurchProfile::all();

        // Query builder for report incidents
        $query = ReportIncident::with(['category', 'incidentType', 'location', 'churchProfile']);

        // Apply filters if selected
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->incident_type_id) {
            $query->where('incident_type_id', $request->incident_type_id);
        }

        if ($request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->church_profile_id) {
            $query->where('church_profile_id', $request->church_profile_id);
        }

        // Group by category for chart
        $categoryWiseIncidents = $query->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->get();

        return view('backend.layouts.reports.index', compact(
            'categoryWiseIncidents',
            'categories',
            'incidentTypes',
            'locations',
            'churches'
        ));
    }
}
