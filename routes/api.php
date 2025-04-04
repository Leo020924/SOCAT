<?php

use App\Http\Controllers\AutorTaxonController;
use App\Http\Controllers\BibliografiaController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\GraficasController;
use App\Http\Controllers\GrupoTaxonomicoController;
use App\Http\Controllers\NombresArbolController;
use App\Http\Controllers\NombresController;
use App\Http\Controllers\TiposDistribucionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/busca-autor', [AutorTaxonController::class, 'buscarAutor']);
    Route::get('/autor', [AutorTaxonController::class, 'index']);
    Route::post('/autor', [AutorTaxonController::class, 'store']);
    Route::put('/autor/{id}', [AutorTaxonController::class, 'update']);
    Route::delete('/autor/{id}', [AutorTaxonController::class, 'destroy']);



    Route::get('/busca-grupo', [GrupoTaxonomicoController::class, 'buscaGrupo'])->name('buscaGrupo');

    Route::get('/grupos-taxonomicos/{grupo_taxonomico}/edit', [GrupoTaxonomicoController::class, 'edit'])->name('gruposTaxonomicos.edit');
    Route::put('/grupos-taxonomicos/{id}', [GrupoTaxonomicoController::class, 'update'])->name('gruposTaxonomicos.update');
    Route::delete('/grupos-taxonomicos/{IdGrupoSCAT}', [GrupoTaxonomicoController::class, 'destroy'])->name('gruposTaxonomicos.destroy');

    // Ruta para la búsqueda
    Route::get('/busca-tipo-distribucion', [TiposDistribucionController::class, 'buscaTipoDistribucion'])->name('buscaTipoDistribucion');

    Route::get('/chart-data', [GraficasController::class, 'getData']);



    //Rutas de la API para controlar los nombres taxonomicos 



    //----------------------------------Rutas Catalogo de Nombre Taxonomicos----------------------------------
    Route::get('/Nombre', [NombresController::class, 'Index'])->name('nombreTax.index');
    Route::get('/cargar-nomArb', [NombresController::class, 'fetchNomArb']);
    Route::get('/cargar-hijos-nomArb/{id}', [NombresController::class, 'fetchHijos']);


    Route::get('/cargar-nomArb', [NombresArbolController::class, 'fetchNomArb'])->name('nomTax.fetch');
    Route::get('/valAltEstatus', [NombresArbolController::class, 'validaAltaEst']);
    Route::get('/valCamEstatus', [NombresArbolController::class, 'validaCambio']);
    Route::get('/cargar-hijos-nomArb/{id}', [NombresArbolController::class, 'fetchHijos']);
    Route::get('/carga-filcatalogos', [NombresArbolController::class, 'filtroGruposSCAT']);
    Route::get('/carga-fil-categ', [NombresArbolController::class, 'filtroCateg']);
    Route::get('/carga-list-grp', [NombresArbolController::class, 'cargaListGrupos']);
    Route::get('/cargar-comSnib', [NombresArbolController::class, 'cargaComSnib']);
    Route::get('/carga-AcumuladoSnib', [NombresArbolController::class, 'cargaComAcum']);
    Route::get('/carga-ComDet', [NombresArbolController::class, 'cargaComDet']);
    Route::get('/carga-categ', [NombresArbolController::class, 'cargaCategorias']);
    Route::post('/nombres-store', [NombresArbolController::class, 'store']);
    Route::put('/actualiza-nombre/{id}', [NombresArbolController::class, 'update']);
    Route::put('/baja-nombre/{id}', [NombresArbolController::class, 'bajaTax']);
    Route::put('/mueveTaxones', [NombresArbolController::class, 'mueveTaxa']);

    Route::post('/guardar-imagen', [NombresArbolController::class, 'guardaImagen']);

    //Rutas de la API para controlar los nombres taxonomicos 
    Route::get('/cargar-catTax', [CategoriasController::class, 'fetchCatTax']);
    Route::get('/cargar-hijos-catTax/{id}', [CategoriasController::class, 'fetchHijos']);





     //Rutas de la API para controlar la bibliografia y estas seran validadas por la autenticacion 
     Route::get('/carga-Biblio', [BibliografiaController::class, 'fetchBibliografia'])->name('biblio.fetch');
     Route::get('/busca-Biblio', [BibliografiaController::class, 'buscaBibliografia']);
     Route::match(['put', 'post'], '/bibliografias/{id}', [BibliografiaController::class, 'update'])->name('bibliografias.update');
     Route::delete('/borrar-biblio/{id}', [BibliografiaController::class, 'delete']);
     Route::get('/bibliografias-api', [BibliografiaController::class, 'indexApi'])->name('bibliografias.api');
 
     Route::get('/bibliografiasIndex',[BibliografiaController::class, 'index'])->name('bibliografias.index');
});
