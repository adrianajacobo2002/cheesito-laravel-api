<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Platillo; 
use App\Models\Inventario;

class PlatilloController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string',
            'precio'  => 'required|numeric',
            'tipo'    => 'required|in:comida,bebida',
            'imagen'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('uploads', 'public');
            $imagenUrl = asset('storage/' . $path);
        }

        $platillo = Platillo::create([
            'nombre'     => $request->nombre,
            'precio'     => $request->precio,
            'tipo'       => $request->tipo,
            'imagen_url' => $imagenUrl,
        ]);

        Inventario::create([
            'platillo_id' => $platillo->id_platillo,
            'cantidad_disponible' => 0,
        ]);

        return response()->json([
            'message'  => 'Platillo creado exitosamente',
            'platillo' => $platillo
        ], 201);
    }
}
