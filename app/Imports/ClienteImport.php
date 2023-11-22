<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class ClienteImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading //
{

    private $missing_attributes;

    public function __construct(array $missing_attributes)
    {
        $this->missing_attributes = $missing_attributes;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        return new Cliente([
            'nombres' => $row['nombres'],
            'apellido_paterno' => $row['apellido_paterno'],
            'apellido_materno' => $row['apellido_materno'],
            'n_contacto' => $row['numero_de_contacto'],
            'email' => $row['correo_electronico'],
            'id_sucursal' =>  $this->missing_attributes['id_sucursal'],
            'status' =>  $this->missing_attributes['status'],
        ]);
    } //model


    public function rules(): array
    {
        return [
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'apellido_materno' => 'required',
            'numero_de_contacto' => 'required|numeric',
            'correo_electronico' => [
                'required',
                Rule::unique('clientes', 'email')->where(function ($query) {
                    $query->where('status', true);
                }),
            ],
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 100;
    }
}//class
