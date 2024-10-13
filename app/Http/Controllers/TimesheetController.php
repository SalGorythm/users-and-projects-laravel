<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TimesheetController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Timesheet store method called');
        Log::info('Request data:', $request->all());

        $validatedData = $request->validate([
            'task_name' => 'required|string',
            'date' => 'required|date',
            'hours' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        Log::info('Validated data:', $validatedData);

        try {
            $timesheet = Timesheet::create($validatedData);
            Log::info('Timesheet created:', $timesheet->toArray());
            return response()->json($timesheet, 201);
        } catch (\Exception $e) {
            Log::error('Error creating timesheet: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create timesheet', 'message' => $e->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        Log::info('Timesheet index method called');
        Log::info('Request data:', $request->all());

        $query = Timesheet::query();

        if ($request->has('filters')) {
            $filters = $request->input('filters');
            Log::info('Filters:', $filters);

            foreach ($filters as $field => $value) {
                if ($field === 'date') {
                    $query->whereDate('date', $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        $timesheets = $query->get();
        
        Log::info('Timesheets retrieved:', $timesheets->toArray());

        return response()->json($timesheets);
    }
}