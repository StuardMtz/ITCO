<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'encargado'=>'required',
            'sucursal'=>'required',
            'bodega'=>'required',
        ];
    }

    public function messages()//Funcion creada para generar el mensaje de error, al momento de generar un nuevo registro.
    {
    return[

        'encargado.required' => 'Ingrese el nombre del encargado.',
        'sucursal.required' => 'Ingrese el número de la sucursal.',
        'bodega.required' => 'Ingrese el número de bodega.',
    ];
    }
}
