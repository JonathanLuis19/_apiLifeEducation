<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Post;
use App\Models\RoleUserStudent;
use App\Models\Student;
use App\Models\SubCourse;
use App\Models\User;
use Illuminate\Http\Request;

class TPostController extends Controller
{
    /**
     * Lectura de todos los posts, tanto de estudiantes y docentes, dentro de un curso
     */
    public function indexPosts($curso_id)
    {
        // 1. Obtener SubCursos de ese curso
        $subcourses = SubCourse::where('course_id', $curso_id)->get();
        $subcourse_ids = $subcourses->pluck('id');

        // 2. Obtener docente(s) asignado(s) a esos subcursos
        $docente_ids = $subcourses->pluck('docente_id')->unique();

        // 3. Obtener estudiantes inscritos en los subcursos de ese curso
        $enrollments = Enrollment::whereIn('subcourse_id', $subcourse_ids)->get();
        $student_ids = $enrollments->pluck('student_id')->unique();

        // 4. Obtener RoleUserStudent para los estudiantes y docentes filtrados
        $roleUsers = RoleUserStudent::with(['posts' => function ($query) use ($curso_id) {
            $query->where('curso_id', $curso_id)->orderBy('created_at', 'desc');
        }])
            ->where(function ($query) use ($student_ids, $docente_ids) {
                $query->whereIn('role_us_id', $student_ids)
                    ->where('rol', 'student')
                    ->orWhere(function ($q) use ($docente_ids) {
                        $q->whereIn('role_us_id', $docente_ids)
                            ->where('rol', 'teacher');
                    });
            })
            ->get();

        // 5. Mapear resultados y evitar duplicados
        $result = $roleUsers->unique(function ($urole) {
            return $urole->rol . '-' . $urole->role_us_id; // evita duplicados
        })->map(function ($urole) {
            if ($urole->rol === 'student') {
                $autor = Student::find($urole->role_us_id);
            } else {
                $autor = User::find($urole->role_us_id);
            }

            return [
                'author' => $autor,
                'author_id' => $urole->id,
                'author_role' => $urole->rol,
                'posts' => $urole->posts->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'text' => $post->text,
                        'img' => $post->img,
                        'video' => $post->video,
                        'created_at' => $post->created_at,
                    ];
                }),
            ];
        });

        return response()->json($result);
    }

    /**
     * Almacena un nuevo post
     */
    public function store(Request $request)
    {
        $request->validate([
            'urole_id' => 'required|exists:role_users_students,id',
            'curso_id' => 'required|exists:courses,id',
            'text' => 'nullable|string',
            'img' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'video' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ]);

        // Verifica que al menos haya texto, imagen o video
        if (!$request->text && !$request->hasFile('img') && !$request->hasFile('video')) {
            return response()->json(['error' => 'Debe enviar al menos texto, imagen o video.'], 422);
        }

        $data = [
            'urole_id' => $request->urole_id,
            'curso_id' => $request->curso_id,
            'text' => $request->text,
            'img' => null,
            'video' => null,
        ];

        if ($request->hasFile('img')) {
            $data['img'] = $request->file('img')->store('posts', 'public');
        }

        if ($request->hasFile('video')) {
            $data['video'] = $request->file('video')->store('posts', 'public');
        }

        $post = Post::create($data);

        return response()->json([
            'message' => 'Post guardado correctamente',
            'post' => $post,
        ]);
    }
}
