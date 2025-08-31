<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;
    
    public function categories()
    {
        $data = Category::with('IncidentTypes')->where('status', 'active')->latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Category not found', 200);
        }

        return $this->success($data, 'Category fetch Successful!', 200);
    }

    public function location()
    {
        $data = Location::latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Location not found', 200);
        }
        
        return $this->success($data, 'Location fetch Successful!', 200);
    }
}
