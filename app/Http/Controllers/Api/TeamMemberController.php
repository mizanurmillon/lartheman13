<?php

namespace App\Http\Controllers\Api;

use App\Models\TeamMember;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AssignedVideo;
use App\Http\Controllers\Controller;

class TeamMemberController extends Controller
{
    use ApiResponse;

    public function allTeamMembers(Request $request)
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
        $query = TeamMember::with('user:id,name,email,avatar')
            ->where('church_profile_id', $churchProfileId)
            ->where('role', 'member');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teamMembers = $query->get();

        if ($teamMembers->isEmpty()) {
            return $this->error([], 'No members found in this church', 404);
        }

        return $this->success($teamMembers, 'Team Members fetched successfully', 200);
    }

    public function allTeamAdmins(Request $request)
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

        // church_profile admin role list 
        $query = TeamMember::with('user:id,name,email,avatar')
            ->where('church_profile_id', $churchProfileId)
            ->where('role', 'admin');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teamAdmins = $query->get();

        if ($teamAdmins->isEmpty()) {
            return $this->error([], 'No admins found in this church', 404);
        }

        return $this->success($teamAdmins, 'Team Admins fetched successfully', 200);
    }

    public function removeTeamMember($id)
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

        // Find the team member to be removed
        $teamMember = TeamMember::where('id', $id)
            ->where('church_profile_id', $churchProfileId)
            ->first();

        if (!$teamMember) {
            return $this->error([], 'Team Member Not Found in your church', 404);
        }

        // Prevent removing oneself
        if ($teamMember->user_id === $user->id) {
            return $this->error([], 'You cannot remove yourself from the team', 403);
        }

        // Check if the team member has assigned videos
        $hasAssignedVideos = AssignedVideo::where('receiver_id', $teamMember->user_id)->exists();

        if ($hasAssignedVideos) {
            return $this->error([], 'Cannot remove team member with assigned videos. Please reassign or delete their videos first.', 400);
        }

        // Remove the team member
        $teamMember->delete();

        return $this->success([], 'Team Member removed successfully', 200);
    }

    public function leaveTeam()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        // Logged-in user team member record
        $teamMember = TeamMember::where('user_id', $user->id)->first();

        if (!$teamMember) {
            return $this->error([], 'You are not assigned to any team', 404);
        }

        $churchProfileId = $teamMember->church_profile_id;

        // Count admins in this church team
        $adminCount = TeamMember::where('church_profile_id', $churchProfileId)
            ->where('role', 'admin')
            ->count();

        // If user is last admin -> block leaving
        if ($teamMember->role === 'admin' && $adminCount < 2) {
            return $this->error([], 'You cannot leave the team as the last admin', 403);
        }

        // Check if user has assigned videos
        $hasAssignedVideos = AssignedVideo::where('receiver_id', $user->id)->exists();

        if ($hasAssignedVideos) {
            return $this->error([], 'Cannot leave the team with assigned videos. Please reassign or delete your videos first.', 400);
        }

        // Leave the team
        $teamMember->delete();

        return $this->success([], 'You have left the team successfully', 200);
    }
}
