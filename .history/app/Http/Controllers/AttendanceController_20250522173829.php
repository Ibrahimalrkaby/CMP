<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    // Add attendance for a student
    public function store(Request $request, $courseId)
    {
        $table = 'lecture_attendance_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course does not exist.'], 404);
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

    public function createAttendance(Lecture $lecture)
    {
        // Get students from the course
        $students = Course::where('id', $lecture->course_id)
            ->with('students')
            ->first()
            ->students;

        // Create attendance records
        foreach ($students as $student) {
            Attendance::firstOrCreate([
                'course_id' => $lecture->course_id,
                'lecture_id' => $lecture->id,
                'student_id' => $student->id,
                'present' => false
            ]);
        }

        return response()->json(['message' => 'Attendance records initialized']);
    }

    public function updateAttendance(Request $request, Lecture $lecture)
    {
        $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'present' => 'required|boolean'
        ]);

        $attendance = Attendance::where('lecture_id', $lecture->id)
            ->where('student_id', $request->student_id)
            ->firstOrFail();

        $attendance->update(['present' => $request->present]);

        return response()->json(['message' => 'Attendance updated successfully']);
    }

    public function getAttendance(Lecture $lecture)
    {
        $attendance = Attendance::where('lecture_id', $lecture->id)
            ->with('student')
            ->get();

        return response()->json([
            'lecture' => $lecture,
            'attendance' => $attendance
        ]);
    }

    public function getStudentAttendance($studentId)
    {
        $attendance = Attendance::where('student_id', $studentId)
            ->with(['lecture', 'course'])
            ->get();

        return response()->json([
            'student_id' => $studentId,
            'attendance_records' => $attendance
        ]);
    }

    public function getCourseAttendance($courseId)
    {
        $attendance = Attendance::where('course_id', $courseId)
            ->with(['student', 'lecture'])
            ->get();

        return response()->json([
            'course_id' => $courseId,
            'attendance_records' => $attendance
        ]);
    }
}
