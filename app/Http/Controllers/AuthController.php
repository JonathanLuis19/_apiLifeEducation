<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas.'], 401);
        }

        if ($user->rolee_id === 1) {
            // Administrador
            $token = $user->createToken('admin-token')->plainTextToken;
            return response()->json(['token' => $token, 'role' => 'admin']);
        } elseif ($user->rolee_id === 2) {
            // Docente
            $token = $user->createToken('teacher-token')->plainTextToken;
            return response()->json(['token' => $token, 'role' => 'teacher']);
        }
        return response()->json(['message' => 'Rol no reconocido.'], 403);
    }



    public function logout(Request $request)
    {
        // Revocar el token del usuario autenticado
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada exitosamente.']);
    }

    public function getUser(Request $request)
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

            $user = $request->user()->load('genre');

            // Devuelve la información del usuario autenticado
            return response()->json($user);
        } catch (\Throwable $th) {
            // Manejo de excepciones
            return response()->json(['error' => 'Error al obtener el usuario: ' . $th->getMessage()], 500);
        }
    }
}
