<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $users = User::with(['rol', 'rol.relmodulo'])->get();
        
        return Inertia::render('Users/Index', [
            'users' => $users,

        ]);
    }

   
}
