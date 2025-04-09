<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\TCourseController;
use App\Http\Controllers\TPostController;
use App\Http\Controllers\TStudentController;
use App\Http\Controllers\TSubCourseController;
use App\Http\Controllers\TTaskController;
use App\Http\Controllers\TVideoCallController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// Ruta para obtener información del usuario autenticado 
Route::prefix('admin')->middleware(['auth:sanctum', 'check.role:admin'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/users', [UserController::class, 'getUsers']);
    Route::post('/users', [UserController::class, 'createUser']);
    Route::get('/users/{id}', [UserController::class, 'getUserById']);
    Route::put('/users/{id}', [UserController::class, 'updateUser']);
    Route::delete('/users/{id}', [UserController::class, 'deleteUser']);
    Route::get('/teachers', [UserController::class, 'getTeachers']);

    //getGenres
});


//register teachers
Route::post('/registerTeacher', [UserController::class, 'registerTeacher']);
Route::get('/genres', [GenreController::class, 'getGenres']);

// Rutas para Docentes
Route::prefix('teacher')->middleware(['auth:sanctum', 'check.role:teacher'])->group(function () {
    // Aquí puedes añadir rutas específicas para docentes
    Route::get('/user', [AuthController::class, 'getUser']);
    //course and sub courses
    Route::get('/courses', [TCourseController::class, 'index']);
    Route::post('/courses', [TCourseController::class, 'store']);
    Route::get('/courses/{id}', [TCourseController::class, 'show']);
    Route::put('/courses/{id}', [TCourseController::class, 'update']);
    Route::delete('/courses/{id}', [TCourseController::class, 'destroy']);

    //Subcourses 
    Route::get('/list-subcourses/{course_id}', [TSubCourseController::class, 'subCourseByIdCourse']);
    Route::resource('subcourses', TSubCourseController::class)->only(['store', 'show', 'update', 'destroy']);

    //tareas
    Route::get('/SubCourse-tasks/{id}', [TTaskController::class, 'TasksByIdSubCourse']);
    Route::post('/tasks', [TTaskController::class, 'store']);
    Route::get('/tasks/{id}', [TTaskController::class, 'show']);
    Route::put('/tasks/{id}', [TTaskController::class, 'update']);
    Route::delete('/tasks/{id}', [TTaskController::class, 'destroy']);
    //delete questions and answers
    Route::delete('/delete-questions/{id}', [TTaskController::class, 'destroyQuestion']);
    Route::delete('/delete-answers/{id}', [TTaskController::class, 'destroyAnswer']);

    //students apis
    Route::get('/students-subcourse/{subcourse_id}', [TStudentController::class, 'allStudentsByDId_SubCId']);
    Route::get('/students', [TStudentController::class, 'allStudentsByDocente']);
    Route::post('/students', [TStudentController::class, 'store']);
    Route::get('/students/{id}', [TStudentController::class, 'show']);
    Route::put('/students/{id}', [TStudentController::class, 'update']);
    Route::delete('/students/{id}', [TStudentController::class, 'destroy']);

    //video call
    Route::get('/video-calls/{subcourse_id}/{docente_id}', [TVideoCallController::class, 'indexVideoCall']);
    Route::post('/video-call', [TVideoCallController::class, 'store']);
    Route::get('/video-call/{id}', [TVideoCallController::class, 'show']);
    Route::put('/video-call/{id}', [TVideoCallController::class, 'update']);
    Route::delete('/video-call/{id}', [TVideoCallController::class, 'destroy']);

    //posts
    Route::get('/posts/{curso_id}', [TPostController::class, 'indexPosts']);
    Route::post('/posts', [TPostController::class, 'store']);
});
