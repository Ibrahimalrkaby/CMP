<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'lecture_id' => 'required|exists:lectures,id',
            'student_id' => 'required|exists:students_data,id',
            'present' => 'required|boolean'
        ]);

        // Create the Attendance
        $attendance = Attendance::create($validated);

        if (!$attendance) {
            return response()->json(['error' => 'Failed to create Attendance record'], 500);
        }

        return response()->json([
            'message' => 'Attendance record created successfully.',
            'attendance' => $attendance
        ], 201);
    }

    // Get all Attendance records
    public function index()
    {
        $attendance = Attendance::with(['course', 'lecture', 'student'])->get();
        return response()->json($attendance);
    }

    // Get Attendance by ID
    public function show($id)
    {
        $attendance = Attendance::with(['course', 'lecture', 'student'])->findOrFail($id);
        return response()->json($attendance);
    }

    // Update Attendance
    public function update(Request $request, $id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'message' => 'Attendance record not found.'
            ], 404);
        }

        // Log the incoming request data
        Log::info('Incoming Attendance update request:', $request->all());

        // Validate request
        $validated = $request->validate([
            'course_id' => 'sometimes|required|exists:courses,id',
            'lecture_id' => 'sometimes|required|exists:lectures,id',
            'student_id' => 'sometimes|required|exists:students_data,id',
            'present' => 'sometimes|required|boolean'
        ]);

        // Log validated data
        Log::info('Validated update data:', $validated);

        // Update Attendance
        $attendance->update($validated);

        return response()->json([
            'message' => 'Attendance record updated successfully',
            'attendance' => $attendance
        ]);
    }

    // Delete Attendance
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'message' => 'Attendance record deleted successfully.'
        ]);
    }

    // Get attendance for a specific lecture
    public function getLectureAttendance($lectureId)
    {
        $attendance = Attendance::where('lecture_id', $lectureId)
            ->with(['student', 'course'])
            ->get();

        return response()->json([
            'lecture_id' => $lectureId,
            'attendance_records' => $attendance
        ]);
    }

    // Get attendance for a specific student
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

    // Get attendance for a specific course
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
