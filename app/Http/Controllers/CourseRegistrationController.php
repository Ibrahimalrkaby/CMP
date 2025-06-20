<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\CourseStudent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\StudentData;

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
            $passed = CourseStudent::where('student_id', $studentId)
                ->where('course_id', $courseId)
                ->whereNotNull('grade')
                ->where('grade', '>=', 60)
                ->exists();

            if (!$passed) {
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

 // ( registered course section ) in (doctor):

    public function getRegisteredCourses(int $studentId, Request $request): JsonResponse
    {
        // Find the student or return 404 if not found
        $student = StudentData::findOrFail($studentId);

        // Fetch the student's course registrations with course and semester relations
        $registrations = CourseRegistration::with(['course', 'semester'])
            ->where('student_id', $studentId)
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->query('search');
                $query->whereHas('course', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->get();

        // Calculate the total credit hours of all registered courses
        $totalRegisteredHours = $registrations->sum(function ($reg) {
            return $reg->course->credit_hours ?? 0;
        });

        // Calculate the total credit hours for confirmed courses only
        $totalNumberOfHoursConfirmed = $registrations
            ->where('status', 'confirmed')
            ->sum(function ($reg) {
                return $reg->course->credit_hours ?? 0;
            });

        // Format the course data
        $formattedCourses = $registrations->map(function ($reg) {
            return [
                'course_code' => $reg->course->code,
                'course_name' => $reg->course->name,
                'credit_hours' => $reg->course->credit_hours,
                'semester' => $reg->semester->description ?? null,
                'status' => $reg->status,
                'grade' => $reg->grade,
            ];
        });

        return response()->json([
            'status' => 'success',
            'student_name' => $student->full_name,
            'student_id' => $student->student_id,
            'gpa' => $student->gpa,
            'level' => $student->level,
            'total_registered_hours' => $totalRegisteredHours,
            'total_confirmed_hours' => $totalNumberOfHoursConfirmed,
            'courses' => $formattedCourses,
        ], 200);
    }

    public function confirmRegistration(int $studentId, Request $request): JsonResponse
    {
        $student = StudentData::findOrFail($studentId);
        $action = $request->input('action'); // Get the action from the request ('confirm' or 'not_confirm')

        if ($action === 'confirm') {
            $registeredCourses = $student->registeredCourses()->get(); // Get the registered courses

            foreach ($registeredCourses as $course) {
                $registration = CourseRegistration::where('student_id', $studentId)
                    ->where('course_id', $course->id)
                    ->where('semester_id', $course->pivot->semester_id)
                    ->first();
                if ($registration) {
                    $registration->status = 'confirmed';
                    $registration->save();
                }
            }
            return response()->json(['success' => true, 'message' => 'Registration confirmed.'], 200);
        } elseif ($action === 'not_confirm') {
            return response()->json(['success' => false, 'message' => 'Registration not confirmed.'], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid action.'], 400);
        }
    }
}

