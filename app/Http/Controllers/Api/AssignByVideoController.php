<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignedVideo;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AssignByVideoController extends Controller
{
    use ApiResponse;

    public function assignByVideo()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $assignedVideos = AssignedVideo::with([
            'trainingProgram:id,title,description,video',
            'sender:id,name,email,avatar'
        ])
            ->where('receiver_id', $user->id)
            ->get();

        if ($assignedVideos->isEmpty()) {
            return $this->error([], 'No assigned video found for this user', 404);
        }

        $sender = $assignedVideos->first()->sender;
        $totalVideos = $assignedVideos->count();
        $completedVideos = $assignedVideos->where('status', 'completed')->count();
        $response = [
            'total_videos' => $totalVideos,
            'completed_videos' => $completedVideos,
            'assign_by' => $sender ? [
                'id' => $sender->id,
                'name' => $sender->name,
                'email' => $sender->email,
                'avatar' => $sender->avatar,
            ] : null,
            'assigned_videos' => $assignedVideos->map(function ($video) {
                $statusLabel = match ($video->status) {
                    'completed' => 'Completed',
                    'watching' => 'Watching',
                    default => 'Not Watch',
                };

                return [
                    'id' => $video->id,
                    'training_program' => $video->trainingProgram,
                    'status' => $video->status,
                    'status_label' => $statusLabel,
                ];
            }),
        ];

        return $this->success($response, 'Assigned video retrieved successfully', 200);
    }


    public function updateVideoStatus($id, Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'Unauthorized', 401);
        }

        $assignedVideo = AssignedVideo::where('id', $id)
            ->where('receiver_id', $user->id)
            ->first();

        if (!$assignedVideo) {
            return $this->error([], 'Assigned video not found', 404);
        }

        $request->validate([
            'status' => 'required|in:in_progress,watching,completed'
        ]);

        $assignedVideo->status = $request->status;
        $assignedVideo->save();

        return $this->success($assignedVideo, 'Video status updated successfully', 200);
    }
}
