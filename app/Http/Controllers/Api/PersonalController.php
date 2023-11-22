<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PersonalRequest;
use App\Models\Empresa;
use App\Models\Personal;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PersonalController extends Controller
{

    public function index(Request $request)
    {
        try {
            $empresa = Empresa::thisEmpresa();

            $personal = Personal::join('sucursales', 'sucursales.id', '=', 'personals.id_sucursal')
                ->select('personals.*')
                ->where('sucursales.status', true)
                ->where('personals.status', true)
                ->where('sucursales.nombres', $request->input('sucursal'))
                ->where('sucursales.id_empresa', $empresa->id)
                ->orderby('id', 'ASC')
                ->get();

            return response()->json([
                'records' => $personal,
                'status' => true,
                'message' => 'Listado correctamente.',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'records' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function store(PersonalRequest $request)
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

            $personal = new Personal($request->all());
            $image_path = $request->file('foto')->store('imagenes', 'public');
            $personal->foto = "/storage/{$image_path}";
            $personal->id_sucursal = $sucursal->id;
            $personal->status = true;
            $personal->save();

            return response()->json([
                'record' => $personal,
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


    public function update(PersonalRequest $request)
    {
        try {

            $personal = Personal::where('status', true)->where('id', $request->input('id'))->first();
            $personal->fill($request->except('foto'));
            //verificar si subio una nueva imagen
            if ($request->file('foto') != null) {
                Storage::disk('public')->delete(str_replace("/storage", "", $personal->foto));
                $image_path = $request->file('foto')->store('imagenes', 'public');
                $personal->foto = "/storage/{$image_path}";
            }
            $personal->update();

            return response()->json([
                'record' => $personal,
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
    } //update


    public function destroy(Request $request)
    {
        try {
            $personal = Personal::where('status', true)->where('id', $request->input('id'))->first();
            $personal->status = false;
            $personal->update();

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

    public function recordByCi(Request $request)
    {
        try {
            $personal = Personal::join('sucursales', 'sucursales.id', '=', 'personals.id_sucursal')
                ->select(
                    'personals.id',
                    'personals.nombres',
                    'personals.apellido_paterno',
                    'personals.apellido_materno',
                )
                ->where('personals.status', true)
                ->where('sucursales.status', true)
                ->where('personals.ci', $request->input('ci'))
                ->where('sucursales.nombres', $request->input('sucursal'))
                ->first();

            if ($personal == null) {
                return response()->json([
                    'record' => null,
                    'message' => "No se encontro ningun personal con CI {$request->input('ci')}, verifique si el personal
                                  se encuentra registrado en el sistema y/o el personal no pertence a la sucursal
                                   {$request->input('sucursal')}.",
                    'status' => false,
                ], 200);
            }

            return response()->json([
                'record' => $personal,
                'message' => "Se encontro un registro con CI {$request->input('ci')}.",
                'status' => true,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'record' => null,
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
}//class
