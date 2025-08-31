<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Media;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ReportIncident;
use App\Http\Controllers\Controller;
use App\Models\IncidentType;
use App\Models\Location;
use Yajra\DataTables\Facades\DataTables;

class SecurityEventController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $reviews = ReportIncident::with('media', 'category')->where('user_id', auth()->user()->id)->latest()->get();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $reviews->where('title', 'LIKE', "%$searchTerm%");
            }
            // dd($reviews);
            return DataTables::of($reviews)
                ->addIndexColumn()
                ->addColumn('event_category', function ($row) {
                    return $row->category ? $row->category->name : 'N/A';
                })
                ->addColumn('incident_type', fn($row) => $row->incidentType ? $row->incidentType->name : ($row->incident_type_other ?? 'N/A'))
                ->addColumn('description', function ($review) {
                    $desc = $review->description;
                    return strlen($desc) > 100 ? substr($desc, 0, 100) . '...' : $desc;
                })
                ->addColumn('incident_date', fn($row) => $row->incident_date)
                ->addColumn('media', function ($review) {
                    if ($review->media->isNotEmpty()) {
                        return '<img src="' . asset($review->media->first()->file_url) . '" width="100">';
                    }
                    return 'No Media';
                })
                ->addColumn('status_actions', function ($review) {
                    if ($review->status == 'approved') {
                        return '<span class="status-badge status-verified">Verified</span>';
                    }
                    return '<span class="status-badge status-pending">Pending</span>
                        <button class="btn btn-primary btn-sm show-event" data-bs-toggle="modal"
                            data-bs-target="#show-event-modal"
                            data-id="' . $review->id . '">Review</button>';
                })
                ->rawColumns(['media', 'status_actions', 'description'])
                ->make(true);
        }

        $categories = Category::where('status', 'active')->latest()->get();
        $locations  = Location::latest()->get();
        $verified   = ReportIncident::with('media', 'incidentType', 'category')
            ->where('user_id', auth()->user()->id)
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('backend.layouts.security_events.index', compact('categories', 'locations', 'verified'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'incident_type_id' => 'required', // will handle 'other' manually
            'incident_type_other' => 'nullable|string|max:255',
            'description' => 'required|string|max:2000',
            'location_id' => 'required|exists:locations,id',
            'incident_date' => 'required|date',
            'incident_time' => 'required|date_format:H:i',
            'file_url.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        // Determine incident_type_id and incident_type_other
        if ($request->incident_type_id === 'other') {
            $incidentTypeId = null;
            $incidentTypeOther = $request->incident_type_other;
        } else {
            $incidentTypeId = $request->incident_type_id;
            $incidentTypeOther = null;
        }

        $data = [
            'user_id' => auth()->id(),
            'category_id' => $request->category_id,
            'incident_type_id' => $incidentTypeId,
            'incident_type_other' => $incidentTypeOther,
            'description' => $request->description,
            'location_id' => $request->location_id,
            'share_regionally_mode' => $request->input('share_regionally_mode', 'own_region'),
            'incident_date' => $request->incident_date,
            'incident_time' => $request->incident_time,
            'status' => 'pending',
        ];

        $reportIncident = ReportIncident::create($data);

        // Handle file uploads
        if ($request->hasFile('file_url')) {
            foreach ($request->file('file_url') as $file) {
                $fileName = uploadImage($file, 'report_incidents');
                Media::create([
                    'report_incident_id' => $reportIncident->id,
                    'file_url' => $fileName,
                ]);
            }
        }

        return redirect()->route('admin.security_events.index')
            ->with('t-success', 'Security event created successfully');
    }


    public function edit($id)
    {
        $incident = ReportIncident::with(['media', 'category', 'location', 'incidentType'])->find($id);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found.',
            ], 404);
        }

        // Render incident type name with fallback
        $incidentTypeName = $incident->incidentType ? $incident->incidentType->name : $incident->incident_type_other;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $incident->id,
                'user_id' => $incident->user_id,
                'category_id' => $incident->category_id,
                'incident_type' => $incident->category ? $incident->category->name : null,
                'incident_type_id' => $incident->incident_type_id,
                'incident_type_name' => $incidentTypeName,
                'location_id' => $incident->location_id,
                'location_name' => $incident->location ? $incident->location->name : null,
                'share_regionally_mode' => $incident->share_regionally_mode,
                'description' => $incident->description,
                'incident_date' => $incident->incident_date,
                'incident_time' => $incident->incident_time,
                'status' => $incident->status,
                'media' => $incident->media->map(fn($m) => [
                    'id' => $m->id,
                    'file_url' => asset($m->file_url),
                ]),
            ],
        ]);
    }


    public function verify($id)
    {
        $incident = ReportIncident::find($id);

        if (!$incident) {
            return response()->json([
                'success' => false,
                'message' => 'Incident not found.',
            ], 404);
        }

        $incident->status = 'approved';
        $incident->save();

        return response()->json([
            'success' => true,
            'message' => 'Security event verified successfully',
        ]);
    }

    // public function verify($id)
    // {
    //     $data = ReportIncident::find($id);

    //     if (!$data) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Item not found.',
    //         ], 404);
    //     }

    //     $data->status = 'approved';
    //     $data->save();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Security event verified successfully',
    //     ]);
    // }

    public function getIncidentTypes(Request $request)
    {
        $categoryId = $request->category_id;
        $types = IncidentType::where('category_id', $categoryId)->get(['id', 'name', 'share_regionally']);
        return response()->json($types);
    }
}
