<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\IncidentType;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class IncidentTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = IncidentType::with('category')->select('incident_types.*');

            $searchTerm = $request->input('search.value');

            $query->where(function ($q) use ($searchTerm) {
                $q->where('incident_types.name', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('category', function ($q2) use ($searchTerm) {
                        $q2->where('name', 'LIKE', "%{$searchTerm}%");
                    });
            });

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('category', fn($row) => $row->category->name ?? '-')
                ->addColumn('share_regionally', function ($row) {
                    $checked = $row->share_regionally ? 'checked' : '';
                    return '<div class="form-check form-switch">
                                <input onclick="showShareToggleConfirm(' . $row->id . ')" type="checkbox" class="form-check-input" id="shareSwitch' . $row->id . '" ' . $checked . '>
                                <label for="shareSwitch' . $row->id . '" class="form-check-label"></label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '<div class="text-center"><div class="btn-group btn-group-sm" role="group">
                              <a href="' . route('admin.incident_types.edit', ['id' => $row->id]) . '" class="text-white btn btn-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                              <a href="#" onclick="showDeleteConfirm(' . $row->id . ')" class="text-white btn btn-danger" title="Delete"><i class="bi bi-trash"></i></a>
                            </div></div>';
                })
                ->rawColumns(['share_regionally', 'action'])
                ->make(true);
        }

        return view('backend.layouts.incident_type.index');
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        return view('backend.layouts.incident_type.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'share_regionally' => 'nullable',
        ]);

        IncidentType::create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'share_regionally' => $request->has('share_regionally') ? true : false,
        ]);

        return redirect()->route('admin.incident_types.index')->with('t-success', 'Incident type created successfully');
    }

    public function edit($id)
    {
        $incidentType = IncidentType::findOrFail($id);
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('backend.layouts.incident_type.edit', compact('incidentType', 'categories'));
    }

    public function update(Request $request, $id)
    {

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'share_regionally' => 'nullable',
        ]);

        $incidentType = IncidentType::findOrFail($id);
        $incidentType->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'share_regionally' => $request->has('share_regionally') ? true : false,
        ]);

        return redirect()->route('admin.incident_types.index')->with('t-success', 'Incident type updated successfully');
    }

    public function toggleShare(int $id): JsonResponse
    {
        $item = IncidentType::findOrFail($id);
        $item->share_regionally = !$item->share_regionally;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => $item->share_regionally ? 'Shared regionally' : 'Unshared regionally',
            'data' => $item,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $item = IncidentType::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Incident type deleted successfully',
        ]);
    }
}
