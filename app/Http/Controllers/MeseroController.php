<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mesero;


class MeseroController extends Controller
{
    public function index()
    {
        $meseros = Mesero::select('id_mesero', 'nombre', 'codigo')->get();

        return response()->json($meseros);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            $mesero = new Mesero();
            $mesero->nombre = $request->nombre;
            $mesero->save();

            // Generar código
            $año = now()->year;
            $iniciales = strtoupper(collect(explode(' ', $mesero->nombre))->map(fn($part) => Str::substr($part, 0, 1))->implode(''));
            $codigo = sprintf('%s-%s-%03d', $año, $iniciales, $mesero->id_mesero);

            $mesero->codigo = $codigo;
            $mesero->save();

            DB::commit();

            return response()->json(['message' => 'Mesero creado exitosamente', 'mesero' => $mesero], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear el mesero'], 500);
        }
    }

    public function show($id)
    {
        $mesero = Mesero::find($id);
        if (!$mesero) {
            return response()->json(['message' => 'Mesero no encontrado'], 404);
        }
        return response()->json($mesero);
    }

    public function update(Request $request, $id)
    {
        $mesero = Mesero::find($id);
        if (!$mesero) {
            return response()->json(['message' => 'Mesero no encontrado'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $mesero->nombre = $request->nombre;


        $año = now()->year;
        $iniciales = strtoupper(collect(explode(' ', $mesero->nombre))->map(fn($part) => Str::substr($part, 0, 1))->implode(''));
        $codigo = sprintf('%s-%s-%03d', $año, $iniciales, $mesero->id_mesero);

        $mesero->codigo = $codigo;
        $mesero->save();

        return response()->json(['message' => 'Mesero actualizado', 'mesero' => $mesero]);
    }

    public function destroy($id)
    {
        $mesero = Mesero::find($id);
        if (!$mesero) {
            return response()->json(['message' => 'Mesero no encontrado'], 404);
        }

        $mesero->delete();
        return response()->json(['message' => 'Mesero eliminado correctamente']);
    }

}
