<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GuardianStudent;
use App\Models\PersonalData;
use App\Models\StudentData;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // Get all students
    public function index()
    {
        return response()->json(StudentData::with('guardianData')->get());
    }

    // Get specific student
    public function show($id)
    {
        $student = StudentData::with('guardianData')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }
        return response()->json($student);
    }

    // Create new student
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Guardian fields
            'guardian_national_id' => 'required|digits:14',
            'guardian_email' => 'required|email',
            'guardian_phone' => 'required',
            'guardian_city' => 'required',

            // Student fields
            'student_name' => 'required|string|max:255',
            'student_email' => 'required|email|unique:students_data,email',
            'student_phone' => 'required',
            'student_department' => 'required|string|max:255',
            'student_personal_id' => 'required|unique:students_personal_date,national_id',
            'student_supervisor_id' => 'nullable|exists:teacher_data,id',
            'student_program_id' => 'nullable|exists:programs,id',
            'age' => 'required|integer',
            'gender' => 'required|string|in:Male,Female',
        ]);

        // Create or find the guardian
        $guardian = GuardianStudent::firstOrCreate(
            ['national_id' => $validated['guardian_national_id']],
            [
                'email' => $validated['guardian_email'],
                'phone' => $validated['guardian_phone'],
                'city' => $validated['guardian_city'],
            ]
        );

        // Create personal data
        $personalData = PersonalData::create([
            'national_id' => $validated['student_personal_id'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
        ]);

        // Create student
        $student = StudentData::create([
            'name' => $validated['student_name'],
            'email' => $validated['student_email'],
            'phone' => $validated['student_phone'],
            'department' => $validated['student_department'],
            'personal_id' => $personalData->national_id,
            'guardian_id' => $guardian->national_id,
            'supervisor_id' => $validated['student_supervisor_id'] ?? null,
            'program_id' => $validated['student_program_id'] ?? null,
        ]);

        return response()->json([
            'message' => 'Student created successfully',
            'student' => $student,
            'guardian' => $guardian,
            'personal_data' => $personalData
        ], 201);
    }

    // Update student
    public function update(Request $request, $id)
    {
        $student = StudentData::find($id);

        if (!$student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:students_data,email,' . $student->id,
            'phone' => 'sometimes|string|max:20',
            'department' => 'sometimes|string|max:255',
            'guardian_id' => 'sometimes|exists:students_guardian,national_id',
        ]);

        $updatedData = $request->only([
            'name',
            'email',
            'phone',
            'department',
            'guardian_id',
        ]);

        $student->update($updatedData);

        return response()->json([
            'message' => 'Student updated successfully',
            'student' => $student
        ]);
    }

    // Delete student
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
