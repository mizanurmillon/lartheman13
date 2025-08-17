<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;
    
    public function categories()
    {
        $data = Category::where('status', 'active')->latest()->get();

        if ($data->isEmpty()) {
            return $this->error([], 'Category not found', 200);
        }

        return $this->success($data, 'Category fetch Successful!', 200);
    }
}
