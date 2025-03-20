<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\AutorTaxon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AutorTaxonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $autor = AutorTaxon::orderBy('NombreAutoridad')->paginate(100);

        return Inertia::render('Socat/Autores/AutorTaxon', [
            'datosAutor' => [
                'data' => $autor->items(),
                'totalItems' => $autor->total(),
                'currentPage' => $autor->currentPage(),
                'nextPageUrl' => $autor->nextPageUrl(),
                'prevPageUrl' => $autor->previousPageUrl(),
            ],
        ]);
    }


    //---------NO BORRAR ESTA PARTE DEL CODIGO, PUEDE SERVIR A FUTURO----------------

    /* public function buscaAutor(Request $request)
    {
        $nombre = $request->input('autor', '');

        // Realizar la consulta con el filtro de búsqueda y paginación
        $autorTax = AutorTaxon::where('NombreAutoridad', 'like', "%$nombre%")
            ->orWhere('NombreCompleto', 'like', "%$nombre%")
            ->orWhere('GrupoTaxonomico', 'like', "%$nombre%")
            ->orderBy('NombreAutoridad')
            ->paginate(100);

        return Inertia::render('Socat/Autores/AutorTaxon', [
            'datosAutor' => [
                'data' => $autorTax->items(),
                'totalItems' => $autorTax->total(),
                'currentPage' => $autorTax->currentPage(),
                'nextPageUrl' => $autorTax->nextPageUrl(),
                'prevPageUrl' => $autorTax->previousPageUrl(),
            ],
        ]);
    } */

    // -------------------------------------------------------------------------------------


    //ESTA ES OTRA MANERA DE BUSCAR PERO NO ES CON INERTIA

    public function buscaAutor(Request $request)
    {
        $autor = $request->input('autor');
        $page = $request->input('page', 1);
        $perPage = 100;
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder');

        $query = AutorTaxon::query();

        if ($autor) {
            $query->where(DB::raw('LOWER(NombreAutoridad)'), 'like', '%' . strtolower($autor) . '%');
        }

        if ($sortBy && $sortOrder) {
            $query->orderBy($sortBy, $sortOrder === 'ascending' ? 'asc' : 'desc');
        } else {
            $query->orderBy('NombreAutoridad', 'asc');
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

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombreAutoridad' => 'required|string|min:1',
            'nombreCompleto' => 'nullable|string',
            'grupoTaxonomico' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'message' => 'Error de validación',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatedData = $validator->validated();

        try {
            // Aqui se verificar si ya existe un registro con el mismo NombreAutoridad y GrupoTaxonomico 
            $existingAutorTaxon = AutorTaxon::where(DB::raw('lower(NombreAutoridad)'), strtolower($validatedData['nombreAutoridad']))
                ->where(DB::raw('lower(GrupoTaxonomico)'), strtolower($validatedData['grupoTaxonomico']))
                ->first();

            if ($existingAutorTaxon) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Ya existe un autor con el mismo Nombre de Autoridad y Grupo Taxonómico.'
                ], 400);
            }

            $autorTaxon = new AutorTaxon();
            $autorTaxon->NombreAutoridad = $validatedData['nombreAutoridad'];
            $autorTaxon->NombreCompleto = $validatedData['nombreCompleto'] ?? null;
            $autorTaxon->GrupoTaxonomico = $validatedData['grupoTaxonomico'];
            $autorTaxon->FechaModificacion = now()->toDateTimeString();
            $autorTaxon->save();

            return response()->json([
                'status' => 200,
                'message' => 'La autoridad taxonómica se dio de alta con éxito',
                'autorTaxon' => $autorTaxon
            ], 200);
        } catch (Exception $e) {
            Log::error("Error creating AutorTaxon: " . $e->getMessage());
            return response()->json([
                'status' => 500,
                'message' => 'Error al crear la autoridad taxonómica: ' . $e->getMessage(),
                'error' => $e->__toString(),
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        // dd($request->all());
        $autorTaxon = AutorTaxon::find($id);

        if (!$autorTaxon) {
            return response()->json(['message' => 'AutorTaxon not found'], 404);
        }

        $autorTaxon->NombreAutoridad = $request->input('nombreAutoridad');
        $autorTaxon->NombreCompleto = $request->input('nombreCompleto');
        $autorTaxon->GrupoTaxonomico = $request->input('grupoTaxonomico');

        try {
            $autorTaxon->save();
            return response()->json([
                'data' => $autorTaxon,
                'message' => 'AutorTaxon Actualizado Exitosamente',
            ], 200);
        } catch (Exception $e) {
            Log::error("Error updating AutorTaxon: {$e->getMessage()}");

            return response()->json(['message' => 'Error updating AutorTaxon'], 500);
        }
    }

    public function destroy($id)
    {
        Log::info("Intentando eliminar autor con ID: {$id}");
        $autorTaxon = AutorTaxon::find($id);
        Log::info("AutorTaxon encontrado:", (array) $autorTaxon);
        if (!$autorTaxon) {
            Log::warning("AutorTaxon no encontrado con ID: {$id}");
            return response()->json(['message' => 'AutorTaxon not found'], 404);
        }
        try {
            $autorTaxon->delete();
            Log::info("AutorTaxon eliminado con ID: {$id}");
            return response()->json(['message' => 'AutorTaxon deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Error al eliminar AutorTaxon con ID: {$id}: " . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el AutorTaxon'], 500);
        }
    }
}
