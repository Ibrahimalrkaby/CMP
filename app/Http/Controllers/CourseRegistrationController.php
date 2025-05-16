<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
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

 // ( registered course section ) in (doctor):
 /**
     * Get registered courses for a student with details, calculations, and student name search.
     *
     * @param int $studentId The ID of the student.
     * @param Request $request The HTTP request, potentially containing a search term.
     * @return JsonResponse Returns a JSON response containing registered courses,
     * total registered hours, and total completed hours.
     */
    public function getRegisteredCourses(int $studentId, Request $request): JsonResponse
    {
        // Find the student or return 404 if not found
        $student = StudentData::findOrFail($studentId);

        // Fetch registered courses using the relationship defined in StudentData model
        $registeredCourses = $student->registeredCourses()
            ->with(['semester:id,description']) // Eager load semester description (optimize query)
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->query('search');
                // Filter the courses based on the student's name
                $query->where('students_data.name', 'like', '%' . $search . '%');
            })
            ->get();

        // Calculate total registered hours by summing credit hours of fetched courses
        $totalRegisteredHours = $registeredCourses->sum('credit_hours');

        // Calculate total numberof hours using the relationship and filtering by status
        $totalnumberofHours = $student->registeredCourses()
            ->where('course_registrations.status', 'confirmed') // Filter for numberof courses
            ->sum('credit_hours');

        // Format the course data for the response
        $formattedCourses = $registeredCourses->map(function ($course) {
            return [
                'course_code' => $course->code,
                'course_name' => $course->name,
                'credit_hours' => $course->credit_hours,
                'semester' => $course->pivot->semester->description ?? null, // Access semester description
            ];
        });

        // Return the formatted response
        return response()->json([
            'status' => 'success',
            'student_name' => $student->name, // Add student name
            'student_id' => $student->student_id, // Assuming 'student_id' is the correct field
            'gpa' => $student->gpa, // Assuming 'gpa' is the correct field
            'level' => $student->level, // Assuming 'level' is the correct field
            'total_number_of_hours' => $student->total_credit_hours ?? 0, // Assuming this field exists
            'current_semester' => $registeredCourses->first()->semester->description ?? null, // Get semester from the first registered course
            'total_registered_hours' => $totalRegisteredHours,
            'total_numberof_hours' => $totalnumberofHours,
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

