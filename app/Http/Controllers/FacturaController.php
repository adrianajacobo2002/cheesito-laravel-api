<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orden;
use App\Models\Factura;
use App\Models\Mesa;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function pagarOrden($orden_id)
    {
        $orden = Orden::with(['detalles.platillo', 'mesa'])->where('id_orden', $orden_id)->firstOrFail();

        if ($orden->estado !== 'por pagar') {
            return response()->json(['message' => 'La orden ya fue cancelada.'], 400);
        }

        $platillosNoListos = $orden->detalles->where('estado', '!=', 'listo');

        if ($platillosNoListos->count() > 0) {
            return response()->json([
                'message' => 'La orden contiene platillos que aún no están listos.',
                'pendientes' => $platillosNoListos
            ], 400);
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

        if ($orden->mesa_id) {
            Mesa::where('id_mesa', $orden->mesa_id)->update(['estado' => 'disponible']);
        }

        return response()->json([
            'message' => 'Factura creada exitosamente',
            'factura' => $factura
        ], 201);
    }

    public function generarPDF($factura_id)
    {
        $factura = Factura::with('orden.detalles.platillo', 'orden.mesa')->findOrFail($factura_id);

        $pdf = Pdf::loadView('pdf.factura', compact('factura'))
            ->setPaper('A4', 'portrait');

        return $pdf->download("factura_{$factura->id}.pdf");
    }
}
