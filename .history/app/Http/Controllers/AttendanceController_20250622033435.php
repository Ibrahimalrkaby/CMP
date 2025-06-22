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
    // Add attendance for a student
    public function store(Request $request, $lectureId)
    {
        $tableName = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($tableName)) {
            return response()->json(['error' => 'Attendance table not found'], 404);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'present' => 'required|boolean'
        ]);

        try {
            DB::table($tableName)->updateOrInsert(
                ['student_id' => $validated['student_id']],
                $validated + ['updated_at' => now()]
            );

            return response()->json(['message' => 'Attendance updated successfully'], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get all attendance for a lecture
    public function index($lectureId)
    {
        $tableName = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($tableName)) {
            return response()->json(['error' => 'Attendance table not found'], 404);
        }

        $attendance = DB::table($tableName)
            ->join('students_data', $tableName . '.student_id', '=', 'students_data.id')
            ->select($tableName . '.*', 'students_data.full_name', 'students_data.student_id as student_code')
            ->get();

        return response()->json($attendance);
    }

    // Update multiple attendance records
    public function bulkUpdate(Request $request, $lectureId)
    {
        $tableName = 'attendance_lecture_' . $lectureId;

        if (!Schema::hasTable($tableName)) {
            return response()->json(['error' => 'Attendance table not found'], 404);
        }

        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students_data,id',
            'attendances.*.present' => 'required|boolean'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->attendances as $attendance) {
                DB::table($tableName)->updateOrInsert(
                    ['student_id' => $attendance['student_id']],
                    [
                        'present' => $attendance['present'],
                        'updated_at' => now()
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Attendance updated successfully',
                'updated_count' => count($request->attendances)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
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
            return response()->json(['error' => 'Attendance table not found'], 404);
        }

        $request->validate([
            'present' => 'required|boolean'
        ]);

        try {
            DB::table($table)->updateOrInsert(
                ['student_id' => $studentId], // Only need student_id
                [
                    'present' => $request->present,
                    'updated_at' => now()
                ]
            );

            return response()->json(['message' => 'Attendance updated successfully']);
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
