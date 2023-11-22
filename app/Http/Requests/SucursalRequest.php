<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class SucursalRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'nombres' => [
                'required',
                //aplicar la validacion unique cuando el campo status este en true
                //aplicamos el ignore cuando sea un update ya que el 'nombres'  es el mismo porque es una actualizacion del registro
                Rule::unique('sucursales')->where(function ($query) {
                    $query->where('status', true);
                })->ignore($this->input('id')),
            ],
            'direccion' => 'required',
            'n_contacto' => 'required',
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
}//class
