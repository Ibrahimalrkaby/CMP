<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CoursePrerequisiteController extends Controller
{
    // Add prerequisite to a course
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'prerequisite_course_id' => 'required|exists:courses,id|different:course_id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $course->prerequisites()->syncWithoutDetaching([$request->prerequisite_course_id]);

        return response()->json(['message' => 'Prerequisite added successfully']);
    }

    // List prerequisites for a course
    public function show($course_id)
    {
        $course = Course::with('prerequisites')->findOrFail($course_id);

        return response()->json([
            'id' => $course->id,
            'name' => $course->name,
            'description' => $course->description,
            'department' => $course->department,
            'level' => $course->level,
            'credit_hours' => $course->credit_hours,
            'prerequisites' => $course->prerequisites->map(function ($prereq) {
                return [
                    'id' => $prereq->id,
                    'name' => $prereq->name,
                    'description' => $prereq->description,
                ];
            }),
        ]);
    }


    // Remove a prerequisite
    public function destroy(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'prerequisite_course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);
        $course->prerequisites()->detach($request->prerequisite_course_id);

        return response()->json(['message' => 'Prerequisite removed successfully']);
    }
    
}
