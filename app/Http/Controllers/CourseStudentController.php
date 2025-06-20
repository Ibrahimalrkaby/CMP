<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseStudent;
use App\Models\StudentData;
use Illuminate\Http\Request;

class CourseStudentController extends Controller
{
    // Store or update the grade for a student in a course.
    
    public function storeOrUpdateGrade(Request $request, $courseId, $studentId)
    {
        $request->validate([
            'grade' => 'required|string|in:A,B,C,D,F,Pass,Fail',
        ]);

        // Check existence of course and student
        if (!Course::find($courseId)) {
            return response()->json(['message' => 'Course not found'], 404);
        }

        if (!StudentData::find($studentId)) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        $courseStudent = CourseStudent::updateOrCreate(
            ['course_id' => $courseId, 'student_id' => $studentId],
            ['grade' => $request->grade]
        );

        return response()->json([
            'message' => 'Grade stored or updated successfully',
            'data' => $courseStudent
        ], 200);
    }

}
