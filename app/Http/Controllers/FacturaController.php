<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Orden;
use App\Models\Factura;

class FacturaController extends Controller
{
    public function pagarOrden($orden_id)
    {
        
        $orden = Orden::with('detalles')->where('id_orden', $orden_id)->firstOrFail();

        if ($orden->estado !== 'por pagar') {
            return response()->json(['message' => 'La orden ya fue cancelada.'], 400);
        }

        $subtotal = $orden->detalles->sum('subtotal');

        $propina = round($subtotal * 0.10, 2);
        $total = $subtotal + $propina;

        $factura = Factura::create([
            'orden_id' => $orden->id_orden,
            'subtotal' => $subtotal,
            'propina' => $propina,
            'total' => $total,
            'fecha' => now(),
        ]);

        $orden->update(['estado' => 'cancelado']);

        return response()->json([
            'message' => 'Factura creada exitosamente',
            'factura' => $factura
        ], 201);
    }
}
