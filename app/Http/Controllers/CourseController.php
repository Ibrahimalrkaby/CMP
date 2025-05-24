<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{


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
        ]);
        // Create the course
        $course = Course::create($validated);

        if (!$course) {
            return response()->json(['error' => 'Failed to create course'], 500);
        }

        // Dynamic table name
        $gradesTable = 'grades_course_' . $course->id;

        // Log the action
        Log::info("Attempting to create table: $gradesTable");

        // Create grade table if it doesn't exist
        if (!Schema::hasTable($gradesTable)) {
            try {
                Schema::create($gradesTable, function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    // You can temporarily comment this line if students_data table doesn't exist:
                    // $table->foreign('student_id')->references('id')->on('students_data')->onDelete('cascade');
                    $table->decimal('midterm_exam', 5, 2)->nullable();
                    $table->decimal('practical_exam', 5, 2)->nullable();
                    $table->decimal('oral_exam', 5, 2)->nullable();
                    $table->decimal('year_work', 5, 2)->nullable();
                    $table->decimal('final_grade', 5, 2)->nullable();
                    $table->decimal('total', 5, 2)->nullable();
                    $table->decimal('course_grade', 5, 2)->nullable();
                    $table->timestamps();
                    $table->unique(['student_id']);
                });
                Log::info("Table $gradesTable created successfully.");
            } catch (\Exception $e) {
                Log::error("Failed to create table $gradesTable: " . $e->getMessage());
                return response()->json(['error' => 'Course created but table creation failed.'], 500);
            }
        } else {
            Log::info("Table $gradesTable already exists.");
        }

        return response()->json([
            'message' => 'Course created and grade table generated successfully.',
            'course' => $course

        ], 201);
    }



    // get all courses

    public function index()
    {
        $courses = Course::with('schedules')->get();
        return response()->json($courses);
    }

    // Get course by ID
    public function show($id)
    {
        $course = Course::with('schedules')->findOrFail($id);
        return response()->json($course);
    }


    // Update course

    public function update(Request $request, $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json([
                'message' => 'Course not found.'
            ], 404);
        }

        // Log the incoming request data
        Log::info('Incoming course update request:', $request->all());

        // Validate request
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'department' => 'sometimes|required|string',
            'level' => 'sometimes|required|string',
            'credit_hours' => 'sometimes|required|integer',
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

        // Log validated data
        Log::info('Validated update data:', $validated);

        // Update course
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


    // Delete course
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $gradesTable = 'grades_course_' . $course->id;

        // delete related schedules 
        $course->schedules()->delete();

        // Drop the dynamic grades table if it exists
        if (Schema::hasTable($gradesTable)) {
            Schema::drop($gradesTable);
        }

        // Delete the course
        $course->delete();

        return response()->json([
            'message' => 'Course and its schedules its grade table deleted successfully.'

        ]);
    }

}
