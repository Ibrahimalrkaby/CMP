<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Exam Grades Routes
    Route::get('/courses/{course}/exam-grades', [App\Http\Controllers\ExamGradeController::class, 'index'])->name('exam-grades.index');
    Route::post('/courses/{course}/exam-grades', [App\Http\Controllers\ExamGradeController::class, 'store'])->name('exam-grades.store');
});
