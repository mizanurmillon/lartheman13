<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Denomination;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DenominationController extends Controller
{
    use ApiResponse;
    
    public function denominations()
    {
        $data = Denomination::all();

        if ($data->isEmpty()) {
            return $this->success([], 'Denomination not found', 200);
        }

        return $this->success($data, 'Denomination fetched successfully', 200);
    }
}
