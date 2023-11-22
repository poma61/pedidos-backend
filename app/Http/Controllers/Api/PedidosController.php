<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PedidosRequest;
use App\Http\Requests\PersonalRequest;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Pedido;
use App\Models\Sucursal;
use COM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PedidosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $empresa = Empresa::thisEmpresa();
            $cliente = Cliente::leftJoin('pedidos', 'pedidos.id_cliente', '=', 'clientes.id')
                ->join('sucursales', 'sucursales.id', '=', 'clientes.id_sucursal')
                ->select(
                    'clientes.id',
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'clientes.email',
                    DB::raw('COUNT(pedidos.id_cliente) as cantidad_pedidos'),
                )
                ->where('clientes.status', true)
                ->where('sucursales.status', true)
                // ->where('pedidos.status', true) => si colocamos esta condicion no mostrara ni un registro registro, 
                //entonces aunque el pedido se haya eliminado se contara cuantos pedidos iso el cliente
                ->where('sucursales.id_empresa', $empresa->id)
                ->where('sucursales.nombres', $request->input('sucursal'))
                //group by para agrupar los clientes y poder contar pedidos.id_cliente
                ->groupBy('clientes.id', 'clientes.nombres', 'clientes.apellido_paterno', 'clientes.apellido_materno','clientes.email')
                ->orderBy('clientes.id', 'DESC')
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


    public function store(PedidosRequest $request)
    {
        try {
            $sucursal = Sucursal::where('status', true)
                ->where('nombres', $request->input('sucursal'))
                ->first();
            //debemos verificar si la sucursal existe en la base de datos por seguridad 
            if ($sucursal == null) {
                return response()->json([
                    'record' => $sucursal,
                    'status' => false,
                    'message' => 'La sucursal no existe!',
                ], 200);
            }

            $pedido = new Pedido($request->all());
            $pedido->status = true;
            $pedido->save();


            return response()->json([
                'record' => $pedido,
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
            //no es necesario verificar la sucursal porque estamos editando un registro existente 
            $pedido = Pedido::where('status', true)
                ->where('id', $request->input('id'))
                ->first();

            $pedido->update($request->all());
            return response()->json([
                'record' => $pedido,
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
            $pedido = Pedido::where('status', true)
                ->where('id', $request->input('id'))
                ->first();
            $pedido->status = false;
            $pedido->update();
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
