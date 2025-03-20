<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ShareUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // Recuperar los permisos del usuario
            $permisos = User::permisos($userId);  // Este es el método que obtiene los permisos del usuario

            // Convertir permisos a un array
            $permisosArray = json_decode(json_encode($permisos), true);

            // Compartir los permisos con todos los componentes de Inertia
            Inertia::share('permisos', $permisosArray);
        }

        return $next($request);
    }
}