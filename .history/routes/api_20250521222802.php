<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\TeacherAuthController;

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

// //Admin Auth Route
// Route::controller(AdminAuthController::class)->name('admins.')->prefix('admins')->group(function () {
//     Route::post('register', 'register')->name('register');
//     Route::post('login', 'login')->name('login');
//     Route::middleware('admin')->group(function () {
//         Route::post('logout', 'logout')->name('logout');
//         Route::post('refresh', 'refresh')->name('refresh');
//         Route::get('me', 'me')->name('me');
//     });
// });

// //Teacher Auth Route
// Route::controller(TeacherAuthController::class)->name('teacher.')->prefix('teacher')->group(function () {
//     Route::post('register', 'register')->name('register');
//     Route::post('login', 'login')->name('login');
//     Route::middleware('teacher')->group(function () {
//         Route::post('logout', 'logout')->name('logout');
//         Route::post('refresh', 'refresh')->name('refresh');
//         Route::get('me', 'me')->name('me');
//     });
// });

// student data routes
Route::controller(StudentController::class)->prefix('admin')->name('students.')->middleware('admin')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('{id}', 'show')->name('show');
    Route::post('/', 'store')->name('store');
    Route::put('{id}', 'update')->name('update');
    Route::delete('{id}', 'destroy')->name('destroy');
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


//Chat Route
Route::controller(ChatController::class)->name('chats.')->prefix('chats')->group(function () {
    Route::get('/', 'show')->name('show');
    Route::post('/', 'store')->name('store');
});


// attendance
Route::controller(LectureController::class)->group(function () {
    Route::post('/lectures', 'store')->name('store');
    Route::put('/lectures/{lecture}/attendance', 'updateAttendance');
    Route::get('/lectures/{lecture}/attendance', 'getAttendance');
});



//Grade
Route::middleware('auth:api')->prefix('grades')->group(function () {
    Route::post('/{courseId}', [GradeController::class, 'store']);             // Add student grade
    Route::get('/{courseId}', [GradeController::class, 'index']);              // Get all grades for course
    Route::get('/{courseId}/{studentId}', [GradeController::class, 'show']);   // Get grade for student
    Route::put('/{courseId}/{studentId}', [GradeController::class, 'update']); // Update student grade
    Route::delete('/{courseId}/{studentId}', [GradeController::class, 'destroy']); // Delete grade
});


//Course
Route::middleware('auth:api')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);           // Get all courses
    Route::post('/courses', [CourseController::class, 'store']);          // Create course (with grade table)
    Route::get('/courses/{id}', [CourseController::class, 'show']);       // Get single course
    Route::put('/courses/{id}', [CourseController::class, 'update']);     // Update course
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']); // Delete course and its grade table
});
