<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AssignedVideo;
use App\Http\Controllers\Controller;

class AssignByVideoController extends Controller
{
    use ApiResponse;

    public function assignByVideo()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        // Get all assigned videos for this receiver
        $assignedVideos = AssignedVideo::with(['sender:id,name,email,avatar', 'trainingProgram:id,title,description,video'])
            ->where('receiver_id', $user->id)
            ->get();

        if ($assignedVideos->isEmpty()) {
            return $this->error([], 'No assigned videos found', 404);
        }

        // Group by sender
        $data = $assignedVideos->groupBy('sender_id')->map(function ($videos) {
            return [
                'sender' => $videos->first()->sender,
                'training_programs' => $videos->pluck('trainingProgram')
            ];
        })->values();

        return $this->success($data, 'Assigned videos retrieved successfully', 200);
    }
}
