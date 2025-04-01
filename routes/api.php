<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\TCourseController;
use App\Http\Controllers\TSubCourseController;
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
});
