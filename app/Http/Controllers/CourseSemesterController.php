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

        $exists = CourseSemester::where('course_id', $request->course_id)
                                ->where('semester_id', $request->semester_id)
                                ->exists();

        if ($exists) {
            return response()->json(['message' => 'This course is already assigned to this semester.'], 409);
        }

        $courseSemester = CourseSemester::create([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ]);

        return response()->json($courseSemester, 201);
    }

    // get all courses
    public function index()
    {
        $courseSemesters = CourseSemester::with(['course', 'semester'])->get();
        return response()->json($courseSemesters);
    }

    // get specific 
    public function show($course_id, $semester_id)
    {
        $courseSemester = CourseSemester::with(['course', 'semester'])
            ->where('course_id', $course_id)
            ->where('semester_id', $semester_id)
            ->first();

        if (!$courseSemester) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($courseSemester);
    }


    // update 
    public function update(Request $request, $course_id, $semester_id)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $courseSemester = CourseSemester::where('course_id', $course_id)
                                        ->where('semester_id', $semester_id)
                                        ->first();

        if (!$courseSemester) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // Check if the new combination exists (avoid duplicates)
        $exists = CourseSemester::where('course_id', $request->course_id)
                                ->where('semester_id', $request->semester_id)
                                ->where(function ($query) use ($course_id, $semester_id) {
                                    $query->where('course_id', '!=', $course_id)
                                        ->orWhere('semester_id', '!=', $semester_id);
                                })->exists();

        if ($exists) {
            return response()->json(['message' => 'This course is already assigned to this semester.'], 409);
        }

        $courseSemester->update([
            'course_id' => $request->course_id,
            'semester_id' => $request->semester_id,
        ]);

        return response()->json($courseSemester);
    }

    // destroy
    public function destroy($course_id, $semester_id)
    {
        $courseSemester = CourseSemester::where('course_id', $course_id)
                                        ->where('semester_id', $semester_id)
                                        ->first();

        if (!$courseSemester) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $courseSemester->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }



}
