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
use App\Http\Controllers\SortedTaskController;
use App\Http\Controllers\StudyFileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UpcomingTaskController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return redirect()->route('dashboard');
    // DSA - Sorted Tasks (Bubble Sort)
    Route::get('/sorted-tasks', [SortedTaskController::class, 'index'])->name('sorted_tasks.index');

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

// Office link
Route::get('/study-files/{studyFile}/office', [StudyFileController::class, 'office'])
    ->middleware('signed')
    ->name('study_files.office');

// All routes below require authentication
Route::middleware('auth')->group(function () {

    // Home
    Route::get('/home', fn () => redirect()->route('dashboard'))
        ->name('home');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Account
    Route::get('/account', [AccountController::class, 'index'])
        ->name('account.index');

    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])
        ->name('account.profile.update');

    Route::post('/account/password', [AccountController::class, 'updatePassword'])
        ->name('account.password.update');

    Route::post('/account/sessions/logout-others', [AccountController::class, 'logoutOtherDevices'])
        ->name('account.sessions.logout_others');

    Route::delete('/account', [AccountController::class, 'destroy'])
        ->name('account.destroy');

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])
        ->name('subjects.index');

    Route::post('/subjects', [SubjectController::class, 'store'])
        ->name('subjects.store');

    Route::patch('/subjects/{subject}', [SubjectController::class, 'update'])
        ->name('subjects.update');

    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])
        ->name('subjects.destroy');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])
        ->name('tasks.index');

    // NEW: the Add Task page (the "Add Task" button on the Tasks page opens it)
    Route::get('/tasks/create', [TaskController::class, 'create'])
        ->name('tasks.create');

    Route::post('/tasks', [TaskController::class, 'store'])
        ->name('tasks.store');

    // IMPORTANT: keep delete-all before /tasks/{task}
    Route::delete('/tasks/delete-all', [TaskController::class, 'destroyAll'])
        ->name('tasks.destroy_all');

    // NEW: the Edit Task page (the "Edit" button on every task opens it)
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])
        ->name('tasks.edit');

    Route::patch('/tasks/{task}', [TaskController::class, 'update'])
        ->name('tasks.update');

    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->name('tasks.complete');

    // Delete selected tasks
    Route::delete('/tasks', [TaskController::class, 'bulkDestroy'])
        ->name('tasks.bulk_destroy');

    // Delete one task
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

    Route::patch('/schedule/{schedule}', [ScheduleController::class, 'update'])
        ->name('schedule.update');

    Route::delete('/schedule/{schedule}', [ScheduleController::class, 'destroy'])
        ->name('schedule.destroy');

    // Notes
    Route::get('/notes', [NoteController::class, 'index'])
        ->name('notes.index');

    Route::post('/notes', [NoteController::class, 'store'])
        ->name('notes.store');

    Route::patch('/notes/{note}', [NoteController::class, 'update'])
        ->name('notes.update');

    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])
        ->name('notes.destroy');

    // Study Files
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

    // DSA - Binary Search
    Route::get('/binary-search', [BinarySearchController::class, 'index'])
        ->name('binary_search.index');
    // DSA - Sorted Tasks (Bubble Sort)
    Route::get('/sorted-tasks', [SortedTaskController::class, 'index'])->name('sorted_tasks.index');

});
