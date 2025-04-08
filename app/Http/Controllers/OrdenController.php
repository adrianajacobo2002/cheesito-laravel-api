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

    public function store(Request $request)
    {
        $request->validate([
            'mesa_id' => 'required|exists:mesas,id_mesa',
            'mesero_id' => 'required|exists:meseros,id_mesero',
            'nombre_cliente' => 'required|string|max:100'
        ]);

        $mesa = Mesa::findOrFail($request->mesa_id);

        $ordenesActivas = Orden::where('mesa_id', $mesa->id_mesa)
            ->whereIn('estado', ['por pagar'])
            ->count();


        if ($mesa->estado === 'disponible' && $ordenesActivas === 0) {
            $mesa->update(['estado' => 'ocupado']);
        } elseif ($mesa->estado === 'ocupado') {
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
            'mesero_id'      => $request->mesero_id, 
            'estado'         => 'por pagar',
            'nombre_cliente' => $request->nombre_cliente,
            'fecha'          => now(),
        ]);

        return response()->json([
            'message' => 'Orden creada exitosamente',
            'orden' => $orden
        ], 201);
    }


    public function agregarPlatillos(Request $request, $orden_id)
    {
        $request->validate([
            'platillos' => 'required|array|min:1',
            'platillos.*.platillo_id' => 'required|exists:platillos,id_platillo',
            'platillos.*.cantidad' => 'required|integer|min:1',
        ]);

        $orden = Orden::where('id_orden', $orden_id)
            ->where('estado', 'por pagar')
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
                'estado' => 'en preparación'
            ]);

            $totalAgregado += $subtotal;
        }

        return response()->json([
            'message' => 'Platillos agregados a la orden.',
            'total_agregado' => $totalAgregado,
            'advertencias' => $mensajes
        ]);
    }

    public function detalleConTotales($id)
    {
        $orden = Orden::with('detalles.platillo')
            ->where('id_orden', $id)
            ->firstOrFail();

        $detalles = $orden->detalles->map(function ($detalle) {
            return [
                'nombre' => $detalle->platillo->nombre,
                'precio' => number_format($detalle->platillo->precio, 2),
                'imagen_url' => $detalle->platillo->imagen_url,
                'cantidad' => $detalle->cantidad,
                'estado' => $detalle->estado,
                'subtotal' => number_format($detalle->subtotal, 2),
            ];
        });

        $subtotal = $orden->detalles->sum('subtotal');
        $propina = round($subtotal * 0.10, 2);
        $total = $subtotal + $propina;

        return response()->json([
            'id_orden' => $orden->id_orden,
            'cliente' => $orden->nombre_cliente,
            'fecha' => $orden->fecha->format('d/m/Y'),
            'estado' => $orden->estado,
            'detalles' => $detalles,
            'pago' => [
                'subtotal' => number_format($subtotal, 2),
                'propina' => number_format($propina, 2),
                'total' => number_format($total, 2),
            ]
        ]);
    }

    public function historialOrdenes()
    {
        $ordenes = Orden::with(['mesa', 'factura'])
            ->where('estado', 'cancelado')
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
        ->where('estado', 'cancelado')
        ->firstOrFail();

        return response()->json($orden);
    }

    

}
