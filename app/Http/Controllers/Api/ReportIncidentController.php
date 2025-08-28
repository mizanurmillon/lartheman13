<?php

namespace App\Http\Controllers\Api;

use App\Models\Media;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Enum\NotificationType;
use App\Models\ReportIncident;
use App\Http\Controllers\Controller;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class ReportIncidentController extends Controller
{
    use ApiResponse;

    public function allReports(Request $request)
    {
        $query = ReportIncident::with('churchProfile:id,church_name,denomination,address', 'user:id,name', 'category:id,name', 'media')
            ->select('id', 'category_id', 'user_id', 'church_profile_id', 'title', 'description', 'location', 'incident_date', 'incident_time')
            ->where('status', 'approved')->where('alerts_types', 'public');

        $data = $query->latest()->get();

        if (!$data) {
            return $this->error([], 'Report incidents not found', 404);
        }
        return $this->success($data, 'Report incidents fetched successfully', 200);
    }

    public function singleReport($id)
    {
        $data = ReportIncident::with('churchProfile:id,church_name,denomination,address', 'user:id,name,avatar', 'category:id,name', 'media')
            ->select('id', 'category_id', 'user_id', 'church_profile_id', 'title', 'description', 'location', 'incident_date', 'incident_time', 'alerts_types', 'status')
            ->where('id', $id)
            ->first();
        if (!$data) {
            return $this->error([], 'Report incident not found', 404);
        }
        return $this->success($data, 'Report incident fetched successfully', 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'location' => 'required|string|max:255',
            'alerts_types' => 'required|in:public,private',
            'incident_date' => 'required|date',
            'incident_time' => 'required|date_format:H:i',
            'file_url' => 'required|array',
            'file_url.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }


        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }
        // Find the user's team member record
        $teamMember = $user->teamMembers()->first();

        if (!$teamMember) {
            return $this->error([], 'User is not a member of any team', 404);
        }

        $data = [
            'user_id'          => $user->id,
            'church_profile_id' => $teamMember->church_profile_id,
            'category_id'      => $request->category_id,
            'title'            => $request->title,
            'description'      => $request->description,
            'location'         => $request->location,
            'alerts_types'     => $request->alerts_types,
            'incident_date'    => $request->incident_date,
            'incident_time'    => $request->incident_time,
        ];

        if ($user->role === 'leader') {
            $data['status'] = 'approved';
        } else {
            $data['status'] = 'pending';
        }

        $reportIncident = ReportIncident::create($data);

        // Then handle file uploads (if any)
        if ($request->hasFile('file_url')) {
            foreach ($request->file('file_url') as $file) {
                $fileName = uploadImage($file, 'report_incidents');
                Media::create([
                    'report_incident_id' => $reportIncident->id, // Now we have an ID
                    'file_url'           => $fileName,
                ]);
            }
        }

        if (!$reportIncident) {
            return $this->error([], 'Report incident not created', 500);
        }

        $reportIncident->load('media');

        $teamMembers = $reportIncident->churchProfile->teamMembers->map->user;

        // Send notification to all related users
        Notification::send($teamMembers, new UserNotification(
            subject: 'New Report Incident',
            message: 'A new report incident has been created',
            channels: ['database'],
            type: NotificationType::SUCCESS,
        ));

        return $this->success($reportIncident, 'Report incident created successfully', 201);
    }

    public function myChurchReports()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $teamMember = $user->teamMembers()->first();

        if (!$teamMember) {
            return $this->error([], 'User is not a member of any team', 404);
        }

        $data = ReportIncident::with('churchProfile:id,church_name,denomination,address', 'user:id,name', 'category:id,name', 'media')
            ->select('id', 'category_id', 'user_id', 'church_profile_id', 'title', 'description', 'location', 'incident_date', 'incident_time', 'alerts_types', 'status')
            ->where('church_profile_id', $teamMember->church_profile_id);

        $data = $data->latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Report incidents not found', 404);
        }
        return $this->success($data, 'Report incidents fetched successfully', 200);
    }
}
