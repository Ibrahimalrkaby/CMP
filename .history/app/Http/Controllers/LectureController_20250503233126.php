<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Models\Attendance;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LectureController extends Controller
{
    public function startLecture(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            // Create lecture record
            $lecture = Lecture::create([
                'teacher_id' => $request->teacher_id,
                'start_time' => now(),
                'table_name' => 'attendance_' . time() . '_' . $request->teacher_id
            ]);

            // Create dynamic table
            Schema::create($lecture->table_name, function ($table) {
                $table->id();
                $table->foreignId('student_id')->constrained();
                $table->boolean('present')->default(false);
                $table->timestamp('marked_at')->nullable();
                $table->timestamps();
            });

            DB::commit();
            return response()->json(['message' => 'Lecture started', 'table' => $lecture->table_name], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Table creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function recordAttendance(Request $request, $tableName)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'present' => 'boolean'
        ]);

        try {
            (new Attendance())->setTableName($tableName)
                ->create([
                    'student_id' => $request->student_id,
                    'present' => $request->present,
                    'marked_at' => now()
                ]);

            return response()->json(['message' => 'Attendance recorded']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Attendance recording failed'], 500);
        }
    }
}
