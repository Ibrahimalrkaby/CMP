<?php

namespace App\Http\Controllers;


use App\Models\Course;
use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class LectureController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:teacher_data,id',
            'course_id' => 'required|exists:courses,id',
            'table_name' => 'required|unique:lectures',
            'start_time' => 'required|date',
            'end_time' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Create lecture
        $lecture = Lecture::create([
            'teacher_id' => $request->teacher_id,
            'course_id' => $request->course_id,
            'table_name' => $request->table_name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time
        ]);

        // Get students enrolled in the course
        $students = Course::where('id', $request->course_id)
            ->pluck('student_id');

        // Create attendance records
        foreach ($students as $studentId) {
            Attendance::create([
                'lecture_id' => $lecture->id,
                'student_id' => $studentId,
                'present' => false
            ]);
        }

        return response()->json([
            'message' => 'Lecture and attendance table created successfully',
            'data' => $lecture
        ], 201);
    }

    public function updateAttendance(Request $request, Lecture $lecture)
    {
        $request->validate([
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:courses,student_id',
            'attendances.*.present' => 'required|boolean'
        ]);

        try {
            foreach ($request->attendances as $attendance) {
                Attendance::updateOrCreate(
                    [
                        'lecture_id' => $lecture->id,
                        'student_id' => $attendance['student_id']
                    ],
                    ['present' => $attendance['present']]
                );
            }

            return response()->json([
                'message' => 'Attendance updated successfully',
                'updated_count' => count($request->attendances)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendance(Lecture $lecture)
    {
        try {
            // 1. Get the course associated with the lecture
            $course = Course::findOrFail($lecture->course_id);

            // 2. Get all students enrolled in this course
            $enrolledStudents = Course::where('name', $course->name)
                ->where('department', $course->department)
                ->where('level', $course->level)
                ->where('semester_id', $course->semester_id)
                ->with(['student' => function ($query) {
                    $query->select('id', 'full_name', 'student_id'); // Changed name to full_name
                }])
                ->get()
                ->pluck('student');

            // 3. Get existing attendance records
            $attendanceRecords = $lecture->attendances()
                ->get()
                ->keyBy('student_id');

            // 4. Combine enrolled students with attendance data
            $attendanceData = $enrolledStudents->map(function ($student) use ($attendanceRecords) {
                return [
                    'student_id' => $student->student_id,
                    'full_name' => $student->full_name, // Changed name to full_name
                    'present' => $attendanceRecords->has($student->student_id)
                        ? $attendanceRecords[$student->student_id]->present
                        : false,
                    'last_updated' => $attendanceRecords->has($student->student_id)
                        ? $attendanceRecords[$student->student_id]->updated_at
                        : null
                ];
            });

            return response()->json([
                'lecture_id' => $lecture->id,
                'course_name' => $course->name,
                'total_students' => $enrolledStudents->count(),
                'attendance' => $attendanceData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'student_id' => 'required|exists:students_data,id',
    //         'teacher_id' => 'required|exists:teacher_data,id',
    //         'course_id' => 'required|exists:courses,id',
    //         'table_name' => 'required|string|max:255',
    //         'start_time' => 'required|date',
    //         'end_time' => 'nullable|date',
    //     ]);

    //     // Create the Lecture
    //     $lecture = Lecture::create($validated);

    //     if (!$lecture) {
    //         return response()->json(['error' => 'Failed to create Lecture'], 500);
    //     }

    //     // Dynamic table name
    //     $attendanceTable = 'attendance_Lecture_' . $lecture->id;

    //     // Log the action
    //     Log::info("Attempting to create table: $attendanceTable");

    //     // Create grade table if it doesn't exist
    //     if (!Schema::hasTable($attendanceTable)) {
    //         try {
    //             Schema::create($attendanceTable, function (Blueprint $table) {
    //                 $table->id();
    //                 $table->unsignedBigInteger('course_id');
    //                 $table->unsignedBigInteger('lecture_id');
    //                 $table->unsignedBigInteger('student_id');
    //                 $table->boolean('present')->default(true);
    //                 $table->timestamps();

    //                 $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
    //                 $table->foreign('lecture_id')->references('id')->on('lectures')->onDelete('cascade');
    //                 $table->foreign('student_id')->references('id')->on('students_data')->onDelete('cascade');

    //                 $table->unique(['lecture_id', 'student_id']);
    //             });
    //             Log::info("Table $attendanceTable created successfully.");
    //         } catch (\Exception $e) {
    //             Log::error("Failed to create table $attendanceTable: " . $e->getMessage());
    //             return response()->json(['error' => 'Lecture created but table creation failed.'], 500);
    //         }
    //     } else {
    //         Log::info("Table $attendanceTable already exists.");
    //     }

    //     return response()->json([
    //         'message' => 'Lecture created and grade table generated successfully.',
    //         'Lecture' => $lecture
    //     ], 201);
    // }


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
