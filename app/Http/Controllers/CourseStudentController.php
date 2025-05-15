<?php
namespace App\Http\Controllers;

use App\Models\CourseStudent;
use Illuminate\Http\Request;

class CourseStudentController extends Controller
{
    // Store or update the grade for a student in a course.
    
    public function storeOrUpdateGrade(Request $request, $courseId, $studentId)
    {
        $request->validate([
            'grade' => 'required|string',
        ]);

        $courseStudent = CourseStudent::updateOrCreate(
            ['course_id' => $courseId, 'student_id' => $studentId],
            ['grade' => $request->grade]
        );

        return response()->json($courseStudent, 200);
    }
}
