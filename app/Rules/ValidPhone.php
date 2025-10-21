<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPhone implements Rule
{
    public function passes($attribute, $value)
    {
        // basic international phone validation (E.164-ish)
        return is_string($value) && preg_match('/^\+?\d{7,15}$/', $value);
    }

    public function message()
    {
        return 'Le numéro de téléphone est invalide.';
    }
}
