<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckStudentRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $student = auth('student')->user(); // Esto es el modelo Student

        if (!$student) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $studentRole = $student->role?->rol; // Usando el operador null-safe "?"


        if (!in_array($studentRole, $roles)) {
            return response()->json(['message' => 'No tienes permiso.'], 403);
        }

        return $next($request);
    }
}
