<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TrainingProgramController extends Controller
{
    use ApiResponse;
    
    public function trainingPrograms()
    {
        $data = TrainingProgram::where('status', 'active')->latest()->get();

        if ($data->isEmpty()) {
            return $this->success([], 'Training Program not found', 200);
        }

        return $this->success($data, 'Training Program fetch Successful!', 200);
    }

    public function trainingProgram($id)
    {
        $data = TrainingProgram::where('id', $id)->where('status', 'active')->first();

        if (!$data) {
            return $this->error([], 'Training Program not found', 200);
        }

        return $this->success($data, 'Training Program fetch Successful!', 200);
    }
}
