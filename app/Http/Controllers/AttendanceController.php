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
    public function createAttendance(Lecture $lecture)
    {
        // Get students from the course table
        $students = Course::where('student_id', '!=', null)
            ->distinct('student_id')
            ->pluck('student_id');

        // Create attendance records
        foreach ($students as $studentId) {
            Attendance::firstOrCreate([
                'lecture_id' => $lecture->id,
                'student_id' => $studentId
            ]);
        }

        return response()->json(['message' => 'Attendance table initialized']);
    }

    public function updateAttendance(Request $request, Lecture $lecture)
    {
        $request->validate([
            'student_id' => 'required|exists:courses,student_id',
            'present' => 'required|boolean'
        ]);

        $attendance = Attendance::where('lecture_id', $lecture->id)
            ->where('student_id', $request->student_id)
            ->firstOrFail();

        $attendance->update(['present' => $request->present]);

        return response()->json(['message' => 'Attendance updated']);
    }

    public function getAttendance(Lecture $lecture)
    {
        $attendance = $lecture->attendances()
            ->with('student')
            ->get();

        return response()->json([
            'lecture' => $lecture,
            'attendance' => $attendance
        ]);
    }

    // Add attendance for a student
    public function store(Request $request, $lectureId)
    {
        $table = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this lecture does not exist.'], 404);
        }

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecture_id' => 'required|exists:lectures,id',
            'student_id' => 'required|exists:students_data,id',
            'present' => 'required|boolean'
        ]);

        try {
            DB::table($table)->insert($validated);
            return response()->json(['message' => 'Attendance added successfully.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Student already has attendance for this lecture or error occurred.', 'details' => $e->getMessage()], 400);
        }
    }

    // Get all attendance for a lecture
    public function index($lectureId)
    {
        $table = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this lecture does not exist.'], 404);
        }

        $attendance = DB::table($table)->get();
        return response()->json($attendance);
    }

    // Get attendance of a student in a lecture
    public function show($lectureId, $studentId)
    {
        $table = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this lecture does not exist.'], 404);
        }

        $attendance = DB::table($table)->where('student_id', $studentId)->first();

        if (!$attendance) {
            return response()->json(['error' => 'Attendance not found.'], 404);
        }

        return response()->json($attendance);
    }

    // Update attendance of a student in a lecture
    public function update(Request $request, $lectureId, $studentId)
    {
        $table = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this lecture does not exist.'], 404);
        }

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.course_id' => 'required|exists:courses,id',
            'attendances.*.lecture_id' => 'required|exists:lectures,id',
            'attendances.*.student_id' => 'required|exists:students_data,id',
            'attendances.*.present' => 'required|boolean'
        ]);

        try {
            foreach ($request->attendances as $attendance) {
                DB::table($table)->updateOrInsert(
                    [
                        'student_id' => $studentId,
                        'lecture_id' => $attendance['lecture_id']
                    ],
                    [
                        'present' => $attendance['present'],
                        'updated_at' => now()
                    ]
                );
            }

            return response()->json([
                'message' => 'Attendance updated successfully',
                'updated_count' => count($request->attendances)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete a student's attendance in a lecture
    public function destroy($lectureId, $studentId)
    {
        $table = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($table)) {
            return response()->json(['error' => 'Attendance table for this lecture does not exist.'], 404);
        }

        $deleted = DB::table($table)->where('student_id', $studentId)->delete();

        if ($deleted) {
            return response()->json(['message' => 'Attendance deleted successfully.']);
        } else {
            return response()->json(['error' => 'Attendance not found.'], 404);
        }

    }
}
    