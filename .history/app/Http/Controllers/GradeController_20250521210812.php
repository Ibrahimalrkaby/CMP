<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    // Store grade
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'midterm_exam' => 'nullable|numeric|between:0,100',
            'practical_exam' => 'nullable|numeric|between:0,100',
            'oral_exam' => 'nullable|numeric|between:0,100',
            'year_work' => 'nullable|numeric|between:0,100',
            'final_grade' => 'nullable|numeric|between:0,100',
            'total'  => 'nullable|numeric|between:0,100',
            'course_grade' => 'nullable|numeric|between:0,100'
        ]);

        $grade = Grade::create($validated);

        return response()->json([
            'message' => 'Grade recorded successfully',
            'grade' => $grade->load(['student', 'course'])
        ], 201);
    }

    // Get all grades
    public function index()
    {
        $grades = Grade::with(['student', 'course'])->get();
        return response()->json($grades);
    }

    // Get grades by student
    public function getByStudent($studentId)
    {
        $grades = Grade::where('student_id', $studentId)
            ->with('course')
            ->get();

        return response()->json($grades);
    }

    // Get grades by course
    public function getByCourse($courseId)
    {
        $grades = Grade::where('course_id', $courseId)
            ->with('student')
            ->get();

        return response()->json($grades);
    }

    // Update grade
    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $validated = $request->validate([
            'midterm_exam' => 'nullable|numeric|between:0,100',
            'practical_exam' => 'nullable|numeric|between:0,100',
            'final_grade' => 'nullable|numeric|between:0,100'
        ]);

        $grade->update($validated);

        return response()->json([
            'message' => 'Grade updated successfully',
            'grade' => $grade->load(['student', 'course'])
        ]);
    }
}
