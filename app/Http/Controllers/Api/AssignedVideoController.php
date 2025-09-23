<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignedVideo;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

use function Pest\Laravel\delete;

class AssignedVideoController extends Controller
{
    use ApiResponse;


    public function assignVideo(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $exitingAssignment = AssignedVideo::where('receiver_id', $request->receiver_id)
            ->where('training_program_id', $request->training_program_id)
            ->first();

        if ($exitingAssignment) {
            $exitingAssignment->delete(); 
            return $this->error(['assigned' => false], 'Unassigned video successfully', 200);
        }

        $data = AssignedVideo::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'training_program_id' => $request->training_program_id,
        ]);

        if (!$data) {
            return $this->error([], 'Failed to assign video', 500);
        }

        return $this->success([
            'assigned' => true, 
            'data' => $data
        ], 'Video assigned successfully', 201);
    }

    public function allAssignedVideos()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $assignedVideos = AssignedVideo::with(['receiver:id,name,email,avatar'])
            ->where('sender_id', $user->id)
            ->get()
            ->groupBy('receiver_id');

        if ($assignedVideos->isEmpty()) {
            return $this->error([], 'No assigned videos found', 404);
        }

        $data = $assignedVideos->map(function ($videos, $receiverId) {
            $receiver = $videos->first()->receiver;

            $total = $videos->count();
            $completed = $videos->where('status', 'completed')->count();
            $watching = $videos->where('status', 'watching')->count();

            if ($completed === $total) {
                $status = 'completed';
            } elseif ($watching > 0) {
                $status = 'watching';
            } else {
                $status = 'assigned';
            }

            return [
                'receiver' => $receiver,
                'status'   => $status,
            ];
        })->values();

        return $this->success($data, 'Assigned videos retrieved successfully', 200);
    }


    public function singleAssignedVideo($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        // Get a single assigned video for this receiver
        $assignedVideo = User::with(['assignedVideos' => function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->with('trainingProgram');
        }])
            ->select('id', 'name', 'email', 'avatar')
            ->find($id);

        $totalVideos = $assignedVideo ? $assignedVideo->assignedVideos->count() : 0;
        $completedVideos = $assignedVideo ? $assignedVideo->assignedVideos->where('status', 'completed')->count() : 0;

        if (!$assignedVideo) {
            return $this->error([], 'No assigned video found for this user', 404);
        }

        $data = [
            'total_videos' => $totalVideos,
            'completed_videos' => $completedVideos,
            'receiver' => $assignedVideo,
        ];

        return $this->success($data, 'Assigned video retrieved successfully', 200);
    }
}
