<?php

namespace App\Http\Controllers;

use App\Models\Semester;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    // show all semesters
    public function index() {
        return Semester::all();
    }

    // show specific semester
    public function show($id) {
        $semester = Semester::findOrFail($id);
        return response()->json($semester);
    }

    // add new semester
    public function store(Request $request) {
        $validated = $request->validate([
            'start_date' => 'required|date|unique:semesters,start_date',
            'end_date' => 'required|date|after:start_date|unique:semesters,end_date',
            'description' => 'nullable|string|max:255',
        ]);

        $semester = Semester::create($validated);
        return response()->json($semester, 201); 
    }

    // update semester
    public function update(Request $request, $id) {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'description' => 'nullable|string|max:255',
        ]);

        $semester->update($validated);
        return response()->json($semester);
    }

    // delete semester
    public function destroy($id) {
        $semester = Semester::findOrFail($id);
        $semester->delete();
        return response()->json(['message' => 'Semester deleted successfully']);
    }

    // add courses to a semester
    public function addCourses(Request $request, $id) {
        $semester = Semester::findOrFail($id);

        $validated = $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $semester->courses()->sync($validated['course_ids']);  
        return response()->json(['message' => 'Courses added to semester successfully']);
    }

    // show courses in a semester
    public function getCourses($id) {
        $semester = Semester::findOrFail($id);
        return response()->json($semester->courses);
    }
}
