<?php

namespace App\Http\Controllers;

use App\Models\TipoDistribucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class TiposDistribucionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Log::info("Mostrando el índice de TipoDistribucion");

        $tipoDistribucion = TipoDistribucion::orderBy('Descripcion')->paginate(100);

        return Inertia::render('Socat/TiposDistribucion/TipoDistribucion', [
            'datosTipoDistribucion' => [
                'data' => $tipoDistribucion->items(),
                'totalItems' => $tipoDistribucion->total(),
                'currentPage' => $tipoDistribucion->currentPage(),
                'nextPageUrl' => $tipoDistribucion->nextPageUrl(),
                'prevPageUrl' => $tipoDistribucion->previousPageUrl(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()->json(['message' => 'Método create no implementado'], 501);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("Creando un nuevo TipoDistribucion");

        $request->validate([
            'Descripcion' => 'required|string|max:255',
            // 'FechaCaptura' => 'nullable|date', // Ajusta las reglas de validación según sea necesario
            // 'FechaModificacion' => 'nullable|date', // Ajusta las reglas de validación según sea necesario
        ]);

        try {
            $tipoDistribucion = new TipoDistribucion();
            $tipoDistribucion->Descripcion = $request->input('Descripcion');
            $tipoDistribucion->FechaCaptura = $request->input('FechaCaptura'); // Si recibes la fecha desde el frontend
            $tipoDistribucion->FechaModificacion = $request->input('FechaModificacion'); // Si recibes la fecha desde el frontend
            $tipoDistribucion->save();

            return response()->json([
                'data' => $tipoDistribucion,
                'message' => 'TipoDistribucion creado exitosamente',
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error al crear TipoDistribucion: {$e->getMessage()}");
            return response()->json(['message' => 'Error al crear TipoDistribucion'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tipoDistribucion = TipoDistribucion::find($id);

        if (!$tipoDistribucion) {
            return response()->json(['message' => 'TipoDistribucion no encontrado'], 404);
        }

        return response()->json($tipoDistribucion);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->json(['message' => 'Método edit no implementado'], 501);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $IdTipoDistribucion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $IdTipoDistribucion)
    {
        Log::info("Actualizando TipoDistribucion con ID: {$IdTipoDistribucion}");
        // dd($request->all());
        $tipoDistribucion = TipoDistribucion::find($IdTipoDistribucion);

        if (!$tipoDistribucion) {
            Log::warning("TipoDistribucion no encontrado con ID: {$IdTipoDistribucion}");
            return response()->json(['message' => 'TipoDistribucion no encontrado'], 404);
        }

        $request->validate([
            'Descripcion' => 'required|string|max:255',
            // 'FechaCaptura' => 'nullable|date', // Ajusta las reglas de validación según sea necesario
            // 'FechaModificacion' => 'nullable|date', // Ajusta las reglas de validación según sea necesario
        ]);

        try {
            $tipoDistribucion->Descripcion = $request->input('Descripcion');
            $tipoDistribucion->FechaCaptura = $request->input('FechaCaptura'); // Si recibes la fecha desde el frontend
            $tipoDistribucion->FechaModificacion = $request->input('FechaModificacion'); // Si recibes la fecha desde el frontend
            $tipoDistribucion->save();

            Log::info("TipoDistribucion actualizado con ID: {$IdTipoDistribucion}");
            return response()->json([
                'data' => $tipoDistribucion,
                'message' => 'TipoDistribucion actualizado exitosamente',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error al actualizar TipoDistribucion con ID: {$IdTipoDistribucion}: {$e->getMessage()}");
            return response()->json(['message' => 'Error al actualizar TipoDistribucion'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $IdTipoDistribucion
     * @return \Illuminate\Http\Response
     */
    public function destroy($IdTipoDistribucion)
    {
        Log::info("Eliminando TipoDistribucion con ID: {$IdTipoDistribucion}");
        try {
            $tipoDistribucion = TipoDistribucion::findOrFail($IdTipoDistribucion);
            $tipoDistribucion->delete();

            Log::info("TipoDistribucion eliminado con ID: {$IdTipoDistribucion}");
            return response()->json(['message' => 'TipoDistribucion eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            Log::error("Error al eliminar TipoDistribucion con ID: {$IdTipoDistribucion}: {$e->getMessage()}");
            return response()->json(['message' => 'Error al eliminar TipoDistribucion'], 500);
        }
    }

   public function buscaTipoDistribucion(Request $request)
    {
        $tipoDistribucion = $request->input('tipoDistribucion');
        $page = $request->input('page', 1);
        $perPage = 100;
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder');

        $query = TipoDistribucion::query();

        if ($tipoDistribucion) {
            $query->where(DB::raw('LOWER(Descripcion)'), 'like', '%' . strtolower($tipoDistribucion) . '%');
        }

        if ($sortBy && $sortOrder) {
            $query->orderBy($sortBy, $sortOrder === 'ascending' ? 'asc' : 'desc');
        } else {
            $query->orderBy('Descripcion', 'asc');
        }

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $result->items(),
            'totalItems' => $result->total(),
            'currentPage' => $result->currentPage(),
            'nextPageUrl' => $result->nextPageUrl(),
            'prevPageUrl' => $result->previousPageUrl(),
        ]);
    }
}