<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bibliografia;
use App\Models\RelBiblioGrupoSCAT;
use App\Models\BibliografiaBorra;
use App\Http\Requests\BibliografiaRequest;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class BibliografiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $buscado = $request->input('buscado');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 100);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder');

        $query = Bibliografia::query();

        if ($buscado) {
            $query->where(DB::raw('LOWER(Autor)'), 'like', '%' . strtolower($buscado) . '%');
        }

        if ($sortBy && $sortOrder) {
            $query->orderBy($sortBy, $sortOrder === 'ascending' ? 'asc' : 'desc');
        } else {
            $query->orderBy('Autor', 'asc');
        }

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Socat/Bibliografia/indexBibliografia', [
            'bibliografiaData' => [
                'data' => $result->items(),
                'totalItems' => $result->total(),
                'currentPage' => $result->currentPage(),
                'lastPage' => $result->lastPage(), // Verifica este nombre
                'nextPageUrl' => $result->nextPageUrl(),
                'prevPageUrl' => $result->previousPageUrl(),
            ],
        ]);
    }


    public function indexApi(Request $request)
    {
        $buscado = $request->input('buscado');
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 100);
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder');

        $query = Bibliografia::query();

        if ($buscado) {
            $query->where(DB::raw('LOWER(Autor)'), 'like', '%' . strtolower($buscado) . '%');
        }

        if ($sortBy && $sortOrder) {
            $query->orderBy($sortBy, $sortOrder === 'ascending' ? 'asc' : 'desc');
        } else {
            $query->orderBy('Autor', 'asc');
        }

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'bibliografiaData' => [
                'data' => $result->items(),
                'totalItems' => $result->total(),
                'currentPage' => $result->currentPage(),
                'lastPage' => $result->lastPage(),
                'nextPageUrl' => $result->nextPageUrl(),
                'prevPageUrl' => $result->previousPageUrl(),
            ],
        ]);
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Autor' => 'required|string|min:1',
            'Anio' => 'required|string|min:1',
            'TituloPublicacion' => 'required|string|min:1',
            'TituloSubPublicacion' => 'nullable|string',
            'EditoresCompiladores' => 'nullable|string',
            'EditorialPaisPagina' => 'nullable|string',
            'NumeroVolumenAnio' => 'nullable|string',
            'OrdenCitaCompleta' => 'nullable|string',
            'citaCompleta' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput(); // Devuelve los errores a la vista
        }

        try {
            $biblio = new Bibliografia;
            $biblio->Autor = $request->input('Autor');
            $biblio->Anio = $request->input('Anio');
            $biblio->TituloPublicacion = $request->input('TituloPublicacion');
            $biblio->TituloSubPublicacion = $request->input('TituloSubPublicacion');
            $biblio->EditoresCompiladores = $request->input('EditoresCompiladores');
            $biblio->EditorialPaisPagina = $request->input('EditorialPaisPagina');
            $biblio->NumeroVolumenAnio = $request->input('NumeroVolumenAnio');
            $biblio->OrdenCitaCompleta = $request->input('OrdenCitaCompleta');
            $biblio->CitaCompleta = $request->input('citaCompleta');
            $biblio->FechaModificacion = now()->toDateTimeString();

            $biblio->save();

            return redirect()->route('bibliografias.index')->with('success', 'La bibliografia taxonómica se dio de alta con éxito'); // Redirige con mensaje de éxito
        } catch (\Exception $e) {
            Log::error("Error creating Bibliografia: " . $e->getMessage());
            return back()->with('error', 'Error al crear la bibliografía: ' . $e->getMessage())->withInput(); // Redirige con mensaje de error
        }
    }

    public function update(Request $request, $id)
    {
        $biblio = Bibliografia::find($id);

        if (!$biblio) {
            return response()->json(['message' => 'Bibliografia no encontrada'], 404);
        }

        $biblio->Autor = $request->input('Autor');
        $biblio->Anio = $request->input('Anio');
        $biblio->EditoresCompiladores = $request->input('EditoresCompiladores');
        $biblio->EditorialPaisPagina = $request->input('EditorialPaisPagina');
        $biblio->NumeroVolumenAnio = $request->input('NumeroVolumenAnio');
        $biblio->OrdenCitaCompleta = $request->input('OrdenCitaCompleta');
        $biblio->TituloPublicacion = $request->input('TituloPublicacion');
        $biblio->TituloSubPublicacion = $request->input('TituloSubPublicacion');
        $biblio->CitaCompleta = $request->input('citaCompleta');
        $biblio->FechaModificacion = now()->toDateTimeString();

        try {
            $biblio->save();
            return response()->json(['message' => 'El registro se actualizó con éxito'], 200);
        } catch (Exception $e) {
            Log::error("Error updating Bibliografia: {$e->getMessage()}");
            return response()->json(['message' => 'Error al actualizar la bibliografía: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $biblio = Bibliografia::findOrFail($id);
            $biblio->delete();
            return response()->json(['status' => 200, 'message' => 'Bibliografia eliminada con éxito']);  // Enviar respuesta JSON
        } catch (\Exception $e) {
            Log::error("Error deleting Bibliografia: {$e->getMessage()}");
            return response()->json(['status' => 500, 'message' => 'Error al eliminar la bibliografía: ' . $e->getMessage()]); // Enviar respuesta JSON
        }
    }
}
