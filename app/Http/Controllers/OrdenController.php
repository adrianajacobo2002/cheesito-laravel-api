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

    public function agregarPlatillos(Request $request, $orden_id)
    {
        $request->validate([
            'platillos' => 'required|array|min:1',
            'platillos.*.platillo_id' => 'required|exists:platillos,id_platillo',
            'platillos.*.cantidad' => 'required|integer|min:1',
        ]);

        $orden = Orden::where('id_orden', $orden_id)
            ->where('mesero_id', Auth::id())
            ->where('estado', 'activa')
            ->firstOrFail();

        $mensajes = [];
        $totalAgregado = 0;

        foreach ($request->platillos as $item) {
            $platillo = Platillo::with('inventario')->findOrFail($item['platillo_id']);
            $stock = $platillo->inventario->cantidad_disponible ?? 0;

            if ($stock < $item['cantidad']) {
                $mensajes[] = "Stock insuficiente para {$platillo->nombre}. Disponible: $stock";
                continue;
            }

            $platillo->inventario->decrement('cantidad_disponible', $item['cantidad']);

            $subtotal = $platillo->precio * $item['cantidad'];

            DetalleOrden::create([
                'orden_id' => $orden->id_orden,
                'platillo_id' => $platillo->id_platillo,
                'cantidad' => $item['cantidad'],
                'subtotal' => $subtotal,
                'estado' => 'pendiente'
            ]);

            $totalAgregado += $subtotal;
        }

        return response()->json([
            'message' => 'Platillos agregados a la orden.',
            'total_agregado' => $totalAgregado,
            'advertencias' => $mensajes
        ]);
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

    public function historialOrdenes()
    {
        $ordenes = Orden::with(['mesa', 'factura'])
            ->where('estado', 'pagada')
            ->orderByDesc('fecha')
            ->get();

        return response()->json($ordenes);
    }

    public function detalleHistorial($id)
    {
        $orden = Orden::with([
            'mesa',
            'mesero',
            'factura',
            'detalles.platillo'
        ])
        ->where('id_orden', $id)
        ->where('estado', 'pagada')
        ->firstOrFail();

        return response()->json($orden);
    }

    

}
