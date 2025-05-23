<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class LectureController extends Controller
{


    public function store(Request $request)
    {
        $validated = $request->validate([
            'teacher_id' => 'required|exists:teacher_data,id',
            'course_id' => 'required|exists:courses,id',
            'table_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
            'students' => 'required|array',
            'students.*' => 'required|exists:students_data,id',
        ]);

        // Create the Lecture
        $lecture = Lecture::create([
            'teacher_id' => $validated['teacher_id'],
            'course_id' => $validated['course_id'],
            'table_name' => $validated['table_name'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        if (!$lecture) {
            return response()->json(['error' => 'Failed to create Lecture'], 500);
        }

        // Create attendance table dynamically
        $attendanceTable = 'attendance_Lecture_' . $lecture->id;

        if (!Schema::hasTable($attendanceTable)) {
            try {
                Schema::create($attendanceTable, function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('course_id');
                    $table->unsignedBigInteger('lecture_id');
                    $table->unsignedBigInteger('student_id');
                    $table->boolean('present')->default(true);
                    $table->timestamps();

                    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                    $table->foreign('lecture_id')->references('id')->on('lectures')->onDelete('cascade');
                    $table->foreign('student_id')->references('id')->on('students_data')->onDelete('cascade');

                    $table->unique(['lecture_id', 'student_id']);
                });
            } catch (\Exception $e) {
                return response()->json(['error' => 'Lecture created but attendance table creation failed.'], 500);
            }
        }

        // Insert students into dynamic attendance table
        $records = [];
        foreach ($validated['students'] as $studentId) {
            $records[] = [
                'course_id' => $validated['course_id'],
                'lecture_id' => $lecture->id,
                'student_id' => $studentId,
                'present' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        try {
            DB::table($attendanceTable)->insert($records);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Lecture created but student insertion failed.',
                'details' => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'message' => 'Lecture and attendance table created successfully with students.',
            'Lecture' => $lecture
        ], 201);
    }



    // Get all Lectures
    public function index()
    {
        $lectures = Lecture::all();
        return response()->json($lectures);
    }

    // Get Lecture by ID
    public function show($id)
    {
        $lecture = Lecture::findOrFail($id);
        return response()->json($lecture);
    }

    // Update Lecture
    public function update(Request $request, $id)
    {
        $lecture = Lecture::find($id);

        if (!$lecture) {
            return response()->json([
                'message' => 'Lecture not found.'
            ], 404);
        }

        // Log the incoming request data
        Log::info('Incoming Lecture update request:', $request->all());

        // Validate request
        $validated = $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'teacher_id' => 'required|exists:teacher_data,id',
            'course_id' => 'required|exists:courses,id',
            'table_name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date',
        ]);

        // Log validated data
        Log::info('Validated update data:', $validated);

        // Update Lecture
        $lecture->update($validated);

        return response()->json([
            'message' => 'Lecture updated successfully',
            'Lecture' => $lecture
        ]);
    }


    // Delete Lecture
    public function destroy($id)
    {
        $lecture = Lecture::findOrFail($id);
        $attendanceTable = 'grades_Lecture_' . $lecture->id;

        // Drop the dynamic grades table if it exists
        if (Schema::hasTable($attendanceTable)) {
            Schema::drop($attendanceTable);
        }

        // Delete the Lecture
        $lecture->delete();

        return response()->json([
            'message' => 'Lecture and its grade table deleted successfully.'
        ]);
    }
}
