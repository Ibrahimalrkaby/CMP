<?php

namespace App\Http\Controllers;

use App\Models\StudentData;
use App\Models\TeacherData;
use Illuminate\Http\Request;

class CourseScheduleController extends Controller
{
    public function index($student_id)
    {
        $student = StudentData::with('registeredCourses.schedules')->findOrFail($student_id);

        $coursesWithSchedules = $student->registeredCourses->map(function ($course) {
            return [
                'course_name' => $course->name,
                'schedules' => $course->schedules->map(function ($schedule) {
                    return [
                        'type' => $schedule->type,
                        'day' => $schedule->day,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'location' => $schedule->location,
                    ];
                })
            ];
        });

        return response()->json([
            'student' => $student->name,
            'courses' => $coursesWithSchedules,
        ]);
    }

    public function teacherSchedule($teacher_id)
    {
        $teacher = TeacherData::with(['courses.schedules'])->findOrFail($teacher_id);

        $schedule = $teacher->courses->map(function($course) {
            return [
                'course_name' => $course->name,
                'schedules' => $course->schedules->map(function($schedule) {
                    return [
                        'type' => $schedule->type,
                        'day' => $schedule->day,
                        'start_time' => $schedule->start_time,
                        'end_time' => $schedule->end_time,
                        'location' => $schedule->location,
                    ];
                })
            ];
        });

        return response()->json([
            'teacher' => $teacher->name,
            'schedule' => $schedule,
        ]);
    }
}