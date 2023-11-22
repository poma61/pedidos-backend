<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Empresa;
use App\Models\Role;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{

    public function index(Request $request)
    {
        try {
            $empresa = Empresa::thisEmpresa();
            $user = User::join('roles', 'roles.id_user', '=', 'users.id')
                ->join('personals', 'personals.id', '=', 'users.id_personal')
                ->join('sucursales', 'sucursales.id', '=', 'personals.id_sucursal')
                ->select(
                    'users.*',
                    'roles.type_role',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                )
                ->where('personals.status', true)
                ->where('users.status', true)
                ->where('sucursales.status', true)
                ->where('sucursales.id_empresa', $empresa->id)
                ->where('sucursales.nombres', $request->input('sucursal'))
                ->orderBy('id', 'ASC')
                ->get();

            return response()->json([
                'records' => $user,
                'status' => true,
                'message' => 'OK',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'records' => null,
                'status' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function store(UserRequest $request)
    {
        try {
            $sucursal = Sucursal::where('status', true)
                ->where('nombres', $request->input('sucursal'))
                ->first();
            //debemos verificar si la sucursal existe en la base de datos por seguridad 
            if ($sucursal == null) {
                return response()->json([
                    'record' => null,
                    'status' => false,
                    'message' => 'La sucursal no existe!',
                ], 200);
            }

            $usuario = new User($request->all());
            $usuario->password = Hash::make($request->input('password'));
            $usuario->status = true;
            $usuario->save();

            //creamos el rol 
            $role = new Role();
            $role->type_role = $request->input('type_role');
            $role->id_user = $usuario->id;
            $role->save();

            $user = User::join('roles', 'roles.id_user', '=', 'users.id')
                ->join('personals', 'personals.id', '=', 'users.id_personal')
                ->join('sucursales', 'sucursales.id', '=', 'personals.id_sucursal')
                ->select(
                    'users.*',
                    'roles.type_role',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                )
                ->where('users.id', $usuario->id)
                ->first();

            return response()->json([
                'record' => $user,
                'status' => true,
                'message' => 'Registro creado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }



    public function update(UserRequest $request)
    {

        try {
            $usuario = User::where('status', true)
                ->where('id', $request->input('id'))
                ->first();
            $usuario->fill($request->except('password'));
            //Si el campo password  esta vacio entonces empty devolvera false y se encriptara la contraseña
            if (empty($request->input('password')) == false) {
                $usuario->password = Hash::make($request->input('password'));
            }
            $usuario->update();
            //actualizamos el rol 
            $role = Role::where('id_user', $request->input('id'))
                ->first();
            $role->type_role = $request->input('type_role');
            $role->update();

            $user = User::join('roles', 'roles.id_user', '=', 'users.id')
                ->join('personals', 'personals.id', '=', 'users.id_personal')
                ->join('sucursales', 'sucursales.id', '=', 'personals.id_sucursal')
                ->select(
                    'users.*',
                    'roles.type_role',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                )
                ->where('users.id', $usuario->id)
                ->first();

            return response()->json([
                'record' => $user,
                'status' => true,
                'message' => 'Registro actualizado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $usuario = User::where('status', true)
                ->where('id', $request->input('id'))
                ->first();
            $usuario->status = false;
            $usuario->update();

            return response()->json([
                'status' => true,
                'message' => 'Registro eliminado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
