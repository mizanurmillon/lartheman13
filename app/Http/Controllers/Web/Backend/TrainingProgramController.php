<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Models\TrainingProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TrainingProgramController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TrainingProgram::latest()->get();
            if (!empty($request->input('search.value'))) {
                $searchTerm = $request->input('search.value');
                $data->where('title', 'LIKE', "%$searchTerm%");
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('description', function ($data) {
                    $description       = $data->description;
                    $short_description = strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description;
                    return '<p>' . $short_description . '</p>';
                })
                ->addColumn('video', function ($data) {
                    $url = asset($data->video);

                    if (!$data->video) {
                        return 'No Video';
                    }

                    return '<video width="220" height="80" controls>
                            <source src="' . $url . '" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>';
                })
                ->addColumn('file_url', function ($data) {
                    $file = $data->file_url;
                    if (!$file) {
                        return 'No File';
                    }
                    return '<a href="' . asset($file) . '" target="_blank">' . $file . '</a>';
                })
                ->addColumn('thumbnail', function ($data) {
                    $thumbnail = $data->thumbnail;
                    if (!$thumbnail) {
                        return 'No Thumbnail';
                    }
                    return '<img src="' . asset($thumbnail) . '" alt="Thumbnail" width="100" height="50">';
                })
                ->addColumn('status', function ($data) {
                    $status = ' <div class="form-check form-switch">';
                    $status .= ' <input onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                    if ($data->status == "active") {
                        $status .= "checked";
                    }
                    $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                    return $status;
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                              <a href="' . route('admin.training_programs.edit', ['id' => $data->id]) . '" type="button" class="text-white btn btn-primary" title="Edit">
                              <i class="bi bi-pencil"></i>
                              </a>
                              <a href="#" onclick="showDeleteConfirm(' . $data->id . ')" type="button" class="text-white btn btn-danger" title="Delete">
                              <i class="bi bi-trash"></i>
                                </a>
                            </div>';
                })
                ->rawColumns(['description', 'status', 'action', 'video', 'file_url', 'thumbnail'])
                ->make();
        }
        return view('backend.layouts.training_programs.index');
    }

    public function create()
    {
        return view('backend.layouts.training_programs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2550',
            'thumbnail' => 'required|mimes:jpg,jpeg,png|max:2048',
            'video' => 'nullable|mimetypes:video/mp4,video/ogg,video/webm|max:512000',
            'file_url' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        if ($request->hasFile('thumbnail')) {
            $thumbnail                    = $request->file('thumbnail');
            $thumbnailName                = uploadImage($thumbnail, 'training_programs');
        }

        if ($request->hasFile('video')) {
            $video                        = $request->file('video');
            $videoName                    = uploadImage($video, 'training_programs');
        }else{
            $videoName = null;
        }

        if ($request->hasFile('file_url')) {
            $file_url                    = $request->file('file_url');
            $fileName                    = uploadImage($file_url, 'training_programs');
        }else{
            $fileName = null;
        }

        $training_program = TrainingProgram::create([
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $thumbnailName,
            'video' => $videoName,
            'file_url' => $fileName ?? null,
        ]);

        if (!$training_program) {
            return redirect()->route('admin.training_programs.index')->with('t-error', 'Training Program not created successfully');
        }

        return redirect()->route('admin.training_programs.index')->with('t-success', 'Training Program created successfully');
    }

    public function edit(int $id)
    {
        $data = TrainingProgram::findOrFail($id);
        return view('backend.layouts.training_programs.edit', compact('data'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2550',
            'thumbnail' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'video' => 'mimetypes:video/mp4,video/ogg,video/webm|max:512000',
            'file_url' => 'nullable|mimes:pdf,doc,docx|max:5120',
        ]);

        $training_program = TrainingProgram::findOrFail($id);

        if ($request->hasFile('thumbnail')) {
            if ($training_program->thumbnail) {
                $previousImagePath = public_path($training_program->thumbnail);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }
            $thumbnail                        = $request->file('thumbnail');
            $thumbnailName                    = uploadImage($thumbnail, 'training_programs');
            $training_program->thumbnail = $thumbnailName;
        } else {
            $thumbnailName = $training_program->thumbnail;
        }

        if ($request->hasFile('video')) {
            if ($training_program->video) {
                $previousImagePath = public_path($training_program->video);
                if (file_exists($previousImagePath)) {
                    unlink($previousImagePath);
                }
            }
            $video                        = $request->file('video');
            $videoName                    = uploadImage($video, 'training_programs');
        } else {
            $videoName = $training_program->video;
        }

        if ($request->hasFile('file_url')) {
            if ($training_program->file_url) {
                $previousFilePath = public_path($training_program->file_url);
                if (file_exists($previousFilePath)) {
                    unlink($previousFilePath);
                }
            }
            $file_url                        = $request->file('file_url');
            $fileName                    = uploadImage($file_url, 'training_programs');
            $training_program->file_url = $fileName;
        }else{
            $fileName = $training_program->file_url;
        }

        $training_program->update([
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail' => $thumbnailName,
            'video' => $videoName,
            'file_url' => $fileName ?? null,
        ]);

        return redirect()->route('admin.training_programs.index')->with('t-success', 'Training Program updated successfully');
    }

    public function status(int $id): JsonResponse
    {
        $data = TrainingProgram::findOrFail($id);
        if ($data->status == 'inactive') {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data'    => $data,
            ]);
        } else {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data'    => $data,
            ]);
        }
    }

    public function destroy(int $id): JsonResponse
    {

        $data = TrainingProgram::findOrFail($id);

        if ($data->thumbnail) {
            $previousImagePath = public_path($data->thumbnail);
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }

        if ($data->video) {
            $previousImagePath = public_path($data->video);
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }

        if ($data->file_url) {
            $previousFilePath = public_path($data->file_url);
            if (file_exists($previousFilePath)) {
                unlink($previousFilePath);
            }
        }

        $data->delete();

        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }
}
