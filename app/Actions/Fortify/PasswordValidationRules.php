<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Regras de validacao de senha (politica forte).
     *
     * Exige no minimo 10 caracteres, com maiusculas e minusculas, numeros e
     * simbolos, e verifica se a senha ja vazou em vazamentos conhecidos
     * (HaveIBeenPwned). Combinado com o hash Argon2id, protege as senhas.
     *
     * @return array
     */
    protected function passwordRules()
    {
        return [
            'required',
            'string',
            'confirmed',
            Password::min(10)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(),
        ];
    }
}
