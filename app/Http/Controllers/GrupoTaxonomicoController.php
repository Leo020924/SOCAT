<?php

namespace App\Http\Controllers;

use App\Models\GrupoScat;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GrupoTaxonomicoController extends Controller
{
    public function index()
    {
        $grupo = GrupoScat::orderBy('GrupoSCAT')->paginate(100);
        return Inertia::render('Socat/Grupos/GrupoTaxonomico', [
            'datosGrupo' => [
                'data' => $grupo->items(),
                'totalItems' => $grupo->total(),
                'currentPage' => $grupo->currentPage(),
                'nextPageUrl' => $grupo->nextPageUrl(),
                'prevPageUrl' => $grupo->previousPageUrl(),
            ],
        ]);
    }


    //---------NO BORRAR ESTA PARTE DEL CODIGO, PUEDE SERVIR A FUTURO----------------

    /* public function buscaGrupo(Request $request)
    {
        $nombre = $request->input('grupo', '');

        // Realizar la consulta con el filtro de búsqueda y paginación
        $grupoTax = GrupoTaxonomico::where('Nombre', 'like', "%$nombre%")
            ->orWhere('Descripcion', 'like', "%$nombre%") // Asume que tienes un campo 'Descripcion'
            ->orderBy('Nombre')
            ->paginate(100);

        return Inertia::render('Socat/GruposTaxonomicos/GrupoTaxonomico', [
            'datosGrupo' => [
                'data' => $grupoTax->items(),
                'totalItems' => $grupoTax->total(),
                'currentPage' => $grupoTax->currentPage(),
                'nextPageUrl' => $grupoTax->nextPageUrl(),
                'prevPageUrl' => $grupoTax->previousPageUrl(),
            ],
        ]);
    } */

    // -------------------------------------------------------------------------------------


    //ESTA ES OTRA MANERA DE BUSCAR PERO NO ES CON INERTIA

    public function buscaGrupo(Request $request)
    {
        $grupo = $request->input('grupo');
        $page = $request->input('page', 1);
        $perPage = 100;
        $sortBy = $request->input('sortBy');
        $sortOrder = $request->input('sortOrder');

        $query = GrupoScat::query();

        if ($grupo) {
            $query->where(DB::raw('LOWER(GrupoSCAT)'), 'like', '%' . strtolower($grupo) . '%');
        }

        if ($sortBy && $sortOrder) {
            $query->orderBy($sortBy, $sortOrder === 'ascending' ? 'asc' : 'desc');
        } else {
            $query->orderBy('GrupoSCAT', 'asc');
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
        $grupo = GrupoScat::where('GrupoScat', $request->nomGrupoScat)
            ->get();

        if ($grupo->count() === 0) {

            $grupoScat = new GrupoScat;
            $grupoScat->GrupoScat = $request->GrupoSCAT;
            $grupoScat->GrupoAbreviado = $request->GrupoAbreviado;
            $grupoScat->GrupoSNIB = $request->GrupoSNIB;
            $grupoScat->FechaModificacion = now()->toDateTimeString();
            $grupoScat->save();

            return  response()->json([
                'status' => 200,
                'message' => 'El grupo scat se dio de alta con exito',
            ]);
        } else {
            return  response()->json([
                'status' => 400,
                'message' => 'El grupo scat que intenta guardar ya existe',
            ]);
        }
    }



    public function update(Request $request, $IdGrupoSCAT)
    {
        // dd($request->all());
        $grupoTaxonomico = GrupoScat::find($IdGrupoSCAT);

        if (!$grupoTaxonomico) {
            return response()->json(['message' => 'GrupoTaxonomico not found'], 404);
        }

        $grupoTaxonomico->GrupoSCAT = $request->input('GrupoSCAT'); // Usa el campo correcto
        $grupoTaxonomico->GrupoAbreviado = $request->input('GrupoAbreviado'); // Usa el campo correcto
        $grupoTaxonomico->GrupoSNIB = $request->input('GrupoSNIB');

        try {
            $grupoTaxonomico->save();
            return response()->json([
                'data' => $grupoTaxonomico,
                'message' => 'GrupoTaxonomico Actualizado Exitosamente',
            ], 200);
        } catch (Exception $e) {
            Log::error("Error updating GrupoTaxonomico: {$e->getMessage()}");

            return response()->json(['message' => 'Error updating GrupoTaxonomico'], 500);
        }
    }

    public function destroy($IdGrupoSCAT)
    {
        Log::info("Intentando eliminar grupo taxonómico con ID: {$IdGrupoSCAT}");
        $grupoTaxonomico = GrupoScat::find($IdGrupoSCAT);
        Log::info("GrupoTaxonomico encontrado:", (array) $grupoTaxonomico);
        if (!$grupoTaxonomico) {
            Log::warning("GrupoTaxonomico no encontrado con ID: {$IdGrupoSCAT}");
            return response()->json(['message' => 'GrupoTaxonomico not found'], 404);
        }
        try {
            $grupoTaxonomico->delete();
            Log::info("GrupoTaxonomico eliminado con ID: {$IdGrupoSCAT}");
            return response()->json(['message' => 'GrupoTaxonomico deleted successfully'], 200);
        } catch (\Exception $e) {
            Log::error("Error al eliminar GrupoTaxonomico con ID: {$IdGrupoSCAT}: " . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el GrupoTaxonomico'], 500);
        }
    }
}