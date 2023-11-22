<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Empresa;
use App\Models\Personal;
use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Empresa::create([
            'nombres' => 'FARMACORP',
            'direccion' => 'Santa cruz - tercer anillo ',
            'n_contacto' => '12345678',
            'nit' => '2545557',
        ]);

        Empresa::create([
            'nombres' => 'VILLA COPACABANA',
            'direccion' => 'Santa cruz - tercer anillo ',
            'n_contacto' => '12345678',
            'nit' => '2545557',
        ]);


        Sucursal::create([
            'nombres' => 'Sucursal 1-FARMACORP',
            'direccion' => 'Santa cruz - Av. circunvalacion',
            'n_contacto' => '12345678',
            'id_empresa' => 1,
            'status' => true,
        ]);


        Sucursal::create([
            'nombres' => 'Sucursal 1- VILLA',
            'direccion' => 'Santa cruz - Av. circunvalacion',
            'n_contacto' => '12345678',
            'id_empresa' => 2,
            'status' => true,
        ]);

        Personal::create([
            'nombres' => 'Carlos',
            'apellido_paterno' => 'Poma',
            'apellido_materno' => 'Flores',
            'cargo' => 'Sin especificar',
            'ci' => 1234567,
            'ci_expedido' => 'OR',
            'n_contacto' => 1234567,
            'direccion' => 'La Paz - Bolivia',
            'status' => true,
            'email' => "admin@gmail.com",
            'foto' => '/storage/imagenes/img-user.png',
            'id_sucursal' => 1
        ]);

        Personal::create([
            'nombres' => 'Juan',
            'apellido_paterno' => 'Poma',
            'apellido_materno' => 'Flores',
            'cargo' => 'Sin especificar',
            'ci' => 1234567,
            'ci_expedido' => 'OR',
            'n_contacto' => 1234567,
            'direccion' => 'La Paz - Bolivia',
            'status' => true,
            'email' => "admin@gmail.com",
            'foto' => '/storage/imagenes/img-user.png',
            'id_sucursal' => 2
        ]);


        User::create([
            'usuario' => 'carlos@gmail.com',
            'status' => true,
            'password' => '$2y$10$jjDb4siaEWs3Iw.sFqFwquRENoM/Lsi.IK6WL5L9fXF/x1GXKPfFq', //1234
            'id_personal' => 1,
        ]);

        User::create([
            'usuario' => 'juan@gmail.com',
            'status' => true,
            'password' => '$2y$10$jjDb4siaEWs3Iw.sFqFwquRENoM/Lsi.IK6WL5L9fXF/x1GXKPfFq', //1234
            'id_personal' => 2,
        ]);


        Role::create([
            'type_role' => 'Administrador',
            'id_user' => 1,
        ]);

        Role::create([
            'type_role' => 'Administrador',
            'id_user' => 2,
        ]);
    }
}
