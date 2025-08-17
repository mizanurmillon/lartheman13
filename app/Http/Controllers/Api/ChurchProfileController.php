<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChurchProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChurchProfileController extends Controller
{
    use ApiResponse;

    public function churchProfile()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $teamMember = $user->teamMembers()->with('churchProfile')->first();

        if (!$teamMember) {
            return $this->error([], 'User is not a member of any team', 404);
        }

        if (!$teamMember->churchProfile) {
            return $this->error([], 'Church Profile not found', 404);
        }

        $data = ChurchProfile::select('id', 'church_name', 'phone', 'email', 'denomination', 'address', 'city_and_size')->where('id', $teamMember->church_profile_id)->first();

        return $this->success($data, 'Church Profile fetched successfully', 200);
    }

    public function updateChurchProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'church_name'   => 'required|string|max:255',
            'denomination' => 'nullable|string|max:255',
            'phone' => 'nullable|phone:AUTO',
            'address' => 'nullable|string|max:255',
            'city_and_size' => 'nullable|string|max:255',

        ], [
            'name.required' => 'Name is required',
            'phone.phone' => 'Invalid phone number',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $teamMember = $user->teamMembers()->with('churchProfile')->first();

        if (!$teamMember) {
            return $this->error([], 'User is not a member of any team', 404);
        }

        if (!$teamMember->churchProfile) {
            return $this->error([], 'Church Profile not found', 404);
        }

        $teamMember->churchProfile->update($request->only([
            'church_name',
            'denomination',
            'phone',
            'address',
            'city_and_size'
        ]));

        return $this->success($teamMember, 'Church Profile updated successfully', 200);
    }
}
