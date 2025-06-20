<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\CoursePrerequisiteController;
use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\CourseScheduleController;
use App\Http\Controllers\CourseSemesterController;
use App\Http\Controllers\CourseStudentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\StudentDataController;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\TeacherController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// routes/api.php

//Student Auth Route
Route::controller(StudentAuthController::class)->name('students.')->prefix('students')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    // Route::middleware('auth')->group(function () {
    Route::post('logout', 'logout')->name('logout');
    Route::post('refresh', 'refresh')->name('refresh');
    Route::get('me', 'me')->name('me');
    // });
});

//Admin Auth Route
Route::controller(AdminAuthController::class)->name('admins.')->prefix('admins')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::middleware('admin')->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::get('me', 'me')->name('me');
    });
});

//Teacher Auth Route
Route::controller(TeacherAuthController::class)->name('teacher.')->prefix('teacher')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::middleware('teacher')->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::get('me', 'me')->name('me');
    });
});

// student data routes
Route::controller(StudentController::class)->prefix('students')->name('students.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});

// admin data routes

Route::prefix('admin')->name('admin.')->controller(AdminController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});


// teacher data routes
Route::controller(TeacherController::class)->prefix('teachers')->name('teachers.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});

// program data routes
Route::controller(ProgramController::class)->prefix('programs')->name('programs.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});


// semester data routes

Route::controller(SemesterController::class)->prefix('semesters')->name('semesters.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
    Route::post('{id}/courses', 'addCourses')->name('addCourses');
    Route::get('{id}/courses', 'getCourses')->name('getCourses');
});

// course data routes
Route::controller(CourseController::class)->prefix('courses')->name('courses.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});

// store or update course student
Route::put('course/{courseId}/student/{studentId}/grade', [CourseStudentController::class, 'storeOrUpdateGrade'])->name('course.student.grade');

// store course semester
Route::controller(CourseSemesterController::class)->prefix('course-semesters')->name('courseSemesters.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{course_id}/{semester_id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{course_id}/{semester_id}', 'update')->name('update');
    Route::delete('{course_id}/{semester_id}', 'destroy')->name('destroy');
});


// course registration routes
Route::prefix('registration')->controller(CourseRegistrationController::class)->name('registration.')->group(function () {
    Route::post('/', 'register')->name('register');
    Route::get('student/{student_id}', 'studentCourses')->name('student.courses');
    Route::put('status/{id}', 'updateStatus')->name('update.status');
    Route::get('semester/{semester_id}', 'semesterRegistrations')->name('semester.registrations');
    Route::get('/', 'index')->name('index');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// course prerequest routes
Route::prefix('course-prerequisites')->controller(CoursePrerequisiteController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/{course_id}', 'show');
    Route::delete('/', 'destroy');
});

// mail box routes
Route::prefix('mailbox')->name('mailbox.')->controller(MailboxController::class)->group(function () {
    Route::post('/', 'sendMessage')->name('send');
    Route::get('/student/{student_id}', 'studentInbox')->name('inbox');
    Route::get('/read/{id}', 'readMessage')->name('read');
    Route::delete('/{id}', 'deleteMessage')->name('delete');
});

// events routes
Route::prefix('events')->name('events.')->controller(EventController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
});

// fees routes
Route::prefix('fees')->name('fees.')->controller(FeeController::class)->group(function () {
    Route::post('/', 'store')->name('store');
    Route::get('/student/{student_id}', 'getFees')->name('get.fees');
});

// course schedule routes
Route::get('/student/{student_id}/schedules', [CourseScheduleController::class, 'index']);
Route::get('/teacher/{teacher_id}/schedules', [CourseScheduleController::class, 'teacherSchedule']);


// course exam routes
Route::controller(ExamScheduleController::class)->prefix('exam-schedules')->name('exam-schedules.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');

    Route::get('student/{student_id}', 'studentExamSchedule')->name('student.schedule');
    Route::get('teacher/{teacher_id}', 'teacherExamSchedule')->name('teacher.schedule');
});

// studenr result (doctor)
Route::get('semesters/{semester_id}/students/results', [StudentDataController::class, 'getStudentCourseResults'])->name('student.results');


