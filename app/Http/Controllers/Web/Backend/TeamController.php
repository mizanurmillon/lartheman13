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
                    if (!$data->avatar) {
                        $url = asset('backend/images/placeholder/image_placeholder.png');
                    }
                    return '<img src="' . $url . '" alt="Avatar" class="rounded-circle" width="50" height="50">';
                })
                ->addColumn('church_name', function ($data) {
                    return $data->churchProfile->church_name;
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
                // ->addColumn('action', function ($data) {
                //     $status = ' <div class="form-check form-switch">';
                //     $status .= ' <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                //     if ($data->user->status == "active") {
                //         $status .= "checked";
                //     }
                //     $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                //     return $status;
                // })
                ->rawColumns(['status','church_name','avatar','role','name','email'])
                ->make(true);
        }
        return view('backend.layouts.team.index');
    }
}
