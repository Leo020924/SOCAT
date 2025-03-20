<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Nombre;
use App\Models\CategoriasTaxonomicas;
use App\Models\AutorTaxon;
use App\Models\GrupoScat;
use App\Models\Nombre_Relacion;
use App\Models\Scat;
use App\Models\RelNombreAutor;
use App\Helpers\Helpers;
use Inertia\Inertia;
use Exception;

class NombresArbolController extends Controller
{

    public function index()
    {
        return view('catalogos.NombreArbol.index');
    }

    public function fetchNomArb(Request $request)
    {
        $valor = $request->categ;

        if ($request->has('taxon')) {
            if ($request->categ != '') {
                $categ = explode(',', $request->categ);
                $catalog = explode(',', $request->catalog);
                $taxon = $request->taxon;

                $nombres = Nombre::filtraArbolTaxCat($categ, $catalog, $taxon)
                    ->with([
                        'padre',
                        'hijos',
                        'ascendOblig',
                        'ascendObligHijos',
                        'relNombreCat',
                        'categoria',
                        'scat',
                        'nombreRel',
                        'relNombreAutor',
                        'relNombreRegion',
                        'scat.grupoScat'
                    ])
                    ->paginate(150);
            } else {
                $taxon = $request->taxon;

                $nombres = Nombre::filtraArbolTax($taxon)
                    ->with([
                        'padre',
                        'hijos',
                        'ascendOblig',
                        'ascendObligHijos',
                        'relNombreCat',
                        'categoria',
                        'scat',
                        'nombreRel',
                        'relNombreAutor',
                        'relNombreRegion',
                        'scat.grupoScat'
                    ])
                    ->paginate(150);
            }
        } elseif ($request->has('catalog')) {

            $valor = $request->categ;

            $categ = explode(',', $request->categ);
            $catalog = explode(',', $request->catalog);

            $nombres = Nombre::filtraArbol($categ, $catalog)
                ->with([
                    'padre',
                    'hijos',
                    'ascendOblig',
                    'ascendObligHijos',
                    'relNombreCat',
                    'categoria',
                    'scat',
                    'nombreRel',
                    'relNombreAutor',
                    'relNombreRegion',
                    'scat.grupoScat'
                ])
                ->paginate(150);
        } else {

            $nombres = Nombre::where('IdCategoriaTaxonomica', '=', '1')
                ->with([
                    'padre',
                    'hijos',
                    'ascendOblig',
                    'ascendObligHijos',
                    'relNombreCat',
                    'categoria',
                    'scat',
                    'nombreRel',
                    'relNombreAutor',
                    'relNombreRegion',
                    'scat.grupoScat'
                ])
                ->get();
        }
        $autorTaxComp = Nombre::select('NombreAutoridad')
            ->distinct()
            ->orderBy('NombreAutoridad')
            ->get();

        $gruposTax = $this->filtroGruposSCAT();
        $categoriasTax = $this->filtroCateg();
        $data = [];
        foreach ($nombres as $nombre) {
            if ($nombre->EstadoRegistro === 1) {
                switch ($nombre->Estatus) {
                    case 1:
                        $status = "Sinonimo";
                        break;
                    case 6;
                        $status = "NA";
                        break;
                    case -9:
                        $status = "ND";
                        break;
                    default:
                        switch ($nombre->Nombre) {
                            case 'Animalia':
                                $status = "Válido";
                                break;
                            case 'Plantae':
                            case 'Fungi':
                            case 'Protozoa':
                            case 'Archaea':
                            case 'Bacteria':
                            case 'Chromista':
                                $status = "Correcto";
                                break;
                            default:
                                if ($nombre->categoria->IdNivel2 === 0) {
                                    $status = "Correcto";
                                } else {
                                    $status = "Válido";
                                }
                        }
                        break;
                }

                $nomCat = $nombre->categoria->NombreCategoriaTaxonomica .
                    " - Autor taxón Estatus Sist. Clas./Catálogo de autoridad/Diccionario ";
                $etiqueta = $nombre->NombreCompleto . " " . $nombre->NombreAutoridad . " - " . $status . " - " . $nombre->SistClasCatDicc;

                $query = "select count(1) as conteo 
                              from snib.nombre_taxonomia nt 
                                    left join snib.ejemplar_curatorial e on nt.llavenombre = e.llavenombre 
                                    inner join catalogocentralizado._TransformaTablaNombre_snib t on nt.idnombre = t.IdNombre
                              Where (t.IdNombreRel = " . $nombre->IdNombre . " Or nt.IdNombre = " . $nombre->IdNombre . ") " .
                    "and (e.estadoregistro = '' and nt.estadoregistro NOT LIKE '%En proceso de integraci%')";

                $resp = DB::select(DB::raw($query));

                $newHijo = [
                    'id' => $nombre->IdNombre,
                    'label' => $etiqueta,
                    'children' => [],
                    'texto' => $nomCat,
                    'estatus' => $status,
                    'numEjemp' => $resp[0]->conteo,
                    'completo' => $nombre
                ];

                array_push($data, $newHijo);
            }
        }

        return Inertia::render('Socat/NombreTaxonomico/Nombre', [
            'gruposTax' => json_decode(json_encode($gruposTax)),
            'categoriasTax' => json_decode(json_encode($categoriasTax)),
            'autorTaxComp' => json_decode(json_encode($autorTaxComp)),
            'data' => json_decode(json_encode($data)),
            'total' => $nombres->total(),
            'per_page' => $nombres->perPage(),
            'current_page' => $nombres->currentPage(),
            'from' => $nombres->firstItem(),
            'to' => $nombres->lastItem()
        ]);
    }

