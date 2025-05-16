<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    public function register(Request $request)
    {
        // Validation rules
        $request->validate([
            'student_id' => 'required|exists:students_data,id',
            'course_id' => 'required|exists:courses,id',
            'semester_id' => 'required|exists:semesters,id',
        ]);

        $studentId = $request->student_id;
        $courseId = $request->course_id;
        $semesterId = $request->semester_id;

        // Check if the student has already registered for this course in the current semester
        $existing = CourseRegistration::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('semester_id', $semesterId)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You already registered or passed this course'], 400);
        }

        // Check if the student has passed all prerequist courses
        $course = Course::with('prerequisites')->findOrFail($courseId);
        foreach ($course->prerequisites as $prerequisite) {
            $hasPassed = CourseRegistration::where('student_id', $studentId)
                ->where('course_id', $prerequisite->id)
                ->where('status', 'confirmed')
                ->whereNotNull('grade')
                ->where('grade', '>=', 60) 
                ->exists();

            if (!$hasPassed) {
                return response()->json([
                    'message' => 'Missing prerequisite: ' . $prerequisite->name
                ], 400);
            }
        }

        // Check if the total credit hours exceed the limit (max => 18)
        $totalCredits = CourseRegistration::where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->where('status', '!=', 'rejected')
            ->join('courses', 'course_registrations.course_id', '=', 'courses.id')
            ->sum('courses.credit_hours');

        if (($totalCredits + $course->credit_hours) > 18) {
            return response()->json([
                'message' => 'Credit hour limit exceeded for this semester (max 18 hours)'
            ], 400);
        }

        // Check the student's GPA
        $studentGPA = $this->calculateStudentGPA($studentId);

        // Assuming the student needs a GPA of 2.0 or higher to register
        if ($studentGPA < 2.0) {
            return response()->json([
                'message' => 'Your GPA is below the required for registration.'
            ], 400);
        }

        // Register the student
        $registration = CourseRegistration::create([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'semester_id' => $semesterId,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Registration submitted',
            'data' => $registration
        ]);
    }

    // helper function to convert grades to points
    private function convertGradeToPoints($grade)
    {
        switch ($grade) {
            case 'A': return 4.0;
            case 'B': return 3.0;
            case 'C': return 2.0;
            case 'D': return 1.0;
            case 'F': return 0.0;
            default: return 0.0;
        }
    }
    
    // helper function to calculate GPA
    private function calculateStudentGPA($studentId)
    {
        // calculate GPA based on the grades stored in the course student pivot
        $registrations = CourseRegistration::where('student_id', $studentId)->whereNotNull('grade')->get();
        $totalPoints = 0;
        $totalCourses = 0;

        foreach ($registrations as $registration) {
            $grade = $registration->grade;
            $totalPoints += $grade;
            $totalCourses++;
        }

        if ($totalCourses == 0) {
            return 0;
        }

        return $totalPoints / $totalCourses;
    }



    // Get all course registrations for a student
    public function studentCourses($student_id)
    {
        $registrations = CourseRegistration::with(['course', 'semester'])->where('student_id', $student_id)->get();
        return response()->json($registrations);
    }

    // confirm or reject a course registration
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,rejected',
        ]);

        $registration = CourseRegistration::findOrFail($id);
        $registration->status = $request->status;
        $registration->save();

        return response()->json(['message' => 'Status updated', 'data' => $registration]);
    }

    // get all course registrations for a semester
    public function semesterRegistrations($semester_id)
    {
        $registrations = CourseRegistration::with(['student', 'course'])->where('semester_id', $semester_id)->get();
        return response()->json($registrations);
    }

    // get all course registrations
    public function index()
    {
        $all = CourseRegistration::with(['student', 'course', 'semester'])->get();
        return response()->json($all);
    }

    // delete a course registration
    public function destroy($id)
    {
        $registration = CourseRegistration::findOrFail($id);
        $registration->delete();

        return response()->json(['message' => 'Registration deleted successfully']);
    }

}
