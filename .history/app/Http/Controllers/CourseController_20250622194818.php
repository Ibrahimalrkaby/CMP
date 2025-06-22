<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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
            'schedules' => 'nullable|array',
            'schedules.*.day' => 'required_with:schedules|string',
            'schedules.*.start_time' => 'required_with:schedules',
            'schedules.*.end_time' => 'required_with:schedules',
            'schedules.*.location' => 'required_with:schedules|string',
            'schedules.*.type' => 'required_with:schedules|string',
        ]);

        // Start transaction
        DB::beginTransaction();

        try {
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

            // Create grade table
            $gradesTable = 'grades_course_' . $course->id;
            Log::info("Attempting to create table: $gradesTable");

            if (!Schema::hasTable($gradesTable)) {
                Schema::create($gradesTable, function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->decimal('midterm_exam', 5, 2)->nullable();
                    $table->decimal('practical_exam', 5, 2)->nullable();
                    $table->decimal('oral_exam', 5, 2)->nullable();
                    $table->decimal('year_work', 5, 2)->nullable();
                    $table->decimal('final_grade', 5, 2)->nullable();
                    $table->decimal('total', 5, 2)->nullable();
                    $table->decimal('course_grade', 5, 2)->nullable();
                    $table->timestamps();

                    $table->foreign('student_id')
                        ->references('id')
                        ->on('students_data')
                        ->onDelete('cascade');

                    $table->unique(['student_id']);
                });
                Log::info("Table $gradesTable created successfully.");
            } else {
                Log::info("Table $gradesTable already exists.");
            }

            // Commit transaction
            DB::commit();

            return response()->json([
                'message' => 'Course, schedules, and grade table created successfully',
                'course' => $course->load('schedules'),
                'grade_table' => $gradesTable
            ], 201);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            Log::error("Course creation failed: " . $e->getMessage());

            return response()->json([
                'error' => 'Course creation failed: ' . $e->getMessage()
            ], 500);
        }
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
