<?php

namespace App\Http\Controllers;

use App\Models\CourseSemester;
use Illuminate\Http\Request;

class CourseSemesterController extends Controller
{
    
    // Store the relationship between a course and a semester.
     
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $courseSemester = CourseSemester::create([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ]);

        return response()->json($courseSemester, 201);
    }
}
