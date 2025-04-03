<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\SubCourse;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\Traits\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class TTaskController extends Controller
{
    public function TasksByIdSubCourse($sub_course_id)
    {
        try {
            // Buscar el subcurso con sus tareas
            $subCourse = SubCourse::with('tasks')->find($sub_course_id);

            // Validar si el subcurso existe
            if (!$subCourse) {
                return response()->json(['message' => 'Subcurso no encontrado'], 404);
            }

            // Verificar si tiene tareas
            if ($subCourse->tasks->isEmpty()) {
                return response()->json(['message' => 'El subcurso no tiene tareas registradas'], 404);
            }

            // Retornar el subcurso con sus tareas en una estructura clara
            return response()->json([
                'sub_course' => [
                    'id' => $subCourse->id,
                    'name' => $subCourse->name,
                    'description' => $subCourse->description,
                    'tasks' => $subCourse->tasks
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

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'sub_course_id' => 'required|exists:sub_course,id',
                'name' => 'required|string',
                'instrucciones' => 'required|string|max:255',
                'texto' => 'nullable|string',
                'intentos' => 'required|integer|min:1',
                'fecha' => 'required|date',
                'hora' => 'required|date_format:H:i',
                'url_video' => 'nullable|url',
                'video_seconds_start' => 'nullable|integer|min:0',
                'video_seconds_end' => 'nullable|integer|min:0',
                'audioFile' => 'nullable|file|mimes:mp3,wav,ogg|max:10240',
                'videoFile' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
                'imagenFile' => 'nullable|file|mimes:jpg,jpeg,png,bmp|max:10240',
                'recursoFile' => 'nullable|file|max:10240',
                'questions' => 'nullable|array',
            ]);

            $validatedData['fecha_limite'] = Carbon::createFromFormat('Y-m-d H:i', "{$validatedData['fecha']} {$validatedData['hora']}");

            $audioFilePath = $request->file('audioFile') ? $request->file('audioFile')->store('audios', 'public') : null;
            $videoFilePath = $request->file('videoFile') ? $request->file('videoFile')->store('videos', 'public') : null;
            $imageFilePath = $request->file('imageFile_url') ? $request->file('imagenFile')->store('images', 'public') : null;
            $recursoFilePath = $request->file('recursoFile') ? $request->file('recursoFile')->store('resources', 'public') : null;

            $task = Task::create([
                'sub_course_id' => $validatedData['sub_course_id'],
                'name' => $validatedData['name'],
                'instrucciones' => $validatedData['instrucciones'],
                'texto' => $validatedData['texto'],
                'fecha_limite' => $validatedData['fecha_limite'],
                'intentos' => $validatedData['intentos'],
                'url_video' => $validatedData['url_video'],
                'video_seconds_start' => $validatedData['video_seconds_start'],
                'video_seconds_end' => $validatedData['video_seconds_end'],
                'audioFile_url' => $audioFilePath,
                'videoFile_url' => $videoFilePath,
                'imageFile_url' => $imageFilePath,
                'recursoFile_url' => $recursoFilePath,
            ]);

            $questions = [];
            if (!empty($request->questions)) {
                foreach ($request->questions as $index => $q) {
                    $request->validate([
                        "questions.$index.texto_pregunta" => 'required|string|max:255',
                    ]);

                    $tipoPregunta = $q['tipo_pregunta'] ?? 'multiple_choice_single';

                    $question = Question::create([
                        'task_id' => $task->id,
                        'texto_pregunta' => $q['texto_pregunta'],
                        'tipo_pregunta' => $tipoPregunta,
                    ]);

                    $options = [];
                    if (isset($q['options'])) {
                        foreach ($q['options'] as $optionIndex => $option) {
                            $isCorrect = isset($q['correct']) && in_array($optionIndex + 1, $q['correct']);
                            $options[] = $question->options()->create([
                                'texto_respuesta' => $option,
                                'is_correct' => $isCorrect,
                                'orden_respuesta' => $optionIndex + 1,
                            ]);
                        }
                    }

                    if (isset($q['respuesta_esperada']) && $tipoPregunta === 'text_input') {
                        $options[] = $question->options()->create([
                            'texto_respuesta' => $q['respuesta_esperada'],
                            'is_correct' => true,
                            'orden_respuesta' => 0,
                        ]);
                    }

                    $questions[] = [
                        'id' => $question->id,
                        'texto_pregunta' => $question->texto_pregunta,
                        'tipo_pregunta' => $question->tipo_pregunta,
                        'opciones' => $options
                    ];
                }
            }

            return response()->json([
                'message' => 'Tarea creada con éxito',
                'task' => [
                    'id' => $task->id,
                    'sub_course_id' => $task->sub_course_id,
                    'name' => $task->name,
                    'instrucciones' => $task->instrucciones,
                    'texto' => $task->texto,
                    'fecha_limite' => $task->fecha_limite,
                    'intentos' => $task->intentos,
                    'url_video' => $task->url_video,
                    'video_seconds_start' => $task->video_seconds_start,
                    'video_seconds_end' => $task->video_seconds_end,
                    'audioFile_url' => $task->audioFile_url,
                    'videoFile_url' => $task->videoFile_url,
                    'imageFile_url' => $task->imagenFile_url,
                    'recursoFile_url' => $task->recursoFile_url,
                    'questions' => $questions
                ]
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Error de validación', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error en la creación de la tarea', 'message' => $e->getMessage()], 500);
        }
    }


    public function show(string $id)
    {
        try {
            // Buscar la tarea por su ID, incluyendo las preguntas y sus opciones
            $tarea = Task::with(['subcourse', 'questions.options'])->findOrFail($id);

            return response()->json([
                'message' => 'Tarea encontrada',
                'tarea' => $tarea
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Tarea no encontrada',
                'message' => $e->getMessage()
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al obtener la tarea',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            // Buscar la tarea
            $tarea = Task::findOrFail($id);

            // Validar solo los campos que se envían en la solicitud
            $validatedData = $request->validate([
                'sub_course_id' => 'sometimes|exists:sub_courses,id',
                'name' => 'sometimes|string',
                'instrucciones' => 'sometimes|string|max:255',
                'texto' => 'sometimes|nullable|string',
                'intentos' => 'sometimes|integer|min:1',
                'fecha_limite' => 'sometimes|date',
                'url_video' => 'sometimes|nullable|url',
                'video_seconds_start' => 'sometimes|nullable|integer|min:0',
                'video_seconds_end' => 'sometimes|nullable|integer|min:0',
                'audioFile_url' => 'sometimes|nullable|file|mimes:mp3,wav,ogg|max:10240',
                'videoFile_url' => 'sometimes|nullable|file|mimes:mp4,mov,avi|max:20480',
                'imagenFile_url' => 'sometimes|nullable|file|mimes:jpg,jpeg,png,bmp|max:10240',
                'recursoFile_url' => 'sometimes|nullable|file|max:10240',
                'questions' => 'sometimes|array',
            ]);

            // Manejo de archivos solo si se envían
            if ($request->hasFile('audioFile_url')) {
                $validatedData['audioFile_url'] = $request->file('audioFile_url')->store('audios', 'public');
            }
            if ($request->hasFile('videoFile_url')) {
                $validatedData['videoFile_url'] = $request->file('videoFile_url')->store('videos', 'public');
            }
            if ($request->hasFile('imagenFile_url')) {
                $validatedData['imagenFile_url'] = $request->file('imagenFile_url')->store('images', 'public');
            }
            if ($request->hasFile('recursoFile_url')) {
                $validatedData['recursoFile_url'] = $request->file('recursoFile_url')->store('resources', 'public');
            }

            // Si se envía la fecha, formatearla correctamente
            if ($request->has('fecha_limite')) {
                $validatedData['fecha_limite'] = Carbon::createFromFormat('Y-m-d H:i', $validatedData['fecha_limite']);
            }

            // Actualizar solo los campos proporcionados
            $tarea->update($validatedData);

            // Si se envían preguntas, actualizar
            if ($request->has('questions')) {
                $tarea->questions()->delete();

                foreach ($request->questions as $q) {
                    $question = $tarea->questions()->create([
                        'texto_pregunta' => $q['texto_pregunta'],
                        'tipo_pregunta' => $q['tipo_pregunta'] ?? 'multiple_choice_single',
                    ]);

                    if (in_array($q['tipo_pregunta'], ['multiple_choice_single', 'multiple_choice_multiple']) && isset($q['options'])) {
                        foreach ($q['options'] as $optionIndex => $option) {
                            $isCorrect = in_array($optionIndex + 1, $q['correct'] ?? []);
                            $question->options()->create([
                                'texto_respuesta' => $option,
                                'is_correct' => $isCorrect,
                                'orden_respuesta' => $optionIndex + 1,
                            ]);
                        }
                    } elseif ($q['tipo_pregunta'] === 'text_input') {
                        $question->options()->create([
                            'texto_respuesta' => $q['respuesta_esperada'],
                            'is_correct' => true,
                            'orden_respuesta' => 0,
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada con éxito',
                'data' => $tarea->load('questions.options')
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la tarea',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        try {
            $task = Task::findOrFail($id);

            // Eliminar las preguntas asociadas a la tarea, incluidas las opciones
            $task->questions()->each(function ($question) {
                $question->options()->delete();  // Eliminar opciones de cada pregunta
                $question->delete();  // Eliminar la pregunta
            });

            // Eliminar los archivos asociados si existen
            if ($task->audioFile_url) {
                Storage::disk('public')->delete($task->audioFile_url);
            }
            if ($task->videoFile_url) {
                Storage::disk('public')->delete($task->videoFile_url);
            }
            if ($task->imagen_url) {
                Storage::disk('public')->delete($task->imagen_url);
            }
            if ($task->recursoFile_url) {
                Storage::disk('public')->delete($task->recursoFile_url);
            }

            // Eliminar la tarea
            $task->delete();

            // Respuesta JSON si se utiliza en API
            return response()->json([
                'success' => true,
                'message' => 'La tarea y todos sus elementos han sido eliminados correctamente.'
            ], 200);
        } catch (\Throwable $th) {
            // Respuesta JSON en caso de error
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la tarea: ' . $th->getMessage()
            ], 500);
        }
    }



    public function destroyQuestion($questionId)
    {
        try {
            $question = Question::findOrFail($questionId);

            // Elimina todas las opciones asociadas
            $question->options()->delete();

            // Elimina la pregunta
            $question->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pregunta eliminada correctamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la pregunta',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroyAnswer($optionId)
    {
        try {
            $option = Answer::findOrFail($optionId);
            $option->delete();

            return response()->json([
                'success' => true,
                'message' => 'Opción eliminada correctamente',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la opción',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
