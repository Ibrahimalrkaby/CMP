<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // Get all admins
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'admins' => Admin::all()
        ]);
    }

    // Get single admin by ID
    public function show($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'admin' => $admin
        ]);
    }

    // Create new admin
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin = Admin::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Admin created successfully',
            'admin' => $admin
        ], 201);
    }

    // Update admin
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:admins,email,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $admin->name = $request->name ?? $admin->name;
        $admin->email = $request->email ?? $admin->email;

        if ($request->password) {
            $admin->password = bcrypt($request->password);
        }

        $admin->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Admin updated successfully',
            'admin' => $admin
        ]);
    }

    // Delete admin
    public function destroy($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Admin not found'
            ], 404);
        }

        $admin->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Admin deleted successfully'
        ]);
    }
}
