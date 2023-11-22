<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [];

    protected $hidden = [
        'updated_at',
    ];

    public static function thisEmpresa()
    {
        $empresa = Empresa::join('sucursales', 'sucursales.id_empresa', '=', 'empresas.id')
            ->join('personals', 'personals.id_sucursal', '=', 'sucursales.id')
            ->join('users', 'users.id_personal', '=', 'personals.id')
            ->select('empresas.*')
            ->where('users.id', Auth::user()->id)->first();
        return $empresa;
    }
}
