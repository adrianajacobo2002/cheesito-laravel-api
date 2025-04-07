<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Inventario;
use App\Models\Platillo;

class InventarioController extends Controller
{
    public function index()
    {
        $inventario = Inventario::with('platillo')->get();
        return response()->json($inventario);
    }

    
    public function agregarStock(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);

        $inventario = Inventario::findOrFail($id);
        $inventario->cantidad_disponible += $request->cantidad;
        $inventario->save();

        return response()->json([
            'message' => 'Stock actualizado correctamente',
            'inventario' => $inventario->load('platillo')
        ]);
    }

    
    public function agotados()
    {
        $agotados = Inventario::with('platillo')->where('cantidad_disponible', 0)->get();
        return response()->json($agotados);
    }
}
