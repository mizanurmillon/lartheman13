<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Location::select('locations.*');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<div class="text-center"><div class="btn-group btn-group-sm" role="group">
                              <a href="' . route('admin.locations.edit', ['id' => $row->id]) . '" class="text-white btn btn-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                              <a href="#" onclick="showDeleteConfirm(' . $row->id . ')" class="text-white btn btn-danger" title="Delete"><i class="bi bi-trash"></i></a>
                            </div></div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('backend.layouts.location.index');
    }

    public function create()
    {
        return view('backend.layouts.location.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Location::create(['name' => $request->name]);

        return redirect()->route('admin.locations.index')->with('t-success', 'Location created successfully');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return view('backend.layouts.location.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location = Location::findOrFail($id);
        $location->name = $request->name;
        $location->save();

        return redirect()->route('admin.locations.index')->with('t-success', 'Location updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully',
        ]);
    }
}
