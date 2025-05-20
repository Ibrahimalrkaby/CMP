<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    //create course
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:courses,name',
            'description' => 'required|string',
            'department' => 'required|string',
            'level' => 'required|string',
            'credit_hours' => 'required|integer',
            'teacher_id' => 'nullable|exists:teacher_data,id',

            // Validate schedule array
            'schedules' => 'nullable|array',
            'schedules.*.day' => 'required_with:schedules|string',
            'schedules.*.start_time' => 'required_with:schedules',
            'schedules.*.end_time' => 'required_with:schedules',
            'schedules.*.location' => 'required_with:schedules|string',
            'schedules.*.type' => 'required_with:schedules|string',
        ]);

        // Create course
        $course = Course::create($validated);

        // Create course schedule(s) if provided
        if ($request->has('schedules')) {
            foreach ($request->schedules as $schedule) {
                $course->schedules()->create([
                    'teacher_id' => $request->teacher_id,
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'location' => $schedule['location'],
                    'type' => $schedule['type'],
                ]);
            }
        }

        return response()->json([
            'message' => 'Course and its schedule created successfully',
            'course' => $course->load('schedules')
        ], 201);
    }


    // get all courses
    public function index()
    {
        $courses = Course::with('schedules')->get();
        return response()->json($courses);
    }

    // get course by id
    public function show($id)
    {
        $course = Course::with('schedules')->findOrFail($id);
        return response()->json($course);
    }


    // update course
    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'department' => 'sometimes|string',
            'level' => 'sometimes|string',
            'credit_hours' => 'sometimes|integer',
            'teacher_id' => 'nullable|exists:teacher_data,id',

            // validation for optional schedule update
            'schedules' => 'nullable|array',
            'schedules.*.id' => 'nullable|exists:course_schedules,id',
            'schedules.*.day' => 'required_with:schedules|string',
            'schedules.*.start_time' => 'required_with:schedules',
            'schedules.*.end_time' => 'required_with:schedules',
            'schedules.*.location' => 'required_with:schedules|string',
            'schedules.*.type' => 'required_with:schedules|string',
        ]);

        $course->update($validated);

        // Update schedules if provided
        if ($request->has('schedules')) {
            foreach ($request->schedules as $schedule) {
                if (isset($schedule['id'])) {
                    // update existing schedule
                    $existing = CourseSchedule::find($schedule['id']);
                    if ($existing && $existing->course_id == $course->id) {
                        $existing->update([
                            'day' => $schedule['day'],
                            'start_time' => $schedule['start_time'],
                            'end_time' => $schedule['end_time'],
                            'location' => $schedule['location'],
                            'type' => $schedule['type'],
                            'teacher_id' => $request->teacher_id ?? $existing->teacher_id,
                        ]);
                    }
                } else {
                    // create new schedule
                    $course->schedules()->create([
                        'teacher_id' => $request->teacher_id,
                        'day' => $schedule['day'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'location' => $schedule['location'],
                        'type' => $schedule['type'],
                    ]);
                }
            }
        }

        return response()->json([
            'message' => 'Course updated successfully',
            'course' => $course->load('schedules')
        ]);
    }


    // delete course
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        // delete related schedules first
        $course->schedules()->delete();

        // delete the course
        $course->delete();

        return response()->json([
            'message' => 'Course and its schedules deleted successfully'
        ]);
    }

}
