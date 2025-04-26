<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GuardianStudent;
use App\Models\PersonalData;
use App\Models\StudentData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        // Validation
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
    
        // Create or get the guardian (if exists)
        $guardian = GuardianStudent::firstOrCreate(
            ['national_id' => $validated['guardian_national_id']],
            [
                'email' => $validated['guardian_email'],
                'phone' => $validated['guardian_phone'],
                'city' => $validated['guardian_city'],
            ]
        );
    
        // Create personal data record
        $personalData = PersonalData::create([
            'national_id' => $validated['student_personal_id'],  // نفس الـ national_id
            'age' => $validated['age'],
            'gender' => $validated['gender'],
        ]);
    
        // Create the student and link to the guardian and personal data
        $student = StudentData::create([
            'name' => $validated['student_name'],
            'email' => $validated['student_email'],
            'phone' => $validated['student_phone'],
            'department' => $validated['student_department'],
            'personal_id' => $personalData->national_id,  // ربط بالـ personal_id
            'guardian_id' => $guardian->national_id,  // ربط بالـ guardian
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
