<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GradeController extends Controller
{
    // Add grade for a student
    public function store(Request $request, $courseId)
    {
        $table = 'grades_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Grade table for this course does not exist.'], 404);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'midterm_exam' => 'nullable|numeric',
            'practical_exam' => 'nullable|numeric',
            'oral_exam' => 'nullable|numeric',
            'year_work' => 'nullable|numeric',
            'final_grade' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'course_grade' => 'nullable|numeric',
        ]);

        try {
            DB::table($table)->insert($validated);
            return response()->json(['message' => 'Grade added successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Student already has a grade or error occurred.', 'details' => $e->getMessage()], 400);
        }
    }

    // Get all grades for a course
    public function index($courseId)
    {
        $table = 'grades_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Grade table for this course does not exist.'], 404);
        }

        $grades = DB::table($table)->get();
        return response()->json($grades);
    }

    // Get grade of a student in a course
    public function show($courseId, $studentId)
    {
        $table = 'grades_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Grade table for this course does not exist.'], 404);
        }

        $grade = DB::table($table)->where('student_id', $studentId)->first();

        if (!$grade) {
            return response()->json(['error' => 'Grade not found.'], 404);
        }

        return response()->json($grade);
    }

    // Update grade of a student in a course
    public function update(Request $request, $courseId, $studentId)
    {
        $table = 'grades_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Grade table for this course does not exist.'], 404);
        }

        $validated = $request->validate([
            'midterm_exam' => 'nullable|numeric',
            'practical_exam' => 'nullable|numeric',
            'oral_exam' => 'nullable|numeric',
            'year_work' => 'nullable|numeric',
            'final_grade' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'course_grade' => 'nullable|numeric',
        ]);

        $updated = DB::table($table)
            ->where('student_id', $studentId)
            ->update($validated);

        if ($updated) {
            return response()->json(['message' => 'Grade updated successfully.']);
        } else {
            return response()->json(['error' => 'Grade update failed or student not found.'], 404);
        }
    }

    // Delete a student's grade in a course
    public function destroy($courseId, $studentId)
    {
        $table = 'grades_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Grade table for this course does not exist.'], 404);
        }

        $deleted = DB::table($table)->where('student_id', $studentId)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Grade deleted successfully.']);
        } else {
            return response()->json(['error' => 'Grade not found.'], 404);
        }
    }
}
