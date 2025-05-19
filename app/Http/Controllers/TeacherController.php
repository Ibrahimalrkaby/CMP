<?php

namespace App\Http\Controllers;

use App\Models\TeacherData;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // all teachers
    public function index()
    {
        $teachers = TeacherData::with('program')->get();
        return response()->json([
            'data' => $teachers
        ], 200);
    }

    // specific teacher
    public function show($id)
    {
        $teacher = TeacherData::with('program')->find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        return response()->json([
            'data' => $teacher
        ], 200);
    }

    // create teacher
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teacher_data,email',
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:255',
            'personal_id' => 'required|string|unique:teacher_data,personal_id',
            'rank' => 'required|string|max:255',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        $teacher= TeacherData::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'department' => $validatedData['department'],
            'personal_id' => $validatedData['personal_id'],
            'rank' => $validatedData['rank'],
            'program_id' => $validatedData['program_id'],
        ]);

        return response()->json([
            'message' => 'Teacher created successfully',
            'data' => $teacher
        ], 201);
    }

    // update teacher
    public function update(Request $request, $id)
    {
        $teacher = TeacherData::find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:teacher_data,email,' . $teacher->id,
            'phone' => 'sometimes|string|max:20',
            'department' => 'sometimes|string|max:255',
            'personal_id' => 'sometimes|string|unique:teacher_data,personal_id,' . $teacher->id,
            'rank' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|in:teacher,assistant,doctor',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        $teacher->update($validatedData);

        return response()->json([
            'message' => 'Teacher updated successfully',
            'data' => $teacher
        ], 200);
    }

    // delete teacher
    public function destroy($id)
    {
        $teacher = TeacherData::find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher not found'
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'message' => 'Teacher deleted successfully'
        ], 200);
    }
}
