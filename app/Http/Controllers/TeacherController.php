<?php

namespace App\Http\Controllers;

use App\Models\TeacherData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:teacher_api');
    }

    // all teachers
    public function index()
    {
        $teachers = TeacherData::with(['program', 'teacher'])->get();
        return response()->json([
            'data' => $teachers
        ], 200);
    }

    // specific teacher
    public function show($id)
    {
        $teacher = TeacherData::with(['program', 'teacher'])->find($id);

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
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:255',
            'personal_id' => 'required|string|unique:teacher_data,personal_id',
            'rank' => 'required|string|max:255',
            'role' => 'required|string|in:teacher,assistant,doctor',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        // Get the authenticated teacher from jwt guard
        $teacher = auth('teacher_api')->user();

        if (!$teacher) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $teacherId = $teacher->id;

        $teacherData = TeacherData::create([
            'phone' => $validatedData['phone'],
            'department' => $validatedData['department'],
            'personal_id' => $validatedData['personal_id'],
            'rank' => $validatedData['rank'],
            'role' => $validatedData['role'],
            'program_id' => $validatedData['program_id'],
            'teacher_id' => $teacherId,
        ]);

        return response()->json([
            'message' => 'Teacher data created successfully',
            'data' => $teacherData
        ], 201);
    }

    // update teacher
    public function update(Request $request, $id)
    {
        $authTeacher = auth('teacher_api')->user();
        $teacher = TeacherData::where('teacher_id', $authTeacher->id)->first();

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher data not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'phone' => 'sometimes|string|max:20',
            'department' => 'sometimes|string|max:255',
            'personal_id' => 'sometimes|string|unique:teacher_data,personal_id,' . $teacher->id,
            'rank' => 'sometimes|string|max:255',
            'role' => 'sometimes|string|in:teacher,assistant,doctor',
            'program_id' => 'nullable|exists:programs,id',
        ]);

        $teacher->update($validatedData);

        return response()->json([
            'message' => 'Teacher data updated successfully',
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
