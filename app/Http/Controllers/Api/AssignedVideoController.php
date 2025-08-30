<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignedVideo;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AssignedVideoController extends Controller
{
    use ApiResponse;


    public function assignVideo(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error(null, 'Unauthorized', 401);
        }

        $exitingAssignment = AssignedVideo::where('receiver_id', $request->receiver_id)
            ->where('training_program_id', $request->training_program_id)
            ->first();

        if ($exitingAssignment) {
            return $this->error([], 'This video has already been assigned to the selected user', 409);
        }

        $data = AssignedVideo::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'training_program_id' => $request->training_program_id,
        ]);

        if (!$data) {
            return $this->error(null, 'Failed to assign video', 500);
        }

        return $this->success($data, 'Video assigned successfully', 201);
    }

    public function allAssignedVideos()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $data = AssignedVideo::with(['receiver:id,name,email,avatar'])
            ->where('sender_id', $user->id)
            ->get()
            ->unique('receiver_id')
            ->values();

        if ($data->isEmpty()) {
            return $this->error([], 'No assigned videos found', 404);
        }

        return $this->success($data, 'Assigned videos retrieved successfully', 200);
    }

    public function singleAssignedVideo($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        // Get all assigned videos for this receiver
        $assignedVideos = AssignedVideo::with('trainingProgram')
            ->where('sender_id', $user->id)
            ->where('receiver_id', $id)
            ->get();

        if ($assignedVideos->isEmpty()) {
            return $this->error([], 'No assigned videos found for this user', 404);
        }

        // Prepare data: group all training programs under a single receiver
        $receiverData = [
            'id' => $assignedVideos->first()->receiver->id,
            'name' => $assignedVideos->first()->receiver->name,
            'email' => $assignedVideos->first()->receiver->email,
            'avatar' => $assignedVideos->first()->receiver->avatar,
            'training_programs' => $assignedVideos->pluck('trainingProgram')
        ];

        return $this->success($receiverData, 'Assigned videos for the user retrieved successfully', 200);
    }
}
