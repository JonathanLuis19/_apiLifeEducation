<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers(Request $request)
    {
        try {
            // Verifica si el encabezado de autorización está presente
            if (!$request->hasHeader('Authorization')) {
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            // Verifica si el usuario está autenticado
            if (!$request->user()) {
                return response()->json(['error' => 'Token inválido'], 401);
            }

            $users = User::with('genre')->where('rolee_id', 1)->get()->makeHidden(['genre_id']);
            return response()->json($users);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    public function getUserById($id)
    {
        try {
            if (!is_numeric($id) || $id <= 0) {
                return response()->json(['error' => 'ID de usuario no válido'], 400);
            }

            $user = User::with('genre')->find($id)->makeHidden('genre_id');
            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            return response()->json($user);
        } catch (\Illuminate\Database\QueryException $e) {

            return response()->json(['error' => 'Error en la consulta a la base de datos', 'detalle' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió un error inesperado', 'detalle' => $e->getMessage()], 500);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error interno del servidor', 'detalle' => $th->getMessage()], 500);
        }
    }


    public function createUser(UserRequest $request)
    {
        try {
            // Crea el usuario con todos los campos
            $user = User::create([
                'rolee_id' => 1,
                'genre_id' => $request->genre_id,
                'name' => $request->name,
                'user' => $request->user,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'photo_profile' => $request->photo_profile ?? null,
                'photo_portada' => $request->photo_portada ?? null,
                'phone' => $request->phone,
                'fecha_registro' => now(),
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Usuario creado exitosamente.',
                'user' => $user,
            ], 201);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'error' => 'Error al crear el usuario.',
                'message' => $ex->getMessage(),
                'code' => $ex->getCode(),
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'No se pudo crear el usuario.',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function updateUser(UpdateUserRequest $request, $id)
    {
        try {

            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $user->update([
                'genre_id' => $request->genre_id ?? $user->genre_id,
                'name' => $request->name ?? $user->name,
                'user' => $request->user ?? $user->user,
                'fecha_nacimiento' => $request->fecha_nacimiento ?? $user->fecha_nacimiento,
                'photo_profile' => $request->photo_profile ?? $user->photo_profile,
                'photo_portada' => $request->photo_portada ?? $user->photo_portada,
                'phone' => $request->phone ?? $user->phone,
                'email' => $request->email ?? $user->email,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
            ]);

            return response()->json(['message' => 'Usuario actualizado correctamente', 'user' => $user], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'No se pudo actualizar el usuario.',
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function deleteUser($id)
    {

        try {
            $user = User::findOrFail($id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            $user->delete();

            return response()->json(['error' => 'Usuario eliminado'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'No se puedo eliminar el usuario', 'message' => $th->getMessage()], 500);
        }
    }


    //teachers
    //lista de teachers
    public function getTeachers(Request $request)
    {
        try {
            // Verifica si el encabezado de autorización está presente
            if (!$request->hasHeader('Authorization')) {
                return response()->json(['error' => 'Token no proporcionado'], 401);
            }

            // Verifica si el usuario está autenticado
            if (!$request->user()) {
                return response()->json(['error' => 'Token inválido'], 401);
            }

            $users = User::with('genre')->where('rolee_id', 2)->get();
            return response()->json($users);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage()], 500);
        }
    }

    //registro de teachers
    public function registerTeacher(UserRequest $request)
    {
        try {
            $teacher = User::create([
                'rolee_id' => 2,
                'genre_id' => $request->genre_id,
                'name' => $request->name,
                'user' => $request->user,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'photo_profile' => $request->photo_profile ?? null, // Maneja el campo opcional
                'photo_portada' => $request->photo_portada ?? null, // Maneja el campo opcional
                'phone' => $request->phone,
                'fecha_registro' => now(),
                'email' => $request->email,
                'password' => Hash::make($request->password),

            ]);

            return response()->json([
                'message' => 'Usuario creado exitosamente.',
                'user' => $teacher
            ], 201);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'error' => 'Error al registrar el profesor.',
                'message' => $ex->getMessage(),
                'code' => $ex->getCode(),
            ], 400);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => 'No se puedo registraral profesor.',
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
