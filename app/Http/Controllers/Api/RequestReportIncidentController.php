<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Enum\NotificationType;
use App\Models\ReportIncident;
use App\Http\Controllers\Controller;
use App\Notifications\UserNotification;

class RequestReportIncidentController extends Controller
{
    use ApiResponse;
    
    public function allRequests()
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }

        $teamMember = $user->teamMembers()->first();

        if (!$teamMember) {
            return $this->error([], 'User is not a member of any team', 404);
        }

        $data = ReportIncident::with('churchProfile:id,church_name,denomination_id,address','churchProfile.denomination:id,name','user:id,name','category:id,name','incidentType:id,name','location:id,name', 'media')
                        ->select('id', 'category_id', 'user_id', 'church_profile_id', 'incident_type_id', 'description', 'location_id', 'incident_date', 'incident_time', 'alerts_types', 'status')
                        ->where('status', 'pending')->where('church_profile_id', $teamMember->church_profile_id);

        $data = $data->latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Report incidents not found', 404);
        }
        return $this->success($data, 'Report incidents fetched successfully', 200);
    }

    public function requestApproved($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }
        $data = ReportIncident::find($id);

        if (!$data) {
            return $this->error([], 'Report incident not found', 404);
        }

        if ($data->status == 'approved') {
            return $this->error([], 'Report incident is already approved', 404);
        }

        $data->status = 'approved';
        $data->save();

        $data->user->notify(new UserNotification(
            subject: 'Report incident approved',
            message: 'Your report incident has been approved',
            channels: ['database'],
            type: NotificationType::SUCCESS,
        ));

        return $this->success($data, 'Report incident approved successfully', 200);
    }

    public function requestRejected($id)
    {
        $user = auth()->user();

        if (!$user) {
            return $this->error([], 'User not found', 404);
        }
        $data = ReportIncident::find($id);

        if (!$data) {
            return $this->error([], 'Report incident not found', 404);
        }

        if ($data->status == 'rejected') {
            return $this->error([], 'Report incident is already rejected', 404);
        }

        $data->status = 'rejected';
        $data->save();

        $data->user->notify(new UserNotification(
            subject: 'Report incident rejected',
            message: 'Your report incident has been rejected',
            channels: ['database'],
            type: NotificationType::ERROR,
        ));

        return $this->success($data, 'Report incident rejected successfully', 200);
    }
}
