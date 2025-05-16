<?php

namespace App\Http\Controllers;

use App\Models\CourseRegistration;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\StudentData; // Make sure StudentData is imported
use App\Models\CourseStudent; // Import CourseStudent model

class StudentDataController extends Controller
{
    // ... (Existing methods in your StudentDataController)

    /**
     * Get student course results for a specific semester with filtering and pagination.
     *
     * @param  int  $semester_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudentCourseResults(int $semester_id, Request $request): JsonResponse
    {
        // 4.1. Receive Parameters
        $year = $request->query('year');
        $courseCode = $request->query('course_code');
        $passStatus = $request->query('pass_status');
        $page = $request->query('page', 1);

        // 4.2. Build the Query
        $query = CourseRegistration::where('semester_id', $semester_id)
            ->join('students_data', 'course_registrations.student_id', '=', 'students_data.id')
            ->join('courses', 'course_registrations.course_id', '=', 'courses.id')
            ->join('course_student', function ($join) {
                $join->on('course_registrations.student_id', '=', 'course_student.student_id')
                    ->on('course_registrations.course_id', '=', 'course_student.course_id');
            })
            ->select(
                'students_data.name as student_name',
                'courses.name as course_name',
                'courses.code as course_code',
                'course_student.grade as grade'
            );

        // 4.2.1. Apply Filters
        if ($year) {
            $query->where('students_data.academic_year', $year);
        }

        if ($courseCode) {
            $query->where('courses.code', $courseCode);
        }

        // 4.3. Pagination
        $perPage = 10; // You can adjust this
        $results = $query->paginate($perPage, ['*'], 'page', $page);

        // 4.4. Calculate 'pass' Status
        $passingGrade = 60; // Define your passing grade
        foreach ($results as $result) {
            $result->pass = ($result->grade !== null && (int)$result->grade >= $passingGrade) ? 'Yes' : 'No';
        }

        // 4.4.1. Apply 'pass' Status Filter
        if ($passStatus) {
            $results->getCollection()->transform(function ($item) use ($passStatus) {
                if ($passStatus == 'pass' && $item->pass == 'Yes') {
                    return $item;
                } elseif ($passStatus == 'fail' && $item->pass == 'No') {
                    return $item;
                }
                return null;
            });

            $results->setCollection($results->getCollection()->filter()); // Remove null items
        }

        // 4.5. Format the Response
        $formattedResults = $results->items()->map(function ($item) {
            return [
                'name' => $item->student_name,
                'subject' => $item->course_name,
                'code' => $item->course_code,
                'pass' => $item->pass,
                'mark' => $item->grade,
                //'edit' => '...', // Add edit link/button info if needed
            ];
        });

        return response()->json([
            'data' => $formattedResults,
            'pagination' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'total' => $results->total(),
                'per_page' => $results->perPage(),
            ],
        ], 200);
    }
}