//Admin Auth Route
Route::controller(AdminAuthController::class)->name('admins.')->prefix('admins')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::middleware('admin')->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::get('me', 'me')->name('me');
    });
});

//Teacher Auth Route
Route::controller(TeacherAuthController::class)->name('teacher.')->prefix('teacher')->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::middleware('teacher')->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::get('me', 'me')->name('me');
    });
});


//Chat Route
Route::controller(ChatController::class)->name('chats.')->prefix('chats')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('/', 'store')->name('store');
});


//  API for getRegisteredCourses (doctor)
Route::get('/students/{student}/registered-courses', [CourseRegistrationController::class, 'getRegisteredCourses']);

// API for confirmRegistration(doctor)
Route::put('/students/{student}/confirm-registration', [CourseRegistrationController::class, 'confirmRegistration']);


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// attendance
Route::controller(LectureController::class)->group(function () {
    Route::post('/lectures', 'store')->name('store');
    Route::put('/lectures/{lecture}/attendance', 'updateAttendance');
    Route::get('/lectures/{lecture}/attendance', 'getAttendance');
});


//attendance
Route::post('/lectures/{lecture}/attendance', [AttendanceController::class, 'createAttendance'])
    ->name('attendance.create');

Route::put('/lectures/{lecture}/attendance', [AttendanceController::class, 'updateAttendance'])
    ->name('attendance.update');

Route::get('/lectures/{lecture}/attendance', [AttendanceController::class, 'getAttendance'])
    ->name('attendance.show');


Route::post('/lectures', [LectureController::class,]);
Route::put('/lectures/{lecture}/attendance', [LectureController::class, 'updateAttendance']);
Route::get('/lectures/{lecture}/attendance', [LectureController::class, 'getAttendance']);


// lecture
Route::controller(LectureController::class)->group(function () {
    Route::post('/lectures', 'store')->name('store');
    Route::put('/lectures/{lecture}/attendance', 'updateAttendance');
    Route::get('/lectures/{lecture}/attendance', 'getAttendance');
});
//Grade
Route::prefix('grades')->group(function () {
    Route::post('/{courseId}', [GradeController::class, 'store']);             // Add student grade
    Route::get('/{courseId}', [GradeController::class, 'index']);              // Get all grades for course
    Route::get('/{courseId}/{studentId}', [GradeController::class, 'show']);   // Get grade for student
    Route::put('/{courseId}/{studentId}', [GradeController::class, 'update']); // Update student grade
    Route::delete('/{courseId}/{studentId}', [GradeController::class, 'destroy']); // Delete grade
});


//Course
Route::name('courses')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);           // Get all courses
    Route::post('/courses', [CourseController::class, 'store']);          // Create course (with grade table)
    Route::get('/courses/{id}', [CourseController::class, 'show']);       // Get single course
    Route::put('/courses/{id}', [CourseController::class, 'update']);     // Update course
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']); // Delete course and its grade table
});



//Lecture
Route::name('lecture')->group(function () {
    Route::get('/lecture', [LectureController::class, 'index']);           // Get all lectures
    Route::post('/lecture', [LectureController::class, 'store']);          // Create lecture
    Route::get('/lecture/{id}', [LectureController::class, 'show']);       // Get single lecture
    Route::put('/lecture/{id}', [LectureController::class, 'update']);     // Update lecture
    Route::delete('/lecture/{id}', [LectureController::class, 'destroy']); // Delete lecture
});


//Attendance
Route::prefix('attendance')->group(function () {
    Route::post('/{lectureId}', [AttendanceController::class, 'store']);             // Add student attendance
    Route::get('/{lectureId}', [AttendanceController::class, 'index']);              // Get all attendance for course
    Route::get('/{lectureId}/{studentId}', [AttendanceController::class, 'show']);   // Get attendance for student
    Route::put('/{lectureId}/{studentId}', [AttendanceController::class, 'update']); // Update student attendance
    Route::delete('/{lectureId}/{studentId}', [AttendanceController::class, 'destroy']); // Delete attendance
});
