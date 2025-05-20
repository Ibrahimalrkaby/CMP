<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ExamSchedule;
use App\Models\StudentData;
use App\Models\TeacherData;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{

    // show all exams
    public function index()
    {
        $exams = ExamSchedule::with('course')->get();
        return response()->json($exams);
    }

    // store exam
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'exam_type' => 'required|string|max:255',
            'exam_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'location' => 'nullable|string|max:255',
        ]);

        $exists = ExamSchedule::where('course_id', $validatedData['course_id'])
                    ->where('exam_type', $validatedData['exam_type'])
                    ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This course already has an exam scheduled for this exam type.'
            ], 422);
        }

        $exam = ExamSchedule::create($validatedData);

        return response()->json(['message' => 'Exam schedule created successfully', 'exam' => $exam], 201);
    }


    // update exam
    public function update(Request $request, $id)
    {
        $exam = ExamSchedule::findOrFail($id);

        $validatedData = $request->validate([
            'course_id' => 'sometimes|required|exists:courses,id',
            'exam_type' => 'sometimes|required|string|max:255',
            'exam_date' => 'sometimes|required|date',
            'start_time' => 'sometimes|required',
            'end_time' => 'sometimes|required|after:start_time',
            'location' => 'nullable|string|max:255',
        ]);

        $exam->update($validatedData);

        return response()->json(['message' => 'Exam schedule updated successfully', 'exam' => $exam]);
    }

    // delete specific exam
    public function destroy($id)
    {
        $exam = ExamSchedule::findOrFail($id);
        $exam->delete();

        return response()->json(['message' => 'Exam schedule deleted successfully']);
    }

    // student exam schedule
    public function studentExamSchedule($student_id)
    {
        $student = StudentData::with('registeredCourses.examSchedules')->findOrFail($student_id);

        $exams = $student->registeredCourses->map(function ($course) {
            return [
                'course_name' => $course->name,
                'exams' => $course->examSchedules->map(function ($exam) {
                    return [
                        'exam_type' => $exam->exam_type,
                        'exam_date' => $exam->exam_date,
                        'start_time' => $exam->start_time,
                        'end_time' => $exam->end_time,
                        'location' => $exam->location,
                    ];
                }),
            ];
        });

        return response()->json([
            'student' => $student->full_name,
            'exam_schedule' => $exams,
        ]);
    }

    // teacher exam schedule
    public function teacherExamSchedule($teacher_id)
    {
        $teacher = TeacherData::with(['teacher' ,'courses.examSchedules'])->findOrFail($teacher_id);
        $courses = Course::with('schedules')->where('teacher_id', $teacher_id)->get();

        $exams = $courses->map(function ($course) {
            return [
                'course_name' => $course->name,
                'exams' => $course->examSchedules->map(function ($exam) {
                    return [
                        'exam_type' => $exam->exam_type,
                        'exam_date' => $exam->exam_date,
                        'start_time' => $exam->start_time,
                        'end_time' => $exam->end_time,
                        'location' => $exam->location,
                    ];
                }),
            ];
        });

        return response()->json([
            'teacher' => $teacher->teacher ? $teacher->teacher->name : null,
            'exam_schedule' => $exams,
        ]);
    }
}
