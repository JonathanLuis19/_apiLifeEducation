<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Roles;
use App\Models\RoleUserStudent;
use App\Models\Student;
use App\Models\SubCourse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TStudentController extends Controller
{

    /**
     * Lista de todos los estudiantes que tiene el docente.
     *
     */

    public function allStudentsByDId_SubCId($subcourse_id)
    {
        try {
            $userId = Auth::id();

            // Buscar el subcurso asignado a ese docente
            $subcourse = SubCourse::where('id', $subcourse_id)
                ->where('docente_id', $userId)
                ->first();

            // Si el subcurso no existe o no está asignado, devolver el nombre como `null` y un array vacío
            if (!$subcourse) {
                return response()->json([
                    'subcourse_name' => null,
                    'students' => []
                ], 200);
            }

            // Obtener todos los estudiantes inscritos en este subcurso
            $students = Enrollment::where('subcourse_id', $subcourse->id)
                ->with('student') // Relación con el modelo Student
                ->get()
                ->pluck('student');

            // Respuesta con el nombre del subcurso y los estudiantes (o un array vacío si no hay)
            return response()->json([
                'subcourse_name' => $subcourse->name,
                'students' => $students
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener los estudiantes.',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function allStudentsByDocente()
    {
        try {
            // Buscar al docente
            $userId = Auth::id();
            // Obtener los subcursos que tiene asignados
            $subcourses = SubCourse::where('docente_id', $userId)->pluck('id');

            // Obtener los estudiantes inscritos en esos subcursos
            $students = Enrollment::whereIn('subcourse_id', $subcourses)
                ->with('student')
                ->get()
                ->pluck('student')
                ->unique() // Para evitar duplicados si un estudiante está en varios subcursos
                ->values(); // Reindexar el array

            return response()->json($students, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los estudiantes: ' . $e->getMessage()], 500);
        }
    }


    //Operaciones basicas de CRUD para los estudiantes
    public function store(Request $request)
    {
        try {
            // Validación de los datos
            $request->validate([
                'genre_id' => 'required|integer|exists:genres,id',
                'avatar_id' => 'nullable',
                'tutor_id' => 'nullable|integer|exists:users,id',
                'photo_portada' => 'nullable|string',
                'user' => 'required|string|unique:students,user',
                'name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'nullable|string|max:15',
                'email' => 'required|email|unique:students,email',
                'fecha_nacimiento' => 'required|date',
                'password' => 'required|string|min:6',
                'subcourse_id' => 'required|integer|exists:sub_course,id',
            ]);

            DB::beginTransaction(); // Iniciar transacción

            // Asignar el role_id por defecto a 3
            $role_id = 3;

            // Crear el estudiante
            $student = Student::create([
                'role_id' => $role_id,
                'genre_id' => $request->genre_id,
                'avatar_id' => $request->avatar_id,
                'tutor_id' => $request->tutor_id,
                'photo_portada' => $request->photo_portada,
                'user' => $request->user,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'password' => Hash::make($request->password),
            ]);

            // Guardar en enrollments
            Enrollment::create([
                'student_id' => $student->id,
                'subcourse_id' => $request->subcourse_id,
                'enrollment_date' => now(),
                'active' => true,
            ]);

            // Obtener el nombre del rol desde la tabla roles
            $role = Roles::find($role_id);
            if ($role) {
                RoleUserStudent::create([
                    'role_us_id' => $student->id,
                    'rol' => $role->rol,
                ]);
            }

            DB::commit(); // Confirmar transacción

            return response()->json([
                'message' => 'Estudiante creado exitosamente',
                'student' => $student
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack(); // Revertir cambios en caso de error

            return response()->json([
                'error' => 'Error al crear el estudiante',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Obtener un estudiante por ID.
     */
    public function show($id)
    {
        try {
            $student = Student::with('genre', 'enrollments')->find($id);

            if (!$student) {
                return response()->json(['message' => 'Estudiante no encontrado'], 404);
            }

            return response()->json($student, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al obtener el estudiante',
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un estudiante.
     */
    public function update(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            // Validar los datos
            $request->validate([
                'genre_id' => 'sometimes|integer|exists:genres,id',
                'avatar_id' => 'nullable|integer',
                'tutor_id' => 'nullable|integer|exists:users,id',
                'photo_portada' => 'nullable|string',
                'user' => 'sometimes|string|unique:students,user,' . $id,
                'name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'phone' => 'nullable|string|max:15',
                'email' => 'sometimes|email|unique:students,email,' . $id,
                'fecha_nacimiento' => 'sometimes|date',
                'password' => 'nullable|string|min:6',
                'active' => 'sometimes|boolean', // Permitir actualizar el estado
            ]);

            // Obtener solo los datos enviados
            $data = $request->only([
                'genre_id',
                'avatar_id',
                'tutor_id',
                'photo_portada',
                'user',
                'name',
                'last_name',
                'phone',
                'email',
                'fecha_nacimiento',
                'active'
            ]);

            // Si se envía una nueva contraseña, actualizarla
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Actualizar el estudiante
            $student->update($data);

            // Si se envió el estado 'active', actualizar la inscripción del estudiante
            if ($request->has('active')) {
                Enrollment::where('student_id', $student->id)->update(['active' => $request->active]);
            }

            return response()->json([
                'message' => 'Estudiante actualizado correctamente',
                'student' => $student
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error al actualizar el estudiante',
                'message' => $th->getMessage()
            ], 500);
        }
    }


    /**
     * Eliminar un estudiante.
     */
    public function destroy($id)
    {
        try {
            // Buscar el estudiante por su ID
            $student = Student::find($id);

            // Verificar si el estudiante existe
            if (!$student) {
                return response()->json(['message' => 'Estudiante no encontrado'], 404);
            }

            // Eliminar las inscripciones del estudiante
            $student->enrollments()->delete();

            // Eliminar el estudiante
            $student->delete();

            return response()->json(['message' => 'Estudiante y sus inscripciones eliminados correctamente'], 200);
        } catch (\Throwable $th) {
            // Capturar cualquier error
            return response()->json([
                'error' => 'Error al eliminar el estudiante',
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
