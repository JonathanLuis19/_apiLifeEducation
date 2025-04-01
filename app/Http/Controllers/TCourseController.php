<?php

namespace App\Http\Controllers;

use App\Http\Requests\createCourseRequest;
use App\Models\Course;
use App\Models\SubCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TCourseController extends Controller
{

    public function index()
    {
        try {
            $userId = Auth::id(); // Obtiene el ID del usuario autenticado

            $courses = Course::with(['docente', 'subCourses'])
                ->where('docente_id', $userId) // Filtra por el docente autenticado
                ->get();

            if ($courses->isEmpty()) {
                return response()->json([
                    'message' => 'No tienes cursos asignados'
                ], 200); // Envía un 200 OK con el mensaje
            }

            return response()->json($courses, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error en la consulta a la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener los cursos',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function store(createCourseRequest $request)
    {
        try {
            $userId = $request->user()->id; // Obtiene el ID del docente autenticado

            $course = DB::transaction(function () use ($request, $userId) {
                return Course::create([
                    'docente_id' => $userId,
                    'name' => $request->name,
                    'description' => $request->description,
                    'status' => true,
                ]);
            });

            return response()->json([
                'message' => 'Curso creado exitosamente',
                'course' => $course
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al crear el curso',
                'message' => $th->getMessage()
            ], 500);
        }
    }




    public function show($id)
    {
        try {

            $course = Course::with(['docente'])
                ->find($id);


            if (!$course) {
                return response()->json(['message' => 'Curso no encontrado'], 404);
            }

            return response()->json($course, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura errores de base de datos
            return response()->json([
                'error' => 'Error en la consulta a la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener el curso',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(createCourseRequest $request, $id)
    {
        try {
            // Buscar el curso o lanzar excepción si no existe
            $course = Course::findOrFail($id);

            // Obtener los datos validados
            $validatedData = $request->validated();

            // Iniciar una transacción para actualizar el curso
            DB::transaction(function () use ($course, $validatedData) {
                $course->update($validatedData);
            });

            // Responder con los datos actualizados
            return response()->json([
                'message' => 'Curso actualizado correctamente',
                'course' => $course
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Curso no encontrado',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error al actualizar el curso en la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error inesperado al actualizar el curso',
                'message' => $e->getMessage()
            ], 500);
        }
    }




    public function destroy($id)
    {
        try {
            $course = Course::find($id);
            if (!$course) {
                return response()->json(['message' => 'Curso no encontrado'], 404);
            }
            $course->delete();
            return response()->json([
                'message' => 'Curso eliminado correctamente'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al eliminar el curso',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
