<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cargar el rol del usuario
        $userRole = $request->user()->role()->first()->rol;

        if (!in_array($userRole, $roles)) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta.'], 403);
        }

        return $next($request);
    }
}
