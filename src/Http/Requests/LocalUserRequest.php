<?php

namespace Uspdev\SenhaunicaSocialite\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class LocalUserRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'O campo senha precisa ter pelos menos :min caracteres.',
            'password.letters' => 'Sua senha deve conter pelo menous uma letra.',
            'password.mixed_case' => 'Sua senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'Sua senha deve conter pelo menos um número.',
            'password.symbols' => 'Sua senha deve conter pelo menos um símbolo.',
            'password.uncompromised' => 'Esta senha foi divulgada em um vazamento de dados público e não pode ser usada.',
        ];
    }
}
