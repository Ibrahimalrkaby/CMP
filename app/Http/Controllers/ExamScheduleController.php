<?php

namespace App\Http\Controllers;

use App\Models\StudentData;
use App\Models\TeacherData;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
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
            'student' => $student->name,
            'exam_schedule' => $exams,
        ]);
    }

    // teacher exam schedule
    public function teacherExamSchedule($teacher_id)
    {
        $teacher = TeacherData::with('courses.examSchedules')->findOrFail($teacher_id);

        $exams = $teacher->courses->map(function ($course) {
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
            'teacher' => $teacher->name,
            'exam_schedule' => $exams,
        ]);
    }
}
