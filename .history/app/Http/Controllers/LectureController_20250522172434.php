<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class LectureController extends Controller
{


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'department' => 'required|string',
            'level' => 'required|string',
            'credit_hours' => 'required|integer',
            'teacher_id' => 'nullable|exists:teacher_data,id',
        ]);

        // Create the Lecture
        $lecture = Lecture::create($validated);

        if (!$lecture) {
            return response()->json(['error' => 'Failed to create Lecture'], 500);
        }

        // Dynamic table name
        $attendanceTable = 'grades_Lecture_' . $lecture->id;

        // Log the action
        Log::info("Attempting to create table: $attendanceTable");

        // Create grade table if it doesn't exist
        if (!Schema::hasTable($attendanceTable)) {
            try {
                Schema::create($attendanceTable, function (Blueprint $table) {
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
                    $table->decimal('Lecture_grade', 5, 2)->nullable();
                    $table->timestamps();
                    $table->unique(['student_id']);
                });
                Log::info("Table $attendanceTable created successfully.");
            } catch (\Exception $e) {
                Log::error("Failed to create table $attendanceTable: " . $e->getMessage());
                return response()->json(['error' => 'Lecture created but table creation failed.'], 500);
            }
        } else {
            Log::info("Table $attendanceTable already exists.");
        }

        return response()->json([
            'message' => 'Lecture created and grade table generated successfully.',
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
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'department' => 'sometimes|required|string',
            'level' => 'sometimes|required|string',
            'credit_hours' => 'sometimes|required|integer',
            'teacher_id' => 'nullable|exists:teacher_data,id',
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
