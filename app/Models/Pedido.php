<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $fillable = [
        'referencia_domiciliaria',
        'direccion',
        'factura_fiscal',
        'status',
        'id_cliente',
    ];

    protected $hidden = [
        'updated_at',
    ];
}
