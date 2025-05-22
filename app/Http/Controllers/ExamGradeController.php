<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ExamGrade;
use App\Models\StudentData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamGradeController extends Controller
{
    public function index(Course $course)
    {
        $students = $course->students;
        $midtermGrades = ExamGrade::where('course_id', $course->id)
            ->where('exam_type', 'midterm')
            ->get()
            ->keyBy('student_id');

        $finalGrades = ExamGrade::where('course_id', $course->id)
            ->where('exam_type', 'final')
            ->get()
            ->keyBy('student_id');

        return view('exam-grades.index', compact('course', 'students', 'midtermGrades', 'finalGrades'));
    }

    public function store(Request $request, Course $course)
    {
        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students_data,id',
            'grades.*.midterm_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.final_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.midterm_notes' => 'nullable|string',
            'grades.*.final_notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $course) {
            foreach ($request->grades as $gradeData) {
                // Update or create midterm grade
                if (isset($gradeData['midterm_grade'])) {
                    ExamGrade::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'student_id' => $gradeData['student_id'],
                            'exam_type' => 'midterm'
                        ],
                        [
                            'grade' => $gradeData['midterm_grade'],
                            'notes' => $gradeData['midterm_notes'] ?? null
                        ]
                    );
                }

                // Update or create final grade
                if (isset($gradeData['final_grade'])) {
                    ExamGrade::updateOrCreate(
                        [
                            'course_id' => $course->id,
                            'student_id' => $gradeData['student_id'],
                            'exam_type' => 'final'
                        ],
                        [
                            'grade' => $gradeData['final_grade'],
                            'notes' => $gradeData['final_notes'] ?? null
                        ]
                    );
                }
            }
        });

        return redirect()->back()->with('success', 'Grades have been saved successfully.');
    }
}
