<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class LectureController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teacher_data,id',
            'course_id' => 'required|exists:courses,id',
            'table_name' => 'required|unique:lectures',
            'start_time' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create lecture
        $lecture = Lecture::create([
            'teacher_id' => $request->teacher_id,
            'course_id' => $request->course_id,
            'table_name' => $request->table_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        // Get students enrolled in the course
        $students = Course::where('id', $request->course_id)
            ->pluck('student_id');

        // Create attendance records
        foreach ($students as $studentId) {
            Attendance::create([
                'lecture_id' => $lecture->id,
                'student_id' => $studentId,
                'present' => false
            ]);
        }

        return response()->json([
            'message' => 'Lecture and attendance table created successfully',
            'data' => $lecture
        ], 201);
    }

    public function updateAttendance(Request $request, Lecture $lecture)
    {
        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:courses,student_id',
            'attendances.*.present' => 'required|boolean'
        ]);

        try {
            foreach ($request->attendances as $attendance) {
                Attendance::updateOrCreate(
                    [
                        'lecture_id' => $lecture->id,
                        'student_id' => $attendance['student_id']
                    ],
                    ['present' => $attendance['present']]
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

    public function getAttendance(Lecture $lecture)
    {
        try {
            $attendance = $lecture->attendances()
                ->with(['student' => function ($query) {
                    $query->select('id', 'name', 'student_id'); // Ensure these columns exist
                }])
                ->get(['lecture_id', 'student_id', 'present', 'created_at']);

            return response()->json([
                'lecture_id' => $lecture->id,
                'course_id' => $lecture->course_id,
                'attendance' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
