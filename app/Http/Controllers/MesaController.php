<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mesa;
use App\Models\Orden;

use Carbon\Carbon;



class MesaController extends Controller
{
    public function index()
    {
        $mesas = Mesa::with(['ordenes' => function ($query) {
            $query->whereIn('estado', ['por pagar']);
        }])->get();

        $mesas = $mesas->map(function ($mesa) {
            return [
                'id_mesa' => $mesa->id_mesa,
                'num_mesa' => $mesa->num_mesa,
                'capacidad' => $mesa->capacidad,
                'estado' => $mesa->estado,
                'ordenes_activas' => $mesa->ordenes->map(function ($orden) {
                    return [
                        'id_orden' => $orden->id_orden,
                        'nombre_cliente' => $orden->nombre_cliente,
                        'estado' => $orden->estado,
                    ];
                }),
            ];
        });

        return response()->json($mesas);
    }

    public function show($id)
    {
        $mesa = Mesa::with(['ordenes' => function ($query) {
            $query->whereIn('estado', ['por pagar'])->with('detalles.platillo');
        }])->find($id);

        if (!$mesa) {
            return response()->json(['message' => 'Mesa no encontrada.'], 404);
        }

        if ($mesa->ordenes->isEmpty()) {
            return response()->json([
                'id_mesa' => $mesa->id_mesa,
                'num_mesa' => $mesa->num_mesa,
                'capacidad' => $mesa->capacidad,
                'estado' => $mesa->estado,
                'ordenes' => [],
                'message' => 'La mesa no tiene órdenes activas.'
            ]);
        }

        $ordenes = $mesa->ordenes->map(function ($orden) {
            $total = $orden->detalles->sum('subtotal');

            return [
                'id_orden' => $orden->id_orden,
                'nombre_cliente' => $orden->nombre_cliente,
                'estado' => $orden->estado,
                'cantidad_productos' => $orden->detalles->sum('cantidad'),
                'total' => number_format($total, 2)
            ];
        });

        return response()->json([
            'id_mesa' => $mesa->id_mesa,
            'num_mesa' => $mesa->num_mesa,
            'capacidad' => $mesa->capacidad,
            'estado' => $mesa->estado,
            'ordenes' => $ordenes
        ]);
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

        if ($mesa->estado !== 'disponible' || Orden::where('mesa_id', $id)->where('estado', 'por pagar')->exists()) {
            return response()->json(['message' => 'La mesa está ocupada o tiene órdenes activas.'], 400);
        }

        $request->validate([
            'num_mesa'    => 'sometimes|integer|unique:mesas,num_mesa,' . $id,
            'capacidad' => 'sometimes|integer|min:1',
        ]);

        $mesa->update($request->only('num_mesa', 'capacidad'));

        return response()->json($mesa);
    }

    public function destroy($id)
    {
        $mesa = Mesa::findOrFail($id);

        if ($mesa->estado !== 'disponible' || Orden::where('mesa_id', $id)->where('estado', 'por pagar')->exists()) {
            return response()->json(['message' => 'No se puede eliminar una mesa ocupada o con órdenes activas.'], 400);
        }

        $mesa->delete();

        return response()->json(['message' => 'Mesa eliminada correctamente']);
    }

    public function mesasConOrdenes()
    {
        $mesas = Mesa::with(['ordenes' => function ($query) {
            $query->where('estado', 'por pagar');
        }])->get();

        return response()->json($mesas);
    }

    public function allInfo()
    {
        $mesas = Mesa::with(['ordenes.detalles.platillo'])->get();

        $resultado = $mesas->map(function ($mesa) {
            return [
                'id_mesa' => $mesa->id_mesa,
                'num_mesa' => $mesa->num_mesa,
                'capacidad' => $mesa->capacidad,
                'estado' => $mesa->estado,
                'ordenes' => $mesa->ordenes->map(function ($orden) {
                    return [
                        'id_orden' => $orden->id_orden,
                        'estado' => $orden->estado,
                        'fecha' => Carbon::parse($orden->fecha)->format('Y-m-d H:i:s'),
                        'nombre_cliente' => $orden->nombre_cliente,
                        'detalles' => $orden->detalles->map(function ($detalle) {
                            return [
                                'platillo' => $detalle->platillo->nombre,
                                'precio' => number_format($detalle->platillo->precio, 2),
                                'cantidad' => $detalle->cantidad,
                                'subtotal' => number_format($detalle->subtotal, 2),
                                'estado' => $detalle->estado,
                            ];
                        }),
                    ];
                }),
            ];
        });

        return response()->json($resultado);
    }

}
