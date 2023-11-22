<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SucursalRequest;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $empresa = Empresa::join('sucursales', 'sucursales.id_empresa', '=', 'empresas.id')
                ->join('personals', 'personals.id_sucursal', '=', 'sucursales.id')
                ->join('users', 'users.id_personal', '=', 'personals.id')
                ->select('empresas.*')
                ->where('users.id', Auth::user()->id)->first();

            $sucursal = Sucursal::where('status', true)->orderBy('id', 'ASC')->where('id_empresa', $empresa->id)->get();
            return response()->json([
                'records' => $sucursal,
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(SucursalRequest $request)
    {
        try {
            $empresa = Empresa::join('sucursales', 'sucursales.id_empresa', '=', 'empresas.id')
                ->join('personals', 'personals.id_sucursal', '=', 'sucursales.id')
                ->join('users', 'users.id_personal', '=', 'personals.id')
                ->select('empresas.*')
                ->where('users.id', Auth::user()->id)->first();

            $sucursal = new Sucursal($request->all());
            $sucursal->status = true;
            $sucursal->id_empresa = $empresa->id;
            $sucursal->save();

            return response()->json([
                'status' => true,
                'record' => $sucursal,
                'message' => 'Registro creado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => null,
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(SucursalRequest $request)
    {
        try {
            $sucursal = Sucursal::where('status', true)->where('id', $request->input('id'))->first();
            $sucursal->update($request->all());

            return response()->json([
                'status' => true,
                'record' => $sucursal,
                'message' => 'Registro modificado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => null,
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {

            $sucursal = Sucursal::where('status', true)->where('id', $request->input('id'))->first();
            $sucursal->status = false;
            $sucursal->update();

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
