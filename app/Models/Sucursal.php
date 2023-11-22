<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;
    protected $table = "sucursales";

    protected $fillable = [
        'nombres',
        'direccion',
        'n_contacto',
        'id_empresa',
        'status',
    ];

    protected $hidden = [
        'status',
        'updated_at',
    ];
}
