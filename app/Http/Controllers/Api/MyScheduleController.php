<?php

namespace App\Http\Controllers\Api;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\AssingnMember;
use App\Http\Controllers\Controller;

class MyScheduleController extends Controller
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
                $query->where('status', 'active');
            })
            ->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Schedule not found', 404);
        }

        return $this->success($data, 'Schedule found successfully', 200);
    }
}
