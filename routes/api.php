<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\StudentAuthController;
use App\Http\Controllers\TeacherAuthController;
use App\Http\Controllers\TeacherController;


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

// //Student Auth Route
// Route::controller(StudentAuthController::class)->name('students.')->prefix('students')->group(function () {
//     Route::post('register', 'register')->name('register');
//     Route::post('login', 'login')->name('login');
//     Route::middleware('auth')->group(function () {
//         Route::post('logout', 'logout')->name('logout');
//         Route::post('refresh', 'refresh')->name('refresh');
//         Route::get('me', 'me')->name('me');
//     });
// });

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
    
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
