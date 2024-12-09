<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class JsonController extends Controller
{
    private function isValidJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function index()
    {
        $files = Storage::files('app');
        $validJsonFiles = [];

        foreach ($files as $file) {
            $content = Storage::get($file);
            if ($this->isValidJson($content)) {
                $validJsonFiles[] = basename($file);
            }
        }

        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => $validJsonFiles,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'content' => 'required',
        ]);

        $filename = 'app/' . $request->filename;
        $content = $request->content;

        if (Storage::exists($filename)) {
            return response()->json(['mensaje' => 'El fichero ya existe'], 409);
        }

        if (!$this->isValidJson($content)) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        Storage::put($filename, $content);

        return response()->json(['mensaje' => 'Fichero guardado exitosamente'], 200);
    }

    public function show(string $id)
    {
        $filename = 'app/' . $id;

        if (!Storage::exists($filename)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        $content = Storage::get($filename);

        return response()->json([
            'mensaje' => 'Operación exitosa',
            'contenido' => json_decode($content, true),
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $filename = 'app/' . $id;

        if (!Storage::exists($filename)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        $request->validate([
            'content' => 'required',
        ]);

        $content = $request->content;

        if (!$this->isValidJson($content)) {
            return response()->json(['mensaje' => 'Contenido no es un JSON válido'], 415);
        }

        Storage::put($filename, $content);

        return response()->json(['mensaje' => 'Fichero actualizado exitosamente'], 200);
    }

    public function destroy(string $id)
    {
        $filename = 'app/' . $id;

        if (!Storage::exists($filename)) {
            return response()->json(['mensaje' => 'El fichero no existe'], 404);
        }

        Storage::delete($filename);

        return response()->json(['mensaje' => 'Fichero eliminado exitosamente'], 200);
    }
}
