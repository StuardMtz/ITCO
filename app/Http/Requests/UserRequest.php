<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'=>'required',
            'email'=>'required|string|email|max:255|unique:users',
            'sucursal'=>'required|numeric',
            'bodega'=>'required|numeric',
            'roles'=>'required|string',
            'password'=>'required|string|min:6',
        ];
    }

    public function messages()//Funcion creada para generar el mensaje de error, al momento de generar un nuevo registro.
    {
    return[

        'name.required' => 'Ingrese un nombre para el suario.',
        'email.required'=>'Ingrese una dirección de correo',
        'email.email'=>'La dirección de correo no es valida',
        'email.unique:users'=>'Ya existe esta dirección',
        'sucursal.required'=>'Ingrese el número de la sucursal.',
        'bodega.required'=>'Seleccione una bodega.',
        'roles.required'=>'Seleccione un rol para el usuario',
        'password.required'=>'Coloque una contraseña',
        'password.min:6'=>'Ingrese una contraseña con más de 6 caracteres',
    ];
    }
}
