<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\CourseController;
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
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentDataController;

use App\Models\Teacher;

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
    Route::middleware('auth')->group(function () {
        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');
        Route::get('me', 'me')->name('me');
    });
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
Route::post('course-semester', [CourseSemesterController::class, 'store'])->name('course.semester.store');

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
Route::middleware('auth:api')->prefix('mailbox')->name('mailbox.')->controller(MailboxController::class)->group(function () {
    Route::post('/', 'sendMessage')->name('send');
    Route::get('/student/{student_id}', 'studentInbox')->name('inbox');
    Route::get('/read/{id}', 'readMessage')->name('read');
    Route::delete('/{id}', 'deleteMessage')->name('delete');
});

// events routes
Route::prefix('events')->name('events.')->controller(EventController::class)->group(function () {
    Route::post('/', 'store')->name('store');
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

// fees routes
Route::prefix('fees')->name('fees.')->controller(FeeController::class)->group(function () {
    Route::post('/', 'store')->name('store');
    Route::get('/student/{student_id}', 'getFees')->name('get.fees');
});

// course schedule routes
Route::get('/student/{student_id}/schedules', [CourseScheduleController::class, 'index']);
Route::get('/teacher/{student_id}/schedules', [CourseScheduleController::class, 'teacherSchedule']);

// course exam routes
Route::get('/student/{student_id}/exam-schedule', [ExamScheduleController::class, 'studentExamSchedule']);
Route::get('/teacher/{teacher_id}/exam-schedule', [ExamScheduleController::class, 'teacherExamSchedule']);

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
    Route::post('/', 'store')->name('store')->middleware('teacher');
});


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });




//  API for getRegisteredCourses 

Route::apiResource('students', StudentDataController::class);
Route::get('/students/{student}/registered-courses', [StudentDataController::class, 'getRegisteredCourses']);

// API for confirmRegistration

Route::apiResource('students', StudentDataController::class);
Route::get('/students/{student}/registered-courses', [StudentDataController::class, 'getRegisteredCourses']);
Route::patch('/students/{student}/confirm-registration', [StudentDataController::class, 'confirmRegistration']);

