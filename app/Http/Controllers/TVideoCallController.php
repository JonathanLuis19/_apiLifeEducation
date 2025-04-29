<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Participant;
use App\Models\RoleUserStudent;
use App\Models\Student;
use App\Models\SubCourse;
use App\Models\User;
use Illuminate\Http\Request;

class TVideoCallController extends Controller
{
    public function indexVideoCall($subcourse_id, $docente_id)
    {
        try {
            $meeting = Appointment::where('sub_course_id', $subcourse_id)
                ->where('user_id', $docente_id)
                ->with('docente')
                ->with('participants')
                ->get();

            if (!$meeting) {
                return response()->json(['message' => 'Reunión no encontrada'], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Lista de video llamadas',
                'data' => $meeting,
            ]);
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

    public function store(Request $request)
    {
        try {
            // Validación de los datos de la solicitud
            $request->validate([
                'sub_course_id' => 'required|integer|exists:sub_course,id',
                'user_id'       => 'required|integer|exists:users,id', // docente
                'title'         => 'required|string|max:255',
                'access_code'   => 'nullable|string|max:100',
                'start_time'    => 'required|date',
                'duration'      => 'required|integer|min:1',
                'description'   => 'nullable|string',
            ]);

            // Obtener el subcurso con los estudiantes relacionados
            $subCourse = SubCourse::with('enrollments.student')->find($request->sub_course_id);

            // Filtrar solo estudiantes válidos
            $students = $subCourse->enrollments
                ->filter(fn($enrollment) => $enrollment->student !== null)
                ->pluck('student');

            if ($students->isEmpty()) {
                return response()->json([
                    'error' => 'No se puede crear la reunión',
                    'message' => 'No hay estudiantes inscritos en este subcurso.'
                ], 400);
            }

            // Crear la reunión
            $appointment = Appointment::create([
                'sub_course_id' => $request->sub_course_id,
                'user_id'       => $request->user_id,
                'title'         => $request->title,
                'access_code'   => $request->access_code,
                'start_time'    => $request->start_time,
                'duration'      => $request->duration,
                'description'   => $request->description,
            ]);

            // Añadir estudiantes como participantes
            foreach ($students as $student) {
                $studentRole = RoleUserStudent::where('role_us_id', $student->id)
                    ->where('rol', 'student')
                    ->first();

                if ($studentRole) {
                    Participant::create([
                        'appointment_id' => $appointment->id,
                        'urole_id'       => $studentRole->id,
                        'is_connected'   => false
                    ]);
                }
            }

            // Añadir al docente como participante
            $teacherRole = RoleUserStudent::where('role_us_id', $request->user_id)
                ->where('rol', 'teacher')
                ->first();

            if ($teacherRole) {
                Participant::create([
                    'appointment_id' => $appointment->id,
                    'urole_id'       => $teacherRole->id,
                    'is_connected'   => false
                ]);
            }

            // Opcional: cargar relación con participantes
            $appointment->load(['participants.studentsTeacher']);

            return response()->json([
                'message' => 'Reunión y participantes creados exitosamente',
                'reunion' => $appointment
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'error' => 'Error en la base de datos',
                'message' => $e->getMessage()
            ], 500);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'Error inesperado',
                'message' => $th->getMessage()
            ], 500);
        }
    }




    public function show($id)
    {
        try {
            // 1. Cargar reunión con sus participantes y la relación urole (studentsTeacher)
            $appointment = Appointment::with([
                'participants.studentsTeacher',
            ])->findOrFail($id);

            // 2. Mapear autores (estudiante o docente según rol)
            $appointment->participants->transform(function ($participant) {
                $urole = $participant->studentsTeacher;

                if (!$urole) {
                    $participant->author = null;
                    return $participant;
                }

                // Buscar al autor según el rol
                if ($urole->rol === 'student') {
                    $author = Student::find($urole->role_us_id);
                } elseif ($urole->rol === 'teacher') {
                    $author = User::find($urole->role_us_id);
                } else {
                    $author = null;
                }

                // Adjuntar el autor como propiedad adicional
                $participant->author = $author;

                return $participant;
            });

            return response()->json([
                'status' => true,
                'data' => $appointment
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Reunión no encontrada'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener la reunión',
                'error' => $th->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            $request->validate([
                'title'       => 'sometimes|required|string|max:255',
                'access_code' => 'nullable|string|max:100',
                'start_time'  => 'sometimes|required|date',
                'duration'    => 'sometimes|required|integer|min:1',
                'description' => 'nullable|string',
            ]);

            $appointment->update($request->only([
                'title',
                'access_code',
                'start_time',
                'duration',
                'description',
            ]));

            return response()->json([
                'status' => true,
                'message' => 'Reunión actualizada exitosamente',
                'data' => $appointment
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Reunión no encontrada'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar la reunión',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);

            // Eliminar los participantes relacionados
            $appointment->participants()->delete();

            // Eliminar la reunión
            $appointment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Reunión y participantes eliminados correctamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Reunión no encontrada'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar la reunión',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    //Video llamada, ingresos, participantes

    public function stateParticipants($id)
    {
        try {
            $participants = Participant::where('appointment_id', $id)
                ->with('studentsTeacher')
                ->get();

            return response()->json([
                'status' => true,
                'data' => $participants
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Reunión no encontrada'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al obtener los participantes',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function updateParticipant(Request $request, $id)
    {
        try {
            $participant = Participant::findOrFail($id);

            $request->validate([
                'is_connected' => 'required|boolean',
            ]);

            $participant->update($request->only(['is_connected']));

            return response()->json([
                'status' => true,
                'message' => 'Estado de conexión actualizado exitosamente',
                'data' => $participant
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Participante no encontrado'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el estado del participante',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function destroyParticipant($id)
    {
        try {
            $participant = Participant::findOrFail($id);

            $participant->delete();

            return response()->json([
                'status' => true,
                'message' => 'Participante eliminado correctamente'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Participante no encontrado'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al eliminar el participante',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function setActivationDesactivationParticipants($role_us_id, $rol)
    {
        try {
            $identifidor = RoleUserStudent::where('role_us_id', $role_us_id)
                ->where('rol', $rol)
                ->first();

            if (!$identifidor) {
                return response()->json([
                    'status' => false,
                    'message' => 'Participante no encontrado (role_us_id y rol inválidos)'
                ], 404);
            }

            $participant = Participant::where('urole_id', $identifidor->id)->first();

            if (!$participant) {
                return response()->json([
                    'status' => false,
                    'message' => 'Participante no encontrado'
                ], 404);
            }

            // Invertir el estado actual
            $participant->is_connected = !$participant->is_connected;
            $participant->save();

            return response()->json([
                'status' => true,
                'message' => 'Estado de conexión actualizado exitosamente',
                'data' => $participant
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Error al actualizar el estado del participante',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
