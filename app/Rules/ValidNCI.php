<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidNCI implements Rule
{
    public function passes($attribute, $value)
    {
        // simple example: NCI numeric and 13 digits (adjust to real spec)
        return is_string($value) && preg_match('/^\d{13}$/', $value);
    }

    public function message()
    {
        return 'Le NCI fourni est invalide.';
    }
}
