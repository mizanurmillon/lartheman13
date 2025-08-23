<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Media;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ReportIncident;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class SecurityEventController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = ReportIncident::with('media')->where('user_id', auth()->user()->id)->latest()->get();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $reviews->where('title', 'LIKE', "%$searchTerm%");
            }

            return DataTables::of($reviews)
                ->addIndexColumn()
                ->addColumn('media', function ($review) {
                    if ($review->media->isNotEmpty()) {
                        return '<img src="' . asset($review->media->first()->file_url) . '" width="100">';
                    }
                    return 'No Media';
                })
                ->addColumn('description', function ($review) {
                    $description       = $review->description;
                    $short_description = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                    return '<p>' . $short_description . '</p>';
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
        $verified = ReportIncident::with('media')->where('user_id', auth()->user()->id)->where('status', 'approved')->latest()->get();
        return view('backend.layouts.security_events.index', compact('categories', 'verified'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'location' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'incident_time' => 'required|date_format:H:i',
            'file_url' => 'required|array',
            'file_url.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $user = auth()->user();

        $data = [
            'user_id'          => $user->id,
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'description'      => $request->description,
            'location'         => $request->location,
            'alerts_types'     => 'public',
            'incident_date'    => $request->incident_date,
            'incident_time'    => $request->incident_time,
            'status'           => 'pending',
        ];

        $reportIncident = ReportIncident::create($data);

        // Then handle file uploads (if any)
        if ($request->hasFile('file_url')) {
            foreach ($request->file('file_url') as $file) {
                $fileName = uploadImage($file, 'report_incidents');
                Media::create([
                    'report_incident_id' => $reportIncident->id,
                    'file_url'           => $fileName,
                ]);
            }
        }

        return redirect()->route('admin.security_events.index')->with('t-success', 'Security event created successfully');
    }

    public function edit($id)
    {
        $categories = Category::where('status', 'active')->latest()->get();
        $data = ReportIncident::with('media')->find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $data->id,
                'title' => $data->title,
                'description' => $data->description,
                'location' => $data->location,
                'incident_date' => $data->incident_date,
                'incident_time' => $data->incident_time,
                'category_id' => $data->category_id,
                'media' => $data->media ? $data->media->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'file_url' => asset($m->file_url),
                    ];
                }) : [],
            ],
            'categories' => $categories,
        ]);
    }

    public function verify($id)
    {
        $data = ReportIncident::find($id);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found.',
            ], 404);
        }

        $data->status = 'approved';
        $data->save();

        return response()->json([
            'success' => true,
            'message' => 'Security event verified successfully',
        ]);
    }
}
