<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\SubCourse;
use App\Models\Task;
use Illuminate\Http\Request;

class SVirtualClassroomController extends Controller
{
    public function getSubCoursesStudent(Request $request)
    {
        try {
            // Intenta obtener el estudiante autenticado usando el guard 'student'
            $student = auth('student')->user();

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'Estudiante no autenticado.'
                ], 401); // Unauthorized
            }

            // Consulta los subcursos a través de las inscripciones activas
            $subcourses = $student->enrollments()
                ->where('active', true)
                ->with('subCourse')
                ->get()
                ->pluck('subCourse')
                ->filter(); // filtra nulos si hubiera alguno

            if ($subcourses->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No hay subcursos disponibles.',
                    'data' => [],
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Subcursos obtenidos correctamente.',
                'data' => $subcourses->values(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener los subcursos.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }


    public function getTasksBySubCourse(Request $request, $subcourse_id)
    {
        try {
            $student = auth('student')->user();

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'Estudiante no autenticado.'
                ], 401);
            }

            // Verifica si el estudiante está inscrito activamente en el subcurso
            $enrollment = $student->enrollments()
                ->where('active', true)
                ->where('subcourse_id', $subcourse_id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'status' => true,
                    'message' => 'El estudiante no está inscrito en este subcurso o la inscripción no está activa.',
                    'data' => [],
                ]);
            }

            // Cargar las tareas del subcurso directamente
            $subcourse = SubCourse::with('tasks')->find($subcourse_id);

            if (!$subcourse || $subcourse->tasks->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No hay tareas disponibles para este subcurso.',
                    'data' => [],
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Tareas obtenidas correctamente.',
                'data' => $subcourse->tasks,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener las tareas.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTasksById(Request $request, $task_id)
    {
        try {
            $student = auth('student')->user();

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'Estudiante no autenticado.'
                ], 401);
            }

            // Obtener tarea con relaciones necesarias
            $task = Task::with([
                'subcourse.course',
                'subcourse.docente',
                'questions.options'
            ])->find($task_id);

            if (!$task) {
                return response()->json([
                    'status' => true,
                    'message' => 'La tarea no existe.',
                    'data' => [],
                ]);
            }

            // Verificar que el estudiante esté inscrito activamente en el subcurso
            $isEnrolled = $student->enrollments()
                ->where('active', true)
                ->where('subcourse_id', $task->sub_course_id)
                ->exists();

            if (!$isEnrolled) {
                return response()->json([
                    'status' => false,
                    'message' => 'El estudiante no está inscrito en el subcurso de esta tarea.',
                    'data' => [],
                ], 403);
            }

            // Preparar los datos filtrando is_correct
            $taskData = $task->toArray();

            // Filtrar las respuestas y eliminar 'is_correct'
            foreach ($taskData['questions'] as &$question) {
                foreach ($question['options'] as &$option) {
                    unset($option['is_correct']);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Tarea obtenida correctamente.',
                'data' => $taskData,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener la tarea.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    public function getMeetingsBySubCourse(Request $request, $subcourse_id)
    {
        try {
            $student = auth('student')->user();

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'Estudiante no autenticado.'
                ], 401);
            }

            // Verifica si el estudiante está inscrito activamente en el subcurso
            $enrollment = $student->enrollments()
                ->where('active', true)
                ->where('subcourse_id', $subcourse_id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'status' => true,
                    'message' => 'El estudiante no está inscrito en este subcurso o la inscripción no está activa.',
                    'data' => [],
                ]);
            }

            // Obtener las reuniones directamente desde el modelo Appointment
            $meetings = Appointment::with('docente')
                ->where('sub_course_id', $subcourse_id)
                ->get();

            if ($meetings->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'No hay reuniones disponibles para este subcurso.',
                    'data' => [],
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Reuniones obtenidas correctamente.',
                'data' => $meetings,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener las reuniones.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getMeetingById(Request $request, $appointment_id)
    {
        try {
            $student = auth('student')->user();

            if (!$student) {
                return response()->json([
                    'status' => false,
                    'message' => 'Estudiante no autenticado.'
                ], 401);
            }

            // Buscar la reunión y cargar la relación con el docente
            $appointment = Appointment::with('docente')
                ->find($appointment_id);

            if (!$appointment) {
                return response()->json([
                    'status' => true,
                    'message' => 'Reunión no encontrada.',
                    'data' => null,
                ]);
            }

            // Verificar si el estudiante está inscrito al subcurso de la reunión
            $enrollment = $student->enrollments()
                ->where('active', true)
                ->where('subcourse_id', $appointment->sub_course_id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'status' => true,
                    'message' => 'No tienes acceso a esta reunión.',
                    'data' => null,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Reunión obtenida correctamente.',
                'data' => $appointment,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ocurrió un error al obtener la reunión.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
