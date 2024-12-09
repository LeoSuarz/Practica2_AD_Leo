<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class HelloWorldController extends Controller
{
    /**
     * Lista todos los ficheros de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index(): JsonResponse
    {
        // Obtenemos la lista de archivos en el directorio de almacenamiento
        $files = Storage::files();

        // Retornamos la respuesta en formato JSON
        return response()->json([
            'mensaje' => 'Listado de ficheros',
            'contenido' => $files,
        ]);
    }


    public function store(Request $request): JsonResponse
{
    // Validar que los parámetros filename y content están presentes en la solicitud
    if (!$request->has('filename') || !$request->has('content')) {
        return response()->json([
            'mensaje' => 'Parámetros faltantes: filename y content son requeridos'
        ], 422); // HTTP 422 Unprocessable Entity
    }

    $filename = $request->input('filename');
    $content = $request->input('content');

    // Verificar si el archivo ya existe
    if (Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo ya existe'
        ], 409); // HTTP 409 Conflict
    }

    // Crear el archivo con el contenido proporcionado
    Storage::put($filename, $content);

    // Responder con éxito con código 200 y el mensaje esperado
    return response()->json([
        'mensaje' => 'Guardado con éxito'
    ], 200); // HTTP 200 OK
}


public function show(string $filename): JsonResponse
{
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'Archivo no encontrado'
        ], 404);
    }

    $content = Storage::get($filename);

    return response()->json([
        'mensaje' => 'Archivo leído con éxito',  // Mensaje ajustado
        'contenido' => $content
    ], 200);
}


public function update(Request $request, string $filename): JsonResponse
{
    if (!$request->has('content')) {
        return response()->json([
            'mensaje' => 'Parámetros faltantes: content es requerido'
        ], 422);
    }

    $content = $request->input('content');

    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe'  // Mensaje ajustado
        ], 404);
    }

    Storage::put($filename, $content);

    return response()->json([
        'mensaje' => 'Actualizado con éxito'  // Mensaje ajustado
    ], 200);
}


public function destroy(string $filename): JsonResponse
{
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe'  // Mensaje ajustado
        ], 404);
    }

    Storage::delete($filename);

    return response()->json([
        'mensaje' => 'Eliminado con éxito'  // Mensaje ajustado
    ], 200);
}


}

