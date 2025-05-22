<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Lecture;
use App\Models\StudentData;

class AddStudentToLecture extends Command
{
    protected $signature = 'lecture:add-student {lecture_id} {student_id}';
    protected $description = 'Add a student to a lecture';

    public function handle()
    {
        $lectureId = $this->argument('lecture_id');
        $studentId = $this->argument('student_id');

        try {
            $lecture = Lecture::findOrFail($lectureId);
            $student = StudentData::findOrFail($studentId);

            $lecture->student_id = $studentId;
            $lecture->save();

            $this->info("Successfully added student {$student->name} to lecture ID {$lectureId}");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}
