<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GuardianStudent;
use App\Models\PersonalData;
use App\Models\StudentData;
use App\Models\User;
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
            
            'guardian_national_id' => 'required|digits:14',
            'guardian_email' => 'required|email',
            'guardian_phone' => 'required',
            'guardian_city' => 'required',

            'student_phone' => 'required',
            'student_department' => 'required|string|max:255',
            'student_id' => 'required|exists:users,id', 
            'student_personal_id' => 'required|unique:students_personal_date,national_id',
            'student_supervisor_id' => 'nullable|exists:teacher_data,id',
            'student_program_id' => 'nullable|exists:programs,id',
            'age' => 'required|integer',
            'gender' => 'required|string|in:Male,Female',
            'gpa' => 'nullable|numeric|between:0,4',
            'level' => 'nullable|integer|min:1',
            'total_credit_hours' => 'nullable|integer|min:0',
        ]);

        $user = User::find($validated['student_id']);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $guardian = GuardianStudent::firstOrCreate(
            ['national_id' => $validated['guardian_national_id']],
            [
                'email' => $validated['guardian_email'],
                'phone' => $validated['guardian_phone'],
                'city' => $validated['guardian_city'],
            ]
        );

        $personalData = PersonalData::create([
            'national_id' => $validated['student_personal_id'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
        ]);

        $student = StudentData::create([
            'student_id' => $validated['student_id'],
            'full_name' => $user->name,             
            'email' => $user->email,        
            'phone' => $validated['student_phone'],
            'department' => $validated['student_department'],
            'personal_id' => $personalData->id,
            'guardian_id' => $guardian->national_id,
            'supervisor_id' => $validated['student_supervisor_id'] ?? null,
            'program_id' => $validated['student_program_id'] ?? null,
            'gpa' => $validated['gpa'] ?? null,
            'level' => $validated['level'] ?? null,
            'total_credit_hours' => $validated['total_credit_hours'] ?? 0,
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
            'phone' => 'sometimes|string|max:20',
            'department' => 'sometimes|string|max:255',
            'guardian_id' => 'sometimes|exists:students_guardian,national_id',
        ]);

        $student->update($request->only([
            'phone',
            'department',
            'guardian_id',
        ]));

        if ($student->user) {
            $student->full_name = $student->user->name;
            $student->email = $student->user->email;
            $student->save();
        }

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
