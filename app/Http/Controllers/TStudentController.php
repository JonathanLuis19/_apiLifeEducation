<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TStudentController extends Controller
{

    /**
     * Lista de todos los estudiantes que tiene el docente.
     *
     */
    public function allStudentByDocenteId($docente_id)
    {
        try {
            $docente = User::find($docente_id);
            $students = $docente->students()->with('subcourses')->get();
            return response()->json($students);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los estudiantes: ' . $e->getMessage()], 500);
        }
    }
}
