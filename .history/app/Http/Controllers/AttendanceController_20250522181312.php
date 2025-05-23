<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttendanceController extends Controller
{
    // Add attendance for a student
    public function store(Request $request, $courseId)
    {
        $table = 'attendance_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course does not exist.'], 404);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'lecture_id' => 'required|exists:lectures,id',
            'present' => 'required|boolean'
        ]);

        try {
            DB::table($table)->insert($validated);
            return response()->json(['message' => 'Attendance added successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Student already has attendance for this lecture or error occurred.', 'details' => $e->getMessage()], 400);
        }
    }

    // Get all attendance for a course
    public function index($courseId)
    {
        $table = 'attendance_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course does not exist.'], 404);
        }

        $attendance = DB::table($table)->get();
        return response()->json($attendance);
    }

    // Get attendance of a student in a course
    public function show($courseId, $studentId)
    {
        $table = 'attendance_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course does not exist.'], 404);
        }

        $attendance = DB::table($table)->where('student_id', $studentId)->first();

        if (!$attendance) {
            return response()->json(['error' => 'Attendance not found.'], 404);
        }

        return response()->json($attendance);
    }

    // Update attendance of a student in a course
    public function update(Request $request, $courseId, $studentId)
    {
        $table = 'attendance_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course does not exist.'], 404);
        }

        $validated = $request->validate([
            'lecture_id' => 'required|exists:lectures,id',
            'present' => 'required|boolean'
        ]);

        $updated = DB::table($table)
            ->where('student_id', $studentId)
            ->where('lecture_id', $validated['lecture_id'])
            ->update($validated);

        if ($updated) {
            return response()->json(['message' => 'Attendance updated successfully.']);
        } else {
            return response()->json(['error' => 'Attendance update failed or student not found.'], 404);
        }
    }

    // Delete a student's attendance in a course
    public function destroy($courseId, $studentId)
    {
        $table = 'attendance_course_' . $courseId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course does not exist.'], 404);
        }

        $deleted = DB::table($table)->where('student_id', $studentId)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Attendance deleted successfully.']);
        } else {
            return response()->json(['error' => 'Attendance not found.'], 404);
        }
    }

    // Create attendance table for a course
    public function createAttendanceTable($courseId)
    {
        $table = 'attendance_course_' . $courseId;

        if (Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this course already exists.'], 400);
        }

        try {
            Schema::create($table, function ($table) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('lecture_id');
                $table->boolean('present')->default(false);
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on('students_data')->onDelete('cascade');
                $table->foreign('lecture_id')->references('id')->on('lectures')->onDelete('cascade');
                $table->unique(['student_id', 'lecture_id']);
            });

            return response()->json(['message' => 'Attendance table created successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create attendance table.', 'details' => $e->getMessage()], 500);
        }
    }
}
