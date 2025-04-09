<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\SubCourse;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;

class TSubCourseController extends Controller
{


    public function subCourseByIdCourse($course_id)
    {
        try {
            // Buscar el curso con sus subcursos
            $course = Course::with('subCourses')->find($course_id);

            // Validar si el curso existe
            if (!$course) {
                return response()->json(['message' => 'Curso no encontrado'], 404);
            }


            // Retornar el curso con sus subcursos en una estructura clara
            return response()->json([
                'course' => [
                    'id' => $course->id,
                    'name' => $course->name,
                    'description' => $course->description,
                    'status' => $course->status,
                    'sub_courses' => $course->subCourses
                ]
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error en la consulta a la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener los datos',
                'message' => $th->getMessage()
            ], 500);
        }
    }



    public function index()
    {
        try {
            $subCourse = SubCourse::with('course')->with('docente')->get();
            if ($subCourse->isEmpty()) {
                return response()->json(['message' => 'No hay sub-cursos registrados'], 404);
            }

            return response()->json($subCourse, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura errores de base de datos
            return response()->json([
                'error' => 'Error en la consulta a la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener los sub cursos',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|integer|exists:courses,id',
                'docente_id' => 'required|integer|exists:users,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'required|integer|min:1',
                'level' => 'required|string|max:50',
            ]);

            $subCourse = SubCourse::create([
                'course_id' => $request->course_id,
                'docente_id' => $request->docente_id,
                'name' => $request->name,
                'description' => $request->description,
                'duration' => $request->duration,
                'level' => $request->level,
                'status' => true
            ]);

            return response()->json(
                [
                    'message' => 'Sub curso creado exitosamente',
                    'subCurso' => $subCourse
                ],
                201
            );
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura errores de base de datos
            return response()->json([
                'error' => 'Error en la creación del sub curso',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al crear',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $subCourse = SubCourse::with('docente')->find($id);

            if (!$subCourse) {
                return response()->json(['message' => 'Data no encontrado'], 404);
            }

            return response()->json($subCourse, 200);
        } catch (\Illuminate\Database\QueryException $e) {
            // Captura errores de base de datos
            return response()->json([
                'error' => 'Error en la consulta a la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener los datos',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $subCourse = SubCourse::findOrFail($id);

            $request->validate([
                'course_id' => 'sometimes|integer|exists:courses,id',
                'docente_id' => 'sometimes|integer|exists:users,id',
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'duration' => 'sometimes|integer|min:1',
                'level' => 'sometimes|string|max:50',
                'status' => 'sometimes|boolean'
            ]);


            $data = $request->all();

            $subCourse->update($data);


            return response()->json([
                'message' => 'Dato actualizado correctamente',
                'sub_course' => $subCourse
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Datos no encontrado',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error al actualizar los datos en la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error inesperado al actualizar',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $subCourse = SubCourse::find($id);
            if (!$subCourse) {
                return response()->json(['message' => 'Curso no encontrado'], 404);
            }
            $subCourse->delete();
            return response()->json([
                'message' => 'Dato eliminado correctamente'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error al eliminar los datos de la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error inesperado al eliminar',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
