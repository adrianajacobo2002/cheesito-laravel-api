<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Platillo; 
use App\Models\Inventario;

class PlatilloController extends Controller
{
    public function index()
    {
        $platillos = Platillo::with('inventario')->get();

        return response()->json($platillos);
    }

    public function show($id)
    {
        $platillo = Platillo::with('inventario')->findOrFail($id);

        return response()->json($platillo);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre'  => 'required|string',
            'precio'  => 'required|numeric',
            'tipo'    => 'required|in:comida,bebida',
            'imagen'  => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagenUrl = null;
        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('uploads', 'public');
            $imagenUrl = asset('storage/' . $path);
        }

        $platillo = Platillo::create([
            'nombre'     => $request->nombre,
            'precio'     => $request->precio,
            'tipo'       => $request->tipo,
            'imagen_url' => $imagenUrl,
        ]);

        Inventario::create([
            'platillo_id' => $platillo->id_platillo,
            'cantidad_disponible' => 0,
        ]);

        return response()->json([
            'message'  => 'Platillo creado exitosamente',
            'platillo' => $platillo
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $platillo = Platillo::findOrFail($id);

        $request->validate([
            'nombre' => 'sometimes|string',
            'precio' => 'sometimes|numeric',
            'tipo' => 'sometimes|in:comida,bebida',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $datos = $request->only('nombre', 'precio', 'tipo');

        if ($request->hasFile('imagen')) {
            if ($platillo->imagen_url) {
                $imagenAnterior = str_replace(asset('storage/'), '', $platillo->imagen_url);
                Storage::disk('public')->delete($imagenAnterior);
            }

            $path = $request->file('imagen')->store('uploads', 'public');
            $datos['imagen_url'] = asset('storage/' . $path);
        }

        $platillo->update($datos);

        return response()->json([
            'message' => 'Platillo actualizado correctamente.',
            'platillo' => $platillo->load('inventario'),
        ]);
    }


    public function destroy($id)
    {
        $platillo = Platillo::with('inventario')->findOrFail($id);

        if ($platillo->inventario && $platillo->inventario->cantidad_disponible > 0) {
            return response()->json([
                'message' => 'No se puede eliminar el platillo porque aún tiene stock disponible.'
            ], 400);
        }

        $platillo->delete();

        return response()->json([
            'message' => 'Platillo eliminado correctamente.'
        ]);
    }

    public function exportarPDF()
    {
        $platillos = Platillo::with('inventario')->get();

        $pdf = Pdf::loadView('pdf.platillos', compact('platillos'))
                ->setPaper('A4', 'portrait');

        return $pdf->download('platillos_existencias.pdf');
    }


}
