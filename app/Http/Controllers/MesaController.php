<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mesa;
use App\Models\Orden;


class MesaController extends Controller
{
    public function index()
    {
        return response()->json(Mesa::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'num_mesa' => 'required|integer|unique:mesas,num_mesa',
            'capacidad' => 'required|integer|min:1',
        ]);

        $mesa = Mesa::create([
            'num_mesa' => $request->num_mesa,
            'capacidad' => $request->capacidad,
            'estado' => 'disponible',
        ]);

        return response()->json($mesa, 201);
    }


    public function update(Request $request, $id)
    {
        $mesa = Mesa::findOrFail($id);

        if ($mesa->estado !== 'disponible' || Orden::where('mesa_id', $id)->whereIn('estado', ['pendiente', 'en_preparacion'])->exists()) {
            return response()->json(['message' => 'La mesa está ocupada o tiene órdenes activas.'], 400);
        }

        $request->validate([
            'numero'    => 'sometimes|integer|unique:mesas,numero,' . $id,
            'capacidad' => 'sometimes|integer|min:1',
        ]);

        $mesa->update($request->only('numero', 'capacidad'));

        return response()->json($mesa);
    }

    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);

        if ($mesa->estado !== 'disponible' || Orden::where('mesa_id', $id)->whereIn('estado', ['pendiente', 'en_preparacion'])->exists()) {
            return response()->json(['message' => 'No se puede eliminar una mesa ocupada o con órdenes activas.'], 400);
        }

        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada correctamente']);
    }

    public function mesasConOrdenes()
    {
        $mesas = Mesa::with(['ordenes' => function ($query) {
            $query->whereIn('estado', ['pendiente', 'en_preparacion']);
        }])->get();

        return response()->json($mesas);
    }

}
