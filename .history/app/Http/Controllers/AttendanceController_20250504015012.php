<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Http\Request;

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
}
