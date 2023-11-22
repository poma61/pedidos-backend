<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;


class PedidosRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'referencia_domiciliaria' => 'required',
            'direccion'  => 'required',
            'factura_fiscal'  => 'required',
            'id_cliente'  => 'required|numeric',
        ];
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {

        throw  new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Veriricar los campos!',
                'message_errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
