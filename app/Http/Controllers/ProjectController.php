<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query();

        if ($request->has('filters')) {
            $filters = $request->input('filters');
            foreach ($filters as $field => $value) {
                $query->where($field, $value);
            }
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,on_hold',
        ]);

        $project = Project::create($validatedData);
        return response()->json($project, 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:projects,id',
            'name' => 'string|max:255',
            'department' => 'string|max:255',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'in:active,completed,on_hold',
        ]);

        $project = Project::findOrFail($validatedData['id']);
        $project->update($validatedData);
        return response()->json($project);
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:projects,id',
        ]);

        $project = Project::findOrFail($validatedData['id']);
        $project->timesheets()->delete();
        $project->delete();
        return response()->json(null, 204);
    }
}