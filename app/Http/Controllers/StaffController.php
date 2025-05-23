<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;

class StaffController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $staff = Staff::where('username', $validated['username'])
            ->where('password', $validated['password'])
            ->first();

        if (!$staff) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $request->session()->put('staff_id', $staff->id);
        $request->session()->put('staff_role', $staff->role);

        return response()->json([
            'message' => 'Login successful',
            'staff' => [
                'id' => $staff->id,
                'username' => $staff->username,
                'role' => $staff->role
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['staff_id', 'staff_role']);
        return response()->json(['message' => 'Logged out']);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:staff',
            'password' => 'required',
            'role' => 'in:staff,admin'
        ]);

        $staff = Staff::create([
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => $validated['role'] ?? 'staff'
        ]);

        return response()->json([
            'staff' => [
                'id' => $staff->id,
                'username' => $staff->username,
                'role' => $staff->role
            ]
        ], 201);
    }
}
