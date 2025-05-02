<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GuardianStudent;
use App\Models\StudentData;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // all users
    public function index()
    {
        return response()->json(StudentData::with('guardian')->get());
    }

    // spicific user
    public function show($id)
    {
        $student = StudentData::with('guardian')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json($student);
    }

    // create user
    public function store(Request $request)
    {
        $request->validate([
            // student data
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:20',
            'department' => 'required|exists:departments,id',
            'personal_id' => 'required|string|unique:students,personal_id',
            'gender' => 'required|string',
            'age' => 'required|integer|min:0',

            // guardian data
            'guardian_national_id' => 'required|string|max:255',
            'guardian_email' => 'required|email|unique:students_guardian,email',
            'guardian_phone' => 'required|string|max:20',
            'guardian_city' => 'required|string|max:255',
        ]);

        // Create Guardian 
        $guardian = GuardianStudent::create([
            'national_id' => $request->guardian_national_id,
            'email' => $request->guardian_email,
            'phone' => $request->guardian_phone,
            'city' => $request->guardian_city,
        ]);

        // Create Student
        $student = StudentData::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => $request->department,
            'personal_id' => $request->personal_id,
            'guardian_id' => $guardian->national_id, 
        ]);

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student,
            'guardian' => $guardian
        ], 201);
    }

    // update user
    public function update(Request $request, $id)
    {
        $student = StudentData::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        // Validation
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students,email,' . $student->id,
            'phone' => 'sometimes|string|max:20',
            'department' => 'sometimes|exists:departments,id',
            'guardian_id' => 'sometimes|exists:students_guardian,national_id',
        ]);

        // Update allowed fields only
        $student->update($request->only([
            'name',
            'email',
            'phone',
            'department',
            'guardian_id',  
        ]));

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student
        ]);
    }

    // delete user
    public function destroy($id)
    {
        $student = StudentData::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Student deleted successfully']);
    }
}
