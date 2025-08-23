<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\ReportIncident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $totalLastFiveWeeksIncidents = ReportIncident::where('created_at', '>=', now()->subWeeks(5))
            ->count();

        $latestIncidents = ReportIncident::with(['user', 'churchProfile', 'category', 'media'])
            ->latest()
            ->take(5)
            ->get();
        return view('backend.layouts.index', compact('totalLastFiveWeeksIncidents', 'latestIncidents'));
    }
}
