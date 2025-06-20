<?php

namespace App\Http\Controllers;

use App\Models\Course;
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
            'student' => $student->full_name,
            'courses' => $coursesWithSchedules,
        ]);
    }


    public function teacherSchedule($teacher_id)
    {
        $teacherData = TeacherData::with(['teacher', 'courses.schedules'])->findOrFail($teacher_id);

        $courses = Course::with('schedules')->where('teacher_id', $teacher_id)->get();

        $schedule = $courses->map(function($course) {
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
                }),
            ];
        });

        return response()->json([
            'teacher' => $teacherData->teacher ? $teacherData->teacher->name : null,  
            'schedule' => $schedule,
        ]);
    }

}