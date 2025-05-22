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
