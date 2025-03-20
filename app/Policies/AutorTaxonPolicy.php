<?php

namespace App\Policies;

use App\Models\User;

class AutorTaxonPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user)
    {
        return $user->IdRol === 2;  // O la lógica para verificar el rol
    }
}
