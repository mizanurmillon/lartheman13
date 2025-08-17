<?php

namespace App\Http\Controllers\Api;

use App\Models\TeamMember;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamMemberController extends Controller
{
    use ApiResponse;

    public function allTeamMembers()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        // Logged-in user church_profile id
        $churchProfileId = $user->teamMembers()->pluck('church_profile_id')->first();

        if (!$churchProfileId) {
            return $this->error([], 'User is not assigned to any church', 404);
        }

        // church_profile member role list 
        $teamMembers = TeamMember::with('user:id,name,email,avatar')
            ->where('church_profile_id', $churchProfileId)
            ->where('role', 'member')
            ->get();

        if ($teamMembers->isEmpty()) {
            return $this->error([], 'No members found in this church', 404);
        }

        return $this->success($teamMembers, 'Team Members fetched successfully', 200);
    }
}
