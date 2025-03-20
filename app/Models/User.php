<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $connection = 'socat'; 

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Asegurar conexión
        $this->setConnection($this->connection);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'IdRol',
        'Alias',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(rol::class,'IdRol', 'IdRol');
    }

    //Función para extraer los modelos y permisos por perfil
    public function scopePermisos($query, $idUsuario) {
        if ($idUsuario) {
            // Esto está bien configurado, con los nombres de tablas correctos
            return DB::select("
                        SELECT modulosocat.NombreModulo,
                               relmodulorol.Altas,
                               relmodulorol.Bajas,
                               relmodulorol.Cambios,
                               relmodulorol.Visible
                        FROM users
                             INNER JOIN relmodulorol ON users.IdRol = relmodulorol.IdRol
                             INNER JOIN modulosocat ON relmodulorol.IdModulo = modulosocat.IdModulo
                             WHERE users.id = ?",[$idUsuario]);
        }
        return $query;
    }
}
