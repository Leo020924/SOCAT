<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AutorTaxonController;
use App\Http\Controllers\CategoriaTaxonomicaController;
use App\Http\Controllers\GraficasController;
use App\Http\Controllers\GrupoTaxonomicoController;
use App\Http\Controllers\NombreComunController;
use App\Http\Controllers\NombresArbolController;
use App\Http\Controllers\NombresController;
use App\Http\Controllers\TipoDistribucionController;
use App\Http\Controllers\TiposDistribucionController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\TreeMapController;

Route::get('/', function () {
    return Inertia::render('Auth/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    //Rutas definidas para Socat
    Route::get('/busca-autor', [AutorTaxonController::class, 'buscaAutor']);
    Route::get('/autores', [AutorTaxonController::class, 'Index'])->name('autorTaxon.index');
});


Route::middleware(['auth:sanctum', 'verified'])->get('/generate-token', [TokenController::class, 'generateToken'])->name('generate.token');

Route::middleware(['auth'])->group(function () {
    Route::get('/autores', [AutorTaxonController::class, 'index'])->name('autorTaxon.index');
    Route::get('/autores/create', [AutorTaxonController::class, 'create'])->name('autores.create');
    Route::post('/autores', [AutorTaxonController::class, 'store'])->name('autores.store');
    Route::get('/autores/{autor}/edit', [AutorTaxonController::class, 'edit'])->name('autores.edit');
    Route::put('/autores/{id}', [AutorTaxonController::class, 'update'])->name('autores.update');
    Route::delete('/autores/{id}', [AutorTaxonController::class, 'destroy'])->name('autores.destroy');


    Route::get('/busca-grupo', [GrupoTaxonomicoController::class, 'buscaGrupo'])->name('buscaGrupo'); 
    Route::get('/grupos-taxonomicos', [GrupoTaxonomicoController::class, 'index'])->name('grupoTaxonomico.index');
    Route::get('/grupos-taxonomicos/create', [GrupoTaxonomicoController::class, 'create'])->name('gruposTaxonomicos.create');
    Route::post('/grupos-taxonomicos', [GrupoTaxonomicoController::class, 'store'])->name('gruposTaxonomicos.store');
    Route::get('/grupos-taxonomicos/{grupo_taxonomico}/edit', [GrupoTaxonomicoController::class, 'edit'])->name('gruposTaxonomicos.edit');
    Route::put('/grupos-taxonomicos/{IdGrupoSCAT}', [GrupoTaxonomicoController::class, 'update'])->name('gruposTaxonomicos.update');
    Route::delete('/grupos-taxonomicos/{IdGrupoSCAT}', [GrupoTaxonomicoController::class, 'destroy'])->name('gruposTaxonomicos.destroy');


    Route::get('/tipos-distribucion', [TiposDistribucionController::class, 'index'])->name('tiposDistribucion.index');
    Route::get('/tipos-distribucion/create', [TiposDistribucionController::class, 'create'])->name('tiposDistribucion.create');
    Route::post('/tipos-distribucion', [TiposDistribucionController::class, 'store'])->name('tiposDistribucion.store');
    Route::get('/tipos-distribucion/{IdTipoDistribucion}/edit', [TiposDistribucionController::class, 'edit'])->name('tiposDistribucion.edit');
    Route::put('/tipos-distribucion/{IdTipoDistribucion}', [TiposDistribucionController::class, 'update'])->name('tiposDistribucion.update');
    Route::delete('/tipos-distribucion/{IdTipoDistribucion}', [TiposDistribucionController::class, 'destroy'])->name('tiposDistribucion.destroy');

    Route::get('/busca-tipo-distribucion', [TiposDistribucionController::class, 'buscaTipoDistribucion'])->name('buscaTipoDistribucion');




    Route::get('/nombres-comunes', [NombreComunController::class, 'index'])->name('nombresComunes.index');
    Route::get('/nombres-comunes/create', [NombreComunController::class, 'create'])->name('nombresComunes.create');
    Route::post('/nombres-comunes', [NombreComunController::class, 'store'])->name('nombresComunes.store');
    Route::get('/nombres-comunes/{IdNomComun}/edit', [NombreComunController::class, 'edit'])->name('nombresComunes.edit');
    Route::put('/nombres-comunes/{IdNomComun}', [NombreComunController::class, 'update'])->name('nombresComunes.update');
    Route::delete('/nombres-comunes/{IdNomComun}', [NombreComunController::class, 'destroy'])->name('nombresComunes.destroy');
 
    Route::get('/busca-nombre-comun', [NombreComunController::class, 'buscaNombreComun'])->name('buscaNombreComun');


    Route::get('/grafica', [GraficasController::class, 'getData'])->name('grafica.index');

    Route::get('/arbol', [GraficasController::class, 'getData'])->name('grafica.arbol');

    Route::get('/categoria-taxonomica', [CategoriaTaxonomicaController::class, 'index'])->name('categoria-taxonomica.index');


     //----------------------------------Rutas Catalogo de Nombre Taxonomicos----------------------------------
     Route::get('/Nombre',[NombresController::class, 'Index'])->name('nombreTax.index');
     Route::get('/cargar-nomArb',[NombresController::class, 'fetchNomArb']);
     Route::get('/cargar-hijos-nomArb/{id}',[NombresController::class, 'fetchHijos']);

     Route::get('/valCamEstatus', [NombresArbolController::class, 'validaCambio']);



     

    
});
