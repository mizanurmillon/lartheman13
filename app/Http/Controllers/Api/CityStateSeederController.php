<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CityStateSeederController extends Controller
{
    use ApiResponse;
    
    public function cityState()
    {
        $data = City::with('state')->get();

        if ($data->isEmpty()) {
            return $this->error([], 'City not found', 200);
        }
        
        return $this->success($data, 'City fetch Successful!', 200);
    }
}
