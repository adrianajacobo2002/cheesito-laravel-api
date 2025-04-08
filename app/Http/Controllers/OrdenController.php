<?php

namespace App\Http\Controllers;

use App\Models\Orden;
use App\Models\Mesa;
use App\Models\Platillo;
use App\Models\Inventario;
use App\Models\DetalleOrden;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdenController extends Controller
{
    public function mesasAsignadas()
    {
        $mesas = Mesa::where('mesero_id', Auth::id())->with('ordenes')->get();
        return response()->json($mesas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id_mesa',
            'nombre_cliente' => 'required|string|max:100'
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);

        $ordenesActivas = Orden::where('mesa_id', $mesa->id_mesa)
            ->whereIn('estado', ['activa', 'pendiente', 'en_preparacion'])
            ->count();

        if ($mesa->estado === 'disponible' && $ordenesActivas === 0) {
            $mesa->update(['estado' => 'ocupada']);
        } elseif ($mesa->estado === 'ocupada') {
            if ($ordenesActivas >= $mesa->capacidad) {
                return response()->json([
                    'message' => 'La mesa ya alcanzó su límite de órdenes activas.'
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'No se puede crear la orden. La mesa no está disponible.'
            ], 400);
        }

        $orden = Orden::create([
            'mesa_id'        => $mesa->id_mesa,
            'mesero_id'      => Auth::id(),
            'estado'         => 'activa',
            'nombre_cliente' => $request->nombre_cliente,
            'fecha'          => now(),
        ]);

        return response()->json(['message' => 'Orden creada exitosamente', 'orden' => $orden], 201);
    }


    public function pagar($id)
    {
        $orden = Orden::where('id_orden', $id)
                      ->where('mesero_id', Auth::id())
                      ->firstOrFail();

        $orden->update(['estado' => 'pagada']);

        return response()->json(['message' => 'Orden pagada']);
    }

    public function ordenesMesero()
    {
        $ordenes = Orden::where('mesero_id', Auth::id())->with('mesa', 'detalles.platillo')->get();
        return response()->json($ordenes);
    }

    public function detalle($id)
    {
        $orden = Orden::with('mesa', 'detalles.platillo')
                      ->where('id_orden', $id)
                      ->where('mesero_id', Auth::id())
                      ->firstOrFail();

        return response()->json($orden);
    }
}
