<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

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
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
        return response()->json($user, 201);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:users,id',
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'date_of_birth' => 'date',
            'gender' => 'in:male,female,other',
            'email' => 'email|unique:users,email,' . $request->id,
            'password' => 'min:6',
        ]);

        $user = User::findOrFail($validatedData['id']);
        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        $user->update($validatedData);
        return response()->json($user);
    }

    public function destroy(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($validatedData['id']);
        $user->timesheets()->delete();
        $user->delete();
        return response()->json(null, 204);
    }
}