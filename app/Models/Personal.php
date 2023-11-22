<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Personal extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'cargo',
        'ci',
        'ci_expedido',
        'n_contacto',
        'direccion',
        'email',
        'foto',
        'id_sucursal',
        'status',
    ];
    protected $hidden = [
        'updated_at',
        'status',
    ];

}
