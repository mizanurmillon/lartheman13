<?php

namespace App\Http\Controllers\Web\Backend;

use Illuminate\Http\Request;
use App\Models\ReportIncident;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        $categoryWiseIncidents = ReportIncident::with('category')
            ->selectRaw('category_id, COUNT(*) as total')
            ->groupBy('category_id')
            ->get();
        return view('backend.layouts.reports.index', compact('categoryWiseIncidents'));
    }
}
