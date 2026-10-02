<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CrearAsuntoRequest extends FormRequest
{

    protected $redirectRoute = 'asuntos.create';
    
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => [
                'required',
                'string',
                'min:3',
                'max:150',
                'unique:asuntos,titulo',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'confirmed',
            ],

            'expira_at' => [
                'required',
                'date',
                'after:now',
            ],

            'mensaje' => [
                'required',
                'string',
                'min:1',
                'max:10000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El asunto es obligatorio.',
            'titulo.min' => 'El asunto debe tener al menos 3 caracteres.',
            'titulo.max' => 'El asunto no puede superar los 150 caracteres.',
            'titulo.unique' => 'Ese asunto ya existe.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',

            'expira_at.required' => 'La fecha de expiración es obligatoria.',
            'expira_at.after' => 'La fecha de expiración debe ser posterior a la fecha actual.',

            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.max' => 'El mensaje no puede superar los 10.000 caracteres.',
        ];
    }
}
