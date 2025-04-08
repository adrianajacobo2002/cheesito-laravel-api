<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Mesero;


class MeseroController extends Controller
{
    public function index()
    {
        $meseros = Mesero::select('id_mesero', 'nombre')->get();

        return response()->json($meseros);
    }
}
