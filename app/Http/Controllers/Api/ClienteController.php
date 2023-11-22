<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClienteRequest;
use App\Imports\ClienteImport;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Maatwebsite\Excel\Facades\Excel;


class ClienteController extends Controller
{
    public function index(Request $request)
    {
        try {
            $empresa = Empresa::thisEmpresa();
            $cliente = Cliente::join('sucursales', 'sucursales.id', '=', 'clientes.id_sucursal')
                ->select(
                    'clientes.*',
                )
                ->where('clientes.status', true)
                ->where('sucursales.status', true)
                ->where('sucursales.id_empresa', $empresa->id)
                ->where('sucursales.nombres', $request->input('sucursal'))
                ->orderBy('id', 'ASC')
                ->get();

            return response()->json([
                'records' => $cliente,
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


    public function store(ClienteRequest $request)
    {
        try {
            $sucursal = Sucursal::where('status', true)
                ->where('nombres', $request->input('sucursal'))
                ->first();
            //debemos verificar si la sucursal existe en la base de datos por seguridad 
            //y para crear el nuevo registro
            if ($sucursal == null) {
                return response()->json([
                    'record' => null,
                    'status' => false,
                    'message' => 'La sucursal no existe!',
                ], 200);
            }

            $cliente = new Cliente($request->all());
            $cliente->id_sucursal = $sucursal->id;
            $cliente->status = true;
            $cliente->save();


            return response()->json([
                'record' => $cliente,
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



    public function update(ClienteRequest $request)
    {
        try {
            //no es necesario verificar la sucursal porque el estamos editando un registro existente 
            $cliente = Cliente::where('status', true)
                ->where('id', $request->input('id'))
                ->first();

            $cliente->update($request->all());
            return response()->json([
                'record' => $cliente,
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
            //no es necesario verificar la sucursal porque el estamos editando un registro existente 
            $cliente = Cliente::where('status', true)
                ->where('id', $request->input('id'))
                ->first();
            $cliente->status = false;
            $cliente->update();
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

    public function importRecords(Request $request)
    {
        try {
            $sucursal = Sucursal::where('status', true)
                ->where('nombres', $request->input('sucursal'))
                ->first();
            //debemos verificar si la sucursal existe en la base de datos por seguridad 
            //y para crear el nuevo registro
            if ($sucursal == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'La sucursal no existe!',
                ], 200);
            }


            if ($request->file('archivo_xlsx')) {
                
                $missing_attributes = [
                    'id_sucursal' => $sucursal->id,
                    'status' => true,
                ];
                Excel::import(new ClienteImport($missing_attributes),  $request->file('archivo_xlsx')->store('temp'));

                return response()->json([
                    'status' => true,
                    'message' => 'Carga masiva exitoso!',
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => "No se encontro ningun archivo!",
                ], 200);
            }
        } catch (\Maatwebsite\Excel\Validators\ValidationException $ex) {
            $failures = $ex->failures();
            $errors = [];
            //primera forma 
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'validation' => $failure->errors(),
                    // 'values' => $failure->values(),
                ];
            }
            return response()->json([
                'status' => false,
                'message' => 'Algunas filas no tienen datos validos, verificar el archivo!',
                'errors' => $errors,
            ], 422); // 422 es el código HTTP para errores de validación

        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}//class
