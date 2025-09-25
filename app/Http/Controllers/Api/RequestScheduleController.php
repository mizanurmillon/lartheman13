<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AssingnMember;
use App\Enum\NotificationType;
use App\Http\Controllers\Controller;
use App\Notifications\UserNotification;

class RequestScheduleController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $data = AssingnMember::with('schedule')
            ->where('user_id', $user->id)
            ->whereHas('schedule', function ($query) {
                $query->where('status', 'pending');
            })
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Schedule not found', 404);
        }

        return $this->success($data, 'Schedule found successfully', 200);
    }

    public function singleSchedule($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $data = Schedule::with('user:id,name,avatar')->find($id);

        if (!$data) {
            return $this->error([], 'Schedule not found', 404);
        }

        return $this->success($data, 'Schedule found successfully', 200);
    }

    public function acceptSchedule($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return $this->error([], 'Schedule not found', 404);
        }

        $schedule->status = 'active';
        $schedule->save();

        $leaders = $schedule->churchProfile
            ->teamMembers()
            ->where('role', 'admin')
            ->get();

        foreach ($leaders as $leader) {
            if ($leader->user) {
                $leader->user->notify(new UserNotification(
                    subject: 'Schedule accepted',
                    message: 'Your schedule has been accepted',
                    channels: ['database'],
                    type: NotificationType::SUCCESS,
                ));
            }
        }

        return $this->success($schedule, 'Schedule accepted successfully', 200);
    }

    public function declineSchedule($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $schedule = Schedule::find($id);

        if (!$schedule) {
            return $this->error([], 'Schedule not found', 404);
        }

        $schedule->status = 'rejected';
        $schedule->save();

        $leaders = $schedule->churchProfile
            ->teamMembers()
            ->where('role', 'admin')
            ->get();
            
        foreach ($leaders as $leader) {
            if ($leader->user) {
                $leader->user->notify(new UserNotification(
                    subject: 'Schedule declined',
                    message: 'Your schedule has been declined',
                    channels: ['database'],
                    type: NotificationType::ERROR,
                ));
            }
        }

        return $this->success($schedule, 'Schedule declined successfully', 200);
    }
}
