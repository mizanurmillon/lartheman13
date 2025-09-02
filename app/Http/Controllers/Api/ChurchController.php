<?php

namespace App\Http\Controllers\Api;

use App\Models\TeamMember;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ChurchProfile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ChurchController extends Controller
{
    use ApiResponse;

    public function create(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'church_name' => 'required|string|max:255',
                'email' => 'required|email|unique:church_profiles,email',
                'phone' => 'required|phone:AUTO|unique:church_profiles,phone',
                'denomination' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'city_and_size' => 'required|string|max:255',
                'i_confirm' => 'nullable|boolean',
            ],
            [
                'church_name.required' => 'Church name is required',
                'email.required' => 'Email is required',
                'email.unique' => 'Email already exists',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone already exists',
                'phone.phone' => 'Invalid phone number',
                'denomination.required' => 'Denomination is required',
                'address.required' => 'Address is required',
                'city_and_size.required' => 'City and size is required',
                'i_confirm.required' => 'I confirm is required',
            ]
        );

        if ($validator->fails()) {
            return $this->error($validator->errors(), $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $data = ChurchProfile::create([
            'church_name' => $request->church_name,
            'unique_id' => 'CH' . strtoupper(uniqid()),
            'user_name' => strtolower(preg_replace('/\s+/', '_', $request->church_name)),
            'email' => $request->email,
            'phone' => $request->phone,
            'denomination' => $request->denomination,
            'address' => $request->address,
            'city_and_size' => $request->city_and_size,
            'i_confirm' => $request->i_confirm,
        ]);

        TeamMember::create([
            'user_id' => $user->id,
            'church_profile_id' => $data->id,
            'role' => 'admin'
        ]);

        $data->load('teamMembers');

        if (!$data) {
            return $this->error([], 'Church profile not created', 500);
        }

        return $this->success($data, 'Church profile created successfully', 201);
    }


    public function getChurchProfile(Request $request)
    {
        $query = ChurchProfile::with('teamMembers.user:id,name,email,avatar')->select('id', 'church_name', 'denomination', 'address')->where('status', 'active');

        if ($request->has('church_name') && $request->church_name) {
            $query->where('church_name', 'like', '%' . $request->church_name . '%');
        }

        $data = $query->get();

        if (!$data) {
            return $this->error([], 'Church profile not found', 404);
        }

        return $this->success($data, 'Church profile fetched successfully', 200);
    }

    public function getChurchProfileById($id)
    {
        $data = ChurchProfile::with('teamMembers.user:id,name,email,avatar')
            ->select('id', 'church_name', 'email', 'phone', 'denomination', 'address', 'city_and_size')
            ->where('status', 'active')
            ->where('id', $id)
            ->first();

        if (!$data) {
            return $this->error([], 'Church profile not found', 404);
        }

        return $this->success($data, 'Church profile fetched successfully', 200);
    }

    public function joinChurch(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $church = ChurchProfile::find($id);

        if (!$church) {
            return $this->error([], 'Church profile not found', 404);
        }

        $teamMember = TeamMember::where('user_id', $user->id)->where('church_profile_id', $id)->first();

        if($teamMember) {
            
           $teamMember->delete();

           return $this->success([], 'Church profile left successfully', 200);
        }

        $data = TeamMember::create([
            'user_id' => $user->id,
            'church_profile_id' => $id,
            'role' => 'member'
        ]);

        if (!$data) {
            return $this->error([], 'Church profile not joined', 500);
        }

        return $this->success($data, 'Church profile joined successfully', 201);
    }
}
