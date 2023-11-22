<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{

    public function  login(Request $request)
    {
        try {
            $credentials = ['usuario' => $request->input('usuario'), 'password' => $request->input('password'), 'status' => true];
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'access_token' => null,
                    'message' => 'Usuario y/o contraseña incorrectos.',
                    'status' => false,
                ], 200);
            }
            return $this->respondWithToken($token);
        } catch (Throwable $th) {
            return response()->json([
                'access_token' => null,
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }

    public function respondWithToken($token)
    {
        return response()->json([
            "access_token" => $token,
            "token_type" => "Bearer",
            "message" => "Sesion iniciada.",
            "expires_in" => Auth::factory()->getTTL(),
            "status" => true,
        ]);
    }

    public function logout()
    {
        try {
            Auth::logout();

            return response()->json([
                'message' => 'Successfully logged out',
                'status' => true,
            ], 200);
        } catch (Throwable $th) {

            return response()->json([
                'message' => $th->getMessage(),
                'status' => false,
            ], 500);
        }
    }
    public function me()
    {
        try {
            return response()->json(
                [
                    'user' => Auth::user(),
                    'status' => true,
                    'message' => 'OK',
                ]

            );
        } catch (Throwable $th) {
            return response()->json([
                'user' => null,
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}//class
