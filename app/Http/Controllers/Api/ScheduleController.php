<?php

namespace App\Http\Controllers\Api;

use App\Models\Schedule;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AssingnMember;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    use ApiResponse;

    public function allSchedule(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $query = Schedule::with('AssingnMember.user:id,name,email,avatar')->where('user_id', $user->id);

        if($request->has('status')) {
            $query->where('status', $request->status);
        }


        $data = $query->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Schedule not found', 404);
        }
        return $this->success($data, 'Schedule found successfully', 200);
    }


    public function singleSchedule($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $data = Schedule::with('user:id,name,avatar', 'AssingnMember.user:id,name,email,avatar')->find($id);

        if (!$data) {
            return $this->error([], 'Schedule not found', 404);
        }

        return $this->success($data, 'Schedule found successfully', 200);
    }

    public function upcomingSchedule()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $data = Schedule::with('AssingnMember.user:id,name,email,avatar')->where('user_id', $user->id)->where('date', '>=', Carbon::now()->format('Y-m-d'))->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Schedule not found', 404);
        }

        return $this->success($data, 'Schedule found successfully', 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'priority_level' => 'required|in:high,medium,low,critical',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'user_id' => 'required|array',
            'user_id.*' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        // Find the user's team member record
        $teamMember = $user->teamMembers()->first();

        if (!$teamMember) {
            return $this->error([], 'User is not a member of any team', 404);
        }

        $data = Schedule::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority_level' => $request->priority_level,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'user_id' => $user->id,
            'church_id' => $teamMember->church_profile_id
        ]);

        if ($request->has('user_id')) {
            foreach ($request->user_id as $user_id) {
                AssingnMember::create([
                    'schedule_id' => $data->id,
                    'user_id' => $user_id
                ]);
            }
        }

        $data->load('AssingnMember');

        if (!$data) {
            return $this->error([], 'Schedule not created', 404);
        }
        return $this->success($data, 'Schedule created successfully', 200);
    }

    public function updateSchedule(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string|max:2000',
            'priority_level' => 'sometimes|required|in:high,medium,low,critical',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required|date_format:H:i',
            'location' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|array',
            'user_id.*' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $data = Schedule::find($id);

        if (!$data) {
            return $this->error([], 'Schedule not found', 404);
        }

        // Update only the fields that are present in the request
        foreach ($request->only(['title', 'description', 'priority_level', 'date', 'time', 'location']) as $key => $value) {
            $data->$key = $value;
        }

        $data->save();

        if ($request->has('user_id')) {
            // Remove existing assignments
            AssingnMember::where('schedule_id', $data->id)->delete();

            // Add new assignments
            foreach ($request->user_id as $user_id) {
                AssingnMember::create([
                    'schedule_id' => $data->id,
                    'user_id' => $user_id
                ]);
            }
        }

        $data->load('AssingnMember');

        return $this->success($data, 'Schedule updated successfully', 200);
    }

    public function deleteSchedule($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $data = Schedule::find($id);

        if (!$data) {
            return $this->error([], 'Schedule not found', 404);
        }

        $data->delete();

        return $this->success([], 'Schedule deleted successfully', 200);
    }

    public function removeMember($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User Not Found', 404);
        }

        $assignment = AssingnMember::find($id);

        if (!$assignment) {
            return $this->error([], 'Assignment not found', 404);
        }

        $assignment->delete();

        return $this->success([], 'Member removed from schedule successfully', 200);
    }
}
