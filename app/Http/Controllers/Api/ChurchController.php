<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChurchProfile;
use App\Models\TeamMember;
use App\Notifications\UserNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
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
                'user_name' => 'nullable|string|unique:church_profiles,user_name',
                'email' => 'required|email|unique:church_profiles,email',
                'phone' => 'required|string|unique:church_profiles,phone',
                'denomination_id' => 'required|exists:denominations,id',
                'address' => 'required|string|max:500',
                'city_id' => 'required|exists:cities,id',
                'state_id' => 'nullable|exists:states,id',
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
                'city.required' => 'City is required',
                'state.required' => 'State is required',
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
            'denomination_id' => $request->denomination_id,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'state_id' => $request->state_id,
            'i_confirm' => $request->i_confirm,
        ]);

        TeamMember::create([
            'user_id' => $user->id,
            'church_profile_id' => $data->id,
            'role' => 'admin'
        ]);

        $data->load('denomination', 'city', 'state', 'teamMembers');

        if (!$data) {
            return $this->error([], 'Church profile not created', 500);
        }

        return $this->success($data, 'Church profile created successfully', 201);
    }


    public function getChurchProfile(Request $request)
    {
        $query = ChurchProfile::with('denomination:id,name', 'teamMembers.user:id,name,email,avatar')->select('id', 'church_name', 'address', 'denomination_id')->where('status', 'active');

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
        $data = ChurchProfile::with('denomination:id,name', 'city:id,name', 'state:id,name', 'teamMembers.user:id,name,email,avatar')
            ->select('id', 'denomination_id', 'city_id', 'state_id', 'church_name', 'email', 'phone', 'address')
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

        // check if user already belongs to a church
        $existingChurch = TeamMember::where('user_id', $user->id)->first();

        if ($existingChurch) {
            
            if ($existingChurch->church_profile_id == $id) {

                $existingChurch->delete();

                return $this->success([], 'Church profile left successfully', 200);
            }
            
            return $this->error([], 'You can only join one church at a time', 403);
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
