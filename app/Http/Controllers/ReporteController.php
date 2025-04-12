<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Orden;
use App\Models\Platillo;
use App\Models\DetalleOrden;
use App\Models\Factura;


class ReporteController extends Controller
{
    public function platillosVendidos()
    {
        $resultados = DB::table('detalle_orden')
            ->select('platillos.nombre', DB::raw('SUM(cantidad) as total_vendidos'))
            ->join('platillos', 'detalle_orden.platillo_id', '=', 'platillos.id_platillo')
            ->groupBy('platillos.nombre')
            ->orderByDesc('total_vendidos')
            ->limit(10)
            ->get();

        return response()->json($resultados);
    }


    public function ingresos(Request $request)
    {
        $query = DB::table('facturas')
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('SUM(total) as total_ingresos'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('fecha');

        if ($request->filled('desde') && $request->filled('hasta')) {
            $request->validate([
                'desde' => 'date',
                'hasta' => 'date|after_or_equal:desde',
            ]);

            $query->whereBetween('created_at', [$request->desde, $request->hasta]);
        }

        $resultados = $query->get();

        return response()->json($resultados);
    }
    
    public function ingresosPorHora()
    {
        $resultados = DB::table('facturas')
            ->select(DB::raw("TO_CHAR(created_at, 'HH24:00') as hora"), DB::raw('SUM(total) as total'))
            ->groupBy(DB::raw("TO_CHAR(created_at, 'HH24:00')"))
            ->orderBy('hora')
            ->get();

        return response()->json($resultados);
    }
}
