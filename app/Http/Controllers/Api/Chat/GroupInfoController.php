<?php

namespace App\Http\Controllers\Api\Chat;

use App\Models\Group;
use App\Models\Participant;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GroupInfoController extends Controller
{
    use ApiResponse;
    /**
     * Handle the incoming request to get group info.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function groupInfo(Request $request, int $id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $group = Group::find($id);


        if (!$group) {
            return $this->error([], 'Group not found', 404);
        }
        return $this->success($group, 'Group info retrieved successfully', 200);
    }

    public function groupMember(Request $request, int $id)
    {
        $data = Participant::with('participant:id,name,email,avatar')
            ->where('conversation_id', $id)
            ->get();

        if (!$data) {
            return $this->error([], 'Group not found', 404);
        }

        return $this->success($data, 'Group members retrieved successfully', 200);
    }
}