    public function fetchHijos($id)
    {
        $nombres = Nombre::cargaHijos($id)
            ->with([
                'padre',
                'hijos',
                'ascendOblig',
                'ascendObligHijos',
                'relNombreCat',
                'categoria',
                'scat',
                'nombreRel',
                'relNombreAutor',
                'scat.grupoScat',
                'scat.grupoScat'
            ])
            ->get();

        $relaciones = Nombre::cargaRelaciones($id)
            ->get();

        $referencias = Nombre::cargaReferencias($id)
            ->get();

        $data = [];
        foreach ($nombres as $nombre) {
            switch ($nombre->Estatus) {
                case 1:
                    $status = "Sinonimo";
                    break;
                case 6;
                    $status = "NA";
                    break;
                case -9:
                    $status = "ND";
                    break;
                default:
                    if ($nombre->categoria->IdNivel2 === 0) {
                        $status = "Correcto";
                    } else {
                        $status = "Válido";
                    }
                    break;
            }

            $nomCat = $nombre->NombreCategoriaTaxonomica .
                " - Autor taxón Estatus Sist. Clas./Catálogo de autoridad/Diccionario ";
            $etiqueta = $nombre->TaxonCompleto . " " . $nombre->NombreAutoridad . " - " . $status . " - " . $nombre->SistClasCatDicc;

            $query = "select count(1) as conteo 
                              from snib.nombre_taxonomia nt 
                                    left join snib.ejemplar_curatorial e on nt.llavenombre = e.llavenombre 
                                    inner join catalogocentralizado._TransformaTablaNombre_snib t on nt.idnombre = t.IdNombre
                              Where (t.IdNombreRel = " . $nombre->IdNombre . " Or nt.IdNombre = " . $nombre->IdNombre . ") " .
                "and (e.estadoregistro = '' and nt.estadoregistro NOT LIKE '%En proceso de integraci%')";

            $resp = DB::select(DB::raw($query));

            $newHijo = [
                'id' => $nombre->IdNombre,
                'label' => $etiqueta,
                'children' => [],
                'texto' => $nomCat,
                'estatus' => $status,
                'numEjemp' => $resp[0]->conteo,
                'completo' => $nombre
            ];

            array_push($data, $newHijo);
        }

        $reldata = [];
        foreach ($relaciones as $relacion) {
            switch ($relacion->Estatus) {
                case 1:
                    $status = "Sinonimo";
                    break;
                case 6;
                    $status = "NA";
                    break;
                case -9:
                    $status = "ND";
                    break;
                default:
                    if ($relacion->categoria->IdNivel2 === 0) {
                        $status = "Correcto";
                    } else {
                        $status = "Válido";
                    }
                    break;
            }

            $newRel = [
                'TipoRelacion' => $relacion->Descripcion,
                'estatus' => $status,
                'Nombrecompleto' => $relacion->NombreCompleto,
                'Biblio' => $relacion->Biblio
            ];

            array_push($reldata, $newRel);
        }

        return response()->json([$data, $reldata, $referencias]);
    }

