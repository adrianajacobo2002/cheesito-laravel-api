<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReporteController extends Controller
{
    public function platillosVendidos()
    {
        $resultados = DB::table('detalle_ordenes')
            ->select('platillos.nombre', DB::raw('SUM(cantidad) as total_vendidos'))
            ->join('platillos', 'detalle_ordenes.platillo_id', '=', 'platillos.id_platillo')
            ->groupBy('platillos.nombre')
            ->orderByDesc('total_vendidos')
            ->limit(10)
            ->get();

        return response()->json($resultados);
    }

    public function ingresos(Request $request)
    {
        $request->validate([
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $resultados = DB::table('ordenes')
            ->select(DB::raw('DATE(fecha) as fecha'), DB::raw('SUM(total) as total_ingresos'))
            ->whereBetween('fecha', [$request->desde, $request->hasta])
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('fecha')
            ->get();

        return response()->json($resultados);
    }

    public function ingresosPorHora()
    {
        $resultados = DB::table('facturas')
            ->select(DB::raw("DATE_FORMAT(created_at, '%H:00') as hora"), DB::raw('SUM(total) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->get();

        return response()->json($resultados);
    }

}
