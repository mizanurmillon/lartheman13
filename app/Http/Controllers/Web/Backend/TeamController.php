<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = TeamMember::with('churchProfile','user')->get();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('name', 'LIKE', "%$searchTerm%");
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($data) {
                    return $data->user->name;
                })
                ->addColumn('email', function ($data) {
                    return $data->user->email;
                })
                ->addColumn('avatar', function ($data) {
                    $url = asset($data->user->avatar);
                    if (!$data->user->avatar) {
                        $url = asset('backend/images/placeholder/image_placeholder.png');
                    }
                    return '<img src="' . $url . '" alt="Avatar" class="rounded-circle" width="50" height="50">';
                })
                ->addColumn('church_name', function ($data) {
                    return $data->churchProfile->church_name;
                })
                ->addColumn('unique_id', function ($data) {
                    return $data->churchProfile->unique_id;
                })
                ->addColumn('user_name', function ($data) {
                    return $data->churchProfile->user_name;
                })
                ->addColumn('denomination', function ($data) {
                    return $data->churchProfile->denomination;
                })
                ->addColumn('address', function ($data) {
                    return $data->churchProfile->address;
                })
                ->addColumn('city', function ($data) {
                    return $data->churchProfile->city;
                })
                ->addColumn('state', function ($data) {
                    return $data->churchProfile->state ?? 'N/A';
                })
                ->addColumn('role', function ($data) {
                    if ($data->role == 'admin') {
                        $role = '<span class="badge badge-primary">Church Admin</span>';
                    } else {
                        $role = '<span class="badge badge-info">Member</span>';
                    }
                    return $role;
                })
                ->addColumn('status', function ($data) {
                   if ($data->user->status == 'active') {
                        $status = '<span class="badge badge-success">Active</span>';
                    } else {
                        $status = '<span class="badge badge-danger">Inactive</span>';
                    }
                    return $status;
                })
                ->addColumn('action', function ($row) {
                    return '<div class="text-center"><div class="btn-group btn-group-sm" role="group">
                              <a href="' . route('admin.team.show', ['id' => $row->id]) . '" class="text-white btn btn-primary" title="Edit"><i class="bi bi-eye"></i></a>';
                })
                ->rawColumns(['status','church_name','avatar','role','name','email','denomination', 'address', 'action', 'city', 'unique_id', 'user_name', 'state'])
                ->make(true);
        }
        return view('backend.layouts.team.index');
    }

    public function show($id)
    {
        $teamMember = TeamMember::with('churchProfile', 'user')->findOrFail($id);
        return view('backend.layouts.team.show', compact('teamMember'));
    }
}
   
