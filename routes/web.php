<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BinarySearchController;
use App\Http\Controllers\CompletedTaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PriorityTaskController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SelectionSortController;
use App\Http\Controllers\SortedTaskController;
use App\Http\Controllers\StudyFileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UpcomingTaskController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');


// Password Reset
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');


Route::middleware('auth')->group(function () {

    // Home (now redirects to the dashboard)
    Route::get('/home', fn () => redirect()->route('dashboard'))
        ->name('home');


    // Dashboard (sidebar-style overview)
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    // Account
    Route::get('/account', [AccountController::class, 'index'])
        ->name('account.index');

    Route::post('/account/password', [AccountController::class, 'updatePassword'])
        ->name('account.password.update');


    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])
        ->name('subjects.index');

    Route::post('/subjects', [SubjectController::class, 'store'])
        ->name('subjects.store');

    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])
        ->name('subjects.destroy');


    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    Route::patch('/tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update');

    // NEW: Done button
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->name('tasks.complete');

    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy');


    // Completed Tasks
    Route::get('/completed-tasks', [CompletedTaskController::class, 'index'])
        ->name('completed_tasks.index');


    // Upcoming Tasks
    Route::get('/upcoming-tasks', [UpcomingTaskController::class, 'index'])
        ->name('upcoming_tasks.index');


    // Schedule
    Route::get('/schedule', [ScheduleController::class, 'index'])
        ->name('schedule.index');

    Route::post('/schedule', [ScheduleController::class, 'store'])
        ->name('schedule.store');

    Route::delete('/schedule/{schedule}', [ScheduleController::class, 'destroy'])
        ->name('schedule.destroy');


    // Study Space - My Notes
    Route::get('/notes', [NoteController::class, 'index'])
        ->name('notes.index');

    Route::post('/notes', [NoteController::class, 'store'])
        ->name('notes.store');

    Route::patch('/notes/{note}', [NoteController::class, 'update'])
        ->name('notes.update');

    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');


    // Study Space - My Files
    Route::get('/study-files', [StudyFileController::class, 'index'])
        ->name('study_files.index');

    Route::post('/study-files', [StudyFileController::class, 'store'])
        ->name('study_files.store');

    Route::get('/study-files/{studyFile}', [StudyFileController::class, 'show'])
        ->name('study_files.show');

    Route::get('/study-files/{studyFile}/preview', [StudyFileController::class, 'preview'])
        ->name('study_files.preview');

    Route::get('/study-files/{studyFile}/download', [StudyFileController::class, 'download'])
        ->name('study_files.download');

    Route::delete('/study-files/{studyFile}', [StudyFileController::class, 'destroy'])
        ->name('study_files.destroy');


    // DSA - Priority Tasks
    Route::get('/priority-tasks', [PriorityTaskController::class, 'index'])
        ->name('priority_tasks.index');


    // DSA - Sorted Tasks
    Route::get('/sorted-tasks', [SortedTaskController::class, 'index'])
        ->name('sorted_tasks.index');


    // DSA - Binary Search
    Route::get('/binary-search', [BinarySearchController::class, 'index'])
        ->name('binary_search.index');


    // DSA - Selection Sort
    Route::get('/selection-sort', [SelectionSortController::class, 'index'])
        ->name('selection_sort.index');

});