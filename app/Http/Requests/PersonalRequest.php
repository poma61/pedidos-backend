<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
//add
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class PersonalRequest extends FormRequest
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


        $rules = [
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'cargo' => 'required',
            'ci' => [
                'required',
                //debemos utilizar esta consulta personalizada porque hay la posibilidad de que se registre
                //el mismo personal con ci.. pero en diferentes empresas
                function ($attribute, $value, $fail,) {
                    $empresa = Empresa::thisEmpresa();
                    $exists = DB::table('personals')
                        ->join('sucursales', 'personals.id_sucursal', '=', 'sucursales.id')
                        ->join('empresas', 'sucursales.id_empresa', '=', 'empresas.id')
                        ->where('personals.ci', $value)
                        ->where('personals.status', true)
                        ->where('empresas.id', $empresa->id)
                        ->whereNotIn('personals.id', [$this->input('id')]) // excluir el ID especificado (<>)
                        ->exists(); // Comprobar si la consulta devuelve algún resultado

                    if ($exists) {
                        $fail('El campo ci ya ha sido tomado.');
                    }
                },
            ],
            'ci_expedido' => 'required',
            'n_contacto' => 'required|numeric',
            'direccion' => 'required',
            'email' => 'required|email',
        ];
        if ($this->isMethod('PUT')) {
            $rules['foto'] = 'sometimes|mimes:jpeg,png,jpg';
        } else {
            $rules['foto'] = 'required|mimes:jpeg,png,jpg';
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator): HttpResponseException
    {
        $response = [
            'status' => false,
            'message' => 'Verificar los campos!',
            'message_errors' => $validator->errors(),
        ];
        throw  new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
