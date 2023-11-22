<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'n_contacto',
        'email',
        'id_sucursal',
        'status',
    ];

    protected $hidden = [
        'status',
        'updated_at',
    ];
}
