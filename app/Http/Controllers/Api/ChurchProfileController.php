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

        $data = ChurchProfile::with('denomination:id,name', 'city:id,name', 'state:id,name')->select('id', 'church_name', 'phone', 'email', 'denomination_id', 'address', 'city_id', 'state_id')->where('id', $teamMember->church_profile_id)->first();

        return $this->success($data, 'Church Profile fetched successfully', 200);
    }

    public function updateChurchProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'church_name'   => 'required|string|max:255',
            'denomination_id' => 'nullable|exists:denominations,id',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'state_id' => 'nullable|exists:states,id',

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
            'denomination_id',
            'phone',
            'address',
            'city_id',
            'state_id',
        ]));

        $teamMember->churchProfile->load('denomination', 'city', 'state');

        return $this->success($teamMember, 'Church Profile updated successfully', 200);
    }
}