    public function filtroGruposSCAT()
    {
        $grupoSnib = GrupoScat::select('GrupoSNIB')
            ->distinct()
            ->Orderby('GrupoSCAT')
            ->get();

        $data = [];
        $numCat = 1;

        foreach ($grupoSnib as $grupo) {
            $hijos = GrupoScat::select('IdGrupoSCAT', 'GrupoSCAT')
                ->where('GrupoSNIB', '=', $grupo->GrupoSNIB)
                ->orderBy('GrupoSCAT')
                ->get();

            $childrens = [];

            foreach ($hijos as $hijo) {
                $newChildren = [
                    'id' => $hijo->IdGrupoSCAT,
                    'label' => $hijo->GrupoSCAT,
                    'catalogo' => $grupo->GrupoSNIB
                ];

                array_push($childrens, $newChildren);
            }

            $newHijo = [
                'id' => 'A' . $numCat,
                'label' => $grupo->GrupoSNIB,
                'children' => $childrens,
                'catalogo' => $grupo->GrupoSNIB
            ];

            array_push($data, $newHijo);

            $numCat++;
        }

        return $data;
    }

    public function filtroCateg()
    {
        $categ = CategoriasTaxonomicas::select(
            'NombreCategoriaTaxonomica as label',
            DB::raw('group_concat(IdCategoriaTaxonomica) as value')
        )
            ->groupBy('NombreCategoriaTaxonomica')
            ->OrderByRaw('IdNivel1 ASC, IdNivel2 ASC, IdNivel3 ASC, 
                                                      IdNivel4 ASC')
            ->get();

        return $categ;
    }

    public function cargaListGrupos()
    {

        $listGrupos =  GrupoScat::orderByRaw('GrupoSCAT')->get();

        $lista = [];

        foreach ($listGrupos as $regList) {

            $newReg = [
                'id' => $regList->IdGrupoSCAT,
                'label' => $regList->GrupoSCAT . '-' . $regList->GrupoAbreviado . '-' . $regList->GrupoSNIB
            ];

            array_push($lista, $newReg);
        }

        return $lista;
    }

    public function validaCambio(Request $request)
    {
        log::info("Llegue al controller");
        switch ($request->estatusInicio) {
            case 1:
                $tiposRel = Nombre_Relacion::where('IdNombre', '=', $request->nomAct)
                    ->whereIn('IdTipoRelacion', [1, 2])
                    ->count();

                if ($tiposRel > 0) {
                    return false;
                }

                return true;

                break;
            case 2:
                $hijos = Nombre::where('IdNombreAscendente', '=', $request->nomAct)
                    ->where('Estatus', '=', 2)
                    ->count();

                $tipoRel = Nombre_Relacion::where('IdNombre', '=', $request->nomAct)
                    ->whereIn('IdTipoRelacion', [1, 2])
                    ->count();

                if ($hijos > 0 || $tipoRel > 0) {
                    return false;
                }

                return true;

                break;

            case 6:
                $tipoRel = Nombre_Relacion::where('IdNombre', '=', $request->nomAct)
                    ->whereIn('IdTipoRelacion', [1, 2])
                    ->count();

                if ($tipoRel > 0) {
                    return false;
                }

                return true;

                break;
            case -9:
                $tipoRel = Nombre_Relacion::where('IdNombre', '=', $request->nomAct)
                    ->whereIn('IdTipoRelacion', [1, 2])
                    ->count();

                if ($tipoRel > 0) {
                    return false;
                }

                return true;

                break;
        }
    }

    public function  cargaComSnib(Request $request)
    {
        //Aqui se define un inner join con la segunda base de datos en el mismo servidor 
        $resultado = DB::connection('snib')->table('nombre_taxonomia')
            ->join('catalogocentralizado._TransformaTablaNombre_snib', 'nombre_taxonomia.idnombre', '=', '_TransformaTablaNombre_snib.idNombre')
            ->where('_TransformaTablaNombre_snib.IdNombreRel', '=', $request->idNombre)
            ->orWhere('nombre_taxonomia.idnombre', '=', $request->idNombre)
            ->count();

        return $resultado;
    }

    //carga comentarios del snib 
    public function cargaComAcum(Request $request)
    {

        $resultado = DB::connection('mysql2')->table('snib.nombre_taxonomia')
            ->join('catalogocentralizado._TransformaTablaNombre_snib', 'nombre_taxonomia.idnombre', '=', '_TransformaTablaNombre_snib.idNombre')
            ->select(DB::raw('nombre_taxonomia.idnombre, nombre_taxonomia.comentarioscat, count(1) AS Conteo'))
            ->where('_TransformaTablaNombre_snib.IdNombreRel', '=', $request->idNombre)
            ->orWhere('nombre_taxonomia.idnombre', '=', $request->idNombre)
            ->groupBy('nombre_taxonomia.idnombre', 'nombre_taxonomia.comentarioscat')
            ->get();

        return $resultado;
    }

    //carga comentarios detalle del snib
    public function cargaComDet(Request $request)
    {

        //return $request->idNombre;
        //En esta condicion se maneja una condicion entre parentesis 

        $resultado = DB::connection('mysql2')->table('snib.nombre_taxonomia')
            ->join('catalogocentralizado._TransformaTablaNombre_snib', 'nombre_taxonomia.idnombre', '=', '_TransformaTablaNombre_snib.idNombre')
            ->select(DB::raw('nombre_taxonomia.idnombre, nombre_taxonomia.comentarioscat, nombre_taxonomia.llavenombre'))
            ->where('nombre_taxonomia.comentarioscat', '=', $request->comentarios)
            ->where(function ($query) use ($request) {
                /*$query->where('_TransformaTablaNombre_snib.IdNombreRel', '=', $request->idNombre)
                        ->orWhere('nombre_taxonomia.idnombre', '=', $request->idNombre);*/
                $query->where('nombre_taxonomia.idnombre', '=', $request->idNombre);
            })->distinct()->get();

        return response()->json($resultado);
    }

    //Carga lista de categorias taxonomicas descendentes 
    public function cargaCategorias(Request $request)
    {

        if ($request->idNombre === '1') {
            $categorias = CategoriasTaxonomicas::where('IdAscendente', '=', $request->idNombre)
                ->where('IdCategoriaTaxonomica', '<>', $request->idNombre)
                ->select('IdCategoriaTaxonomica', 'NombreCategoriaTaxonomica')
                ->OrderByRaw('IdNivel1 ASC, IdNivel2 ASC, IdNivel3 ASC, 
                                                             IdNivel4 ASC')
                ->get();
        } else {
            $categorias = CategoriasTaxonomicas::where('IdAscendente', '=', $request->idNombre)
                ->where('IdCategoriaTaxonomica', '<>', $request->idNombre)
                ->where('IdNivel2', '=', $request->IdNivel2)
                ->select('IdCategoriaTaxonomica', 'NombreCategoriaTaxonomica')
                ->OrderByRaw('IdNivel1 ASC, IdNivel2 ASC, IdNivel3 ASC, 
                                                             IdNivel4 ASC')
                ->get();

            if ($request->IdNivel1 < 7) {
                $idNiv = $request->IdNivel1 + 1;
                $categ = CategoriasTaxonomicas::where('IdNivel1', '=', $idNiv)
                    ->where('IdNivel2', '=', $request->IdNivel2)
                    ->where('IdNivel3', '=', '0')
                    ->where('IdNivel4', '=', '0')
                    ->select('IdCategoriaTaxonomica', 'NombreCategoriaTaxonomica')
                    ->OrderByRaw('IdNivel1 ASC, IdNivel2 ASC, IdNivel3 ASC, 
                                                             IdNivel4 ASC')
                    ->get();

                $categorias = $categorias->merge($categ);
            }
        }

        $lista = [];

        foreach ($categorias as $regList) {

            $newReg = [
                'id' => $regList->IdCategoriaTaxonomica,
                'label' => $regList->NombreCategoriaTaxonomica
            ];

            array_push($lista, $newReg);
        }

        return $lista;
    }

    //Funcion para dar de alta un taxon 
    public function store(Request $request)
    {
        try {

            //Log::info($request);
            DB::transaction(function () use ($request, &$nombres) {

                $nomAscendente = Nombre::find($request['IdAscendente']);

                $grupoScat =  GrupoScat::find($request['Grupo']);

                $nombre = Nombre::create([
                    'IdCategoriaTaxonomica' => $request['categoria'],
                    'IdNombreAscendente' => $request['IdAscendente'],
                    'IdAscendObligatorio' => $request['IdAscenOblig'],
                    'Nombre' => $request->NombreTax['nombreTaxon'],
                    'NombreAutoridad' => $request->NombreTax['nombreAutoridad'],
                    'Estatus' => $request['Estatus'],
                    'NumeroFilogenetico' => $request->NombreTax['numeroFilogenetico'],
                    'CitaNomenclatural' => $request->NombreTax['citaNomenclatural'],
                    'SistClasCatDicc' => $request->NombreTax['sistClassDicc'],
                    'Anotacion' => $request->NombreTax['anotacionTaxon'],
                    'EstadoRegistro' => '1',
                    'Fuente' => 'SOCAT',
                    'FechaModificacion' => now()->toDateTimeString()
                ]);

                $ascen = Helpers::listaAscen($nombre);
                $ascenOblig = Helpers::listaAscenOblig($nombre);
                $nomComp = Helpers::nombreCompleto($nombre);
                $taxonComp = Helpers::taxonCompleto($nombre);

                $nombre->update([
                    'Ascendentes' => $ascen,
                    'AscendentesObligatorios' => $ascenOblig,
                    'NombreCompleto' => $nomComp,
                    'TaxonCompleto' => $taxonComp
                ]);

                $nombre->scat()->create([
                    'IdNombre' => $nombre->IdNombre,
                    'IDCAT' => $grupoScat['IdGrupoSCAT'] . $grupoScat['GrupoAbreviado'],
                    'Nivel_de_revision' => $request['NivelRevision'],
                    'HomonimiaSNIB' => $request['HomonimiaSnib'],
                    'ValidacionSNIB' => $request['ValidacionSnib'],
                    'ListaInvasoras' => $request['IdInvasora'],
                    'Publico' => $request['Publico'],
                    'IdGrupoSCAT' => $request['Grupo'],
                    'FechaModificacion' => now()->toDateTimeString()
                ]);

                $autorNombre = [];

                foreach ($request->listAutor as $autor) {

                    $newReg = [
                        'IdNombre' => $nombre->IdNombre,
                        'IdAutorTaxon' => $autor['IdAutorTaxon']
                    ];

                    array_push($autorNombre, $newReg);
                }

                DB::table('RelNombreAutor')->insert($autorNombre);

                $nombres = Nombre::with([
                    'padre',
                    'hijos',
                    'ascendOblig',
                    'ascendObligHijos',
                    'relNombreCat',
                    'categoria',
                    'scat',
                    'nombreRel',
                    'relNombreAutor',
                    'relNombreRegion',
                    'scat.grupoScat'
                ])
                    ->find($nombre['IdNombre']);

                //Log::info($nombres);
            });

            DB::commit(); // Confirma la transacción

            if ($nombres->EstadoRegistro === 1) {
                switch ($nombres->Estatus) {
                    case 1:
                        $status = "Sinonimo";
                        break;
                    case 6;
                        $status = "NA";
                        break;
                    case -9:
                        $status = "ND";
                        break;
                    default:
                        switch ($nombres->Nombre) {
                            case 'Animalia':
                                $status = "Válido";
                                break;
                            case 'Plantae':
                            case 'Fungi':
                            case 'Protozoa':
                            case 'Archaea':
                            case 'Bacteria':
                            case 'Chromista':
                                $status = "Correcto";
                                break;
                            default:
                                if ($nombres->categoria->IdNivel2 === 0) {
                                    $status = "Correcto";
                                } else {
                                    $status = "Válido";
                                }
                        }
                        break;
                }

                $nomCat = $nombres->categoria->NombreCategoriaTaxonomica .
                    " - Autor taxón Estatus Sist. Clas./Catálogo de autoridad/Diccionario ";
                $etiqueta = $nombres->NombreCompleto . " " . $nombres->NombreAutoridad . " - " . $status . " - " . $nombres->SistClasCatDicc;

                $query = "select count(1) as conteo 
                              from snib.nombre_taxonomia nt 
                                    left join snib.ejemplar_curatorial e on nt.llavenombre = e.llavenombre 
                                    inner join catalogocentralizado._TransformaTablaNombre_snib t on nt.idnombre = t.IdNombre
                              Where (t.IdNombreRel = " . $nombres->IdNombre . " Or nt.IdNombre = " . $nombres->IdNombre . ") " .
                    "and (e.estadoregistro = '' and nt.estadoregistro NOT LIKE '%En proceso de integraci%')";

                $resp = DB::select(DB::raw($query));

                $newHijo = [
                    'id' => $nombres->IdNombre,
                    'label' => $etiqueta,
                    'children' => [],
                    'texto' => $nomCat,
                    'estatus' => $status,
                    'numEjemp' => $resp[0]->conteo,
                    'completo' => $nombres
                ];
            }

            return  response()->json([
                'status' => 200,
                'message' => 'El taxon se dio de alta con exito',
                'nombreNuevo' => $newHijo
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Realiza el rollback si hay un error

            return  response()->json([
                'status' => 400,
                'message' => 'Error: ' . $e->getMessage(), // Mostrar el mensaje de error
            ]);
        }
    }

    //Funcion para guardar la imagen
    public function guardaImagen(Request $request)
    {

        if ($request->hasFile('image')) {

            $imagePath = $request->file('image')->store('images', 'public');

            return response()->json(['image_path' => $imagePath]);
        }

        return response()->json(['error' => 'No se ha proporcionado ninguna imagen'], 400);
    }

    //Funcion para actualizar el nombre del taxon 
    public function update(Request $request, $id)
    {

        try {
            // Aquí comienza la transacción
            DB::transaction(
                function () use ($request, $id, &$nombres) {



                    $nombre = Nombre::find($id);

                    $nombre->update([
                        'Estatus' => $request->model['Estatus'],
                        'IdNombreAscendente' => $request->model['IdAscendente'],
                        'Anotacion' => $request->model['NombreTax']['anotacionTaxon'],
                        'CitaNomenclatural' => $request->model['NombreTax']['citaNomenclatural'],
                        'NombreAutoridad' => $request->model['NombreTax']['nombreAutoridad'],
                        'Nombre' => $request->model['NombreTax']['nombreTaxon'],
                        'NumeroFilogenetico' => $request->model['NombreTax']['numeroFilogenetico'],
                        'SistClasCatDicc' => $request->model['NombreTax']['sistClassDicc'],
                        'IdCategoriaTaxonomica' => $request->model['categoria'],
                        'ModificadoPor' => $nombre->ModificadoPor . ',' . $request->model['alias']
                    ]);

                    $nomComp = Helpers::nombreCompleto($nombre);
                    $taxonComp = Helpers::taxonCompleto($nombre);

                    $nombre->update([
                        'NombreCompleto' => $nomComp,
                        'TaxonCompleto' => $taxonComp
                    ]);

                    $this->actualizaNombreHijos($nombre);

                    $nombre->scat()->update([
                        'IdGrupoSCAT' => $request->model['Grupo'],
                        'HomonimiaSNIB' => $request->model['HomonimiaSnib'],
                        'Nivel_de_revision' => $request->model['NivelRevision'],
                        'Publico' => $request->model['Publico'],
                        'ValidacionSNIB' => $request->model['ValidacionSnib'],
                        'ListaInvasoras' => $request->model['IdInvasora'],
                    ]);

                    if (count($request->model['listAutor']) > 0) {

                        $relAutor = RelNombreAutor::where('IdNombre', '=',  $id);
                        $relAutor->delete();

                        $autorNombre = [];

                        foreach ($request->model['listAutor'] as $autor) {
                            $newReg = [
                                'IdNombre' => $nombre->IdNombre,
                                'IdAutorTaxon' => $autor['IdAutorTaxon'],
                                'FechaModificacion' => now()->toDateTimeString()
                            ];

                            array_push($autorNombre, $newReg);
                        }

                        DB::table('RelNombreAutor')->insert($autorNombre);
                    }

                    $nombres = Nombre::with([
                        'padre',
                        'hijos',
                        'ascendOblig',
                        'ascendObligHijos',
                        'relNombreCat',
                        'categoria',
                        'scat',
                        'nombreRel',
                        'relNombreAutor',
                        'relNombreRegion',
                        'scat.grupoScat'
                    ])
                        ->find($id);
                }
            );

            DB::commit(); // Confirma la transacción

            if ($nombres->EstadoRegistro === 1) {
                switch ($nombres->Estatus) {
                    case 1:
                        $status = "Sinonimo";
                        break;
                    case 6;
                        $status = "NA";
                        break;
                    case -9:
                        $status = "ND";
                        break;
                    default:
                        switch ($nombres->Nombre) {
                            case 'Animalia':
                                $status = "Válido";
                                break;
                            case 'Plantae':
                            case 'Fungi':
                            case 'Protozoa':
                            case 'Archaea':
                            case 'Bacteria':
                            case 'Chromista':
                                $status = "Correcto";
                                break;
                            default:
                                if ($nombres->categoria->IdNivel2 === 0) {
                                    $status = "Correcto";
                                } else {
                                    $status = "Válido";
                                }
                        }
                        break;
                }

                $nomCat = $nombres->categoria->NombreCategoriaTaxonomica .
                    " - Autor taxón Estatus Sist. Clas./Catálogo de autoridad/Diccionario ";
                $etiqueta = $nombres->NombreCompleto . " " . $nombres->NombreAutoridad . " - " . $status . " - " . $nombres->SistClasCatDicc;

                $query = "select count(1) as conteo 
                                from snib.nombre_taxonomia nt 
                                    left join snib.ejemplar_curatorial e on nt.llavenombre = e.llavenombre 
                                    inner join catalogocentralizado._TransformaTablaNombre_snib t on nt.idnombre = t.IdNombre
                                Where (t.IdNombreRel = " . $nombres->IdNombre . " Or nt.IdNombre = " . $nombres->IdNombre . ") " .
                    "and (e.estadoregistro = '' and nt.estadoregistro NOT LIKE '%En proceso de integraci%')";

                $resp = DB::select(DB::raw($query));

                $newHijo = [
                    'id' => $nombres->IdNombre,
                    'label' => $etiqueta,
                    'children' => [],
                    'texto' => $nomCat,
                    'estatus' => $status,
                    'numEjemp' => $resp[0]->conteo,
                    'completo' => $nombres
                ];
            }

            return  response()->json([
                'status' => 200,
                'message' => 'El taxon fue modificado con exito',
                'nombreMod' => $newHijo
            ]);
        } catch (Exception $e) {
            // Si hay un error, Laravel hace un ROLLBACK automáticamente
            return response()->json([
                'status' => 400,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function mueveTaxa(Request $request)
    {

        $nombre = Nombre::with(['padre', 'hijos', 'ascendOblig', 'ascendObligHijos', 'categoria'])
            ->find($request->model['taxonRecibir']);

        $nombreAct = Nombre::with(['padre', 'hijos', 'ascendOblig', 'ascendObligHijos', 'categoria'])
            ->find($request->model['taxonMover']);

        DB::begintransaction();

        try {

            if ($nombre->categoria->IdNivel3 === 0) {
                $nombreAct->update([
                    'IdNombreAscendente' => $nombre['IdNombre'],
                    'IdAscendObligatorio' => $nombre['IdNombre'],
                    'ModificadoPor' => $nombreAct->ModificadoPor . ", " . $request->model['alias']
                ]);
            } else {
                $nombreAct->update([
                    'IdNombreAscendente' => $nombre['IdNombre'],
                    'IdAscendObligatorio' => $nombre['IdAscendObligatorio'],
                    'ModificadoPor' => $nombreAct->ModificadoPor . ", " . $request->model['alias']
                ]);
            }

            $nombreAct = Nombre::with(['padre', 'hijos', 'ascendOblig', 'ascendObligHijos', 'categoria'])
                ->find($request->model['taxonMover']);

            $ascen = Helpers::listaAscen($nombreAct);
            $ascenOblig = Helpers::listaAscenOblig($nombreAct);
            $nomComp = Helpers::nombreCompleto($nombreAct);
            $taxonComp = Helpers::taxonCompleto($nombreAct);

            $nombreAct->update([
                'Ascendentes' => $ascen,
                'AscendentesObligatorios' => $ascenOblig,
                'NombreCompleto' => $nomComp,
                'TaxonCompleto' => $taxonComp
            ]);

            $this->actualizaHijos($nombreAct);

            DB::commit();

            $categ = explode(',', $request->model['categorias']);
            $catalog = explode(',', $request->model['grupos']);

            $nombres = Nombre::filtraArbol($categ, $catalog)
                ->with([
                    'padre',
                    'hijos',
                    'ascendOblig',
                    'ascendObligHijos',
                    'relNombreCat',
                    'categoria',
                    'scat',
                    'nombreRel',
                    'relNombreAutor',
                    'relNombreRegion',
                    'scat.grupoScat'
                ])
                ->paginate(150);

            return  response()->json([
                'status' => 200,
                'message' => 'Los taxones se movieron con exito',
                'nombre' => $nombres
            ]);
        } catch (\Exception $e) {
            //Si se presenta un error se aplicara un rollback
            DB::rollback();
            return $e;
            return  response()->json([
                'status' => 400,
                'message' => 'No se puede hacer el movimiento del taxon',
            ]);
        }
    }

    public function actualizaHijos($nombre)
    {

        foreach ($nombre->hijos as $hijo) {

            $ascen = Helpers::listaAscen($hijo);
            $ascenOblig = Helpers::listaAscenOblig($hijo);
            $nomComp = Helpers::nombreCompleto($hijo);
            $taxonComp = Helpers::taxonCompleto($hijo);

            $hijo->update([
                'Ascendentes' => $ascen,
                'AscendentesObligatorios' => $ascenOblig,
                'NombreCompleto' => $nomComp,
                'TaxonCompleto' => $taxonComp
            ]);

            $this->actualizaHijos($hijo);
        }
    }

    public function actualizaNombreHijos($nombre)
    {
        Log::info($nombre->hijos);

        foreach ($nombre->hijos as $hijo) {
            Log::info($hijo);
            $nomComp = Helpers::nombreCompleto($hijo);
            Log::info($nomComp);
            $taxonComp = Helpers::taxonCompleto($hijo);
            Log::info($taxonComp);

            $hijo->update([
                'NombreCompleto' => $nomComp,
                'TaxonCompleto' => $taxonComp
            ]);

            $this->actualizaHijos($hijo);
        }
    }

    public function bajaTax(Request $request, $id)
    {

        $nombre = Nombre::find($id);
        Log::info("Estado del registro encontrado: " . $nombre['EstadoRegistro']);
        if ($nombre) {

            $nombre->EstadoRegistro = 0;
            $nombre->ModificadoPor = $nombre->ModificadoPor . ",Eliminado por " . $request->alias;
            Log::info($request);
            Log::info($nombre);
            $nombre->save();

            return  response()->json([
                'status' => 200,
                'Id' => $id,
                'message' => 'El taxon fue eliminado con exito',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Registro no encontrado',
            ]);
        }
    }
}
