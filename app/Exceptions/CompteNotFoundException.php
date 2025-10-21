<?php

namespace App\Exceptions;

use App\Exceptions\ApiException;

class CompteNotFoundException extends ApiException
{
    public function __construct($compteId = null)
    {
        $message = 'Le compte avec l\'ID spécifié n\'existe pas';
        parent::__construct($message, 404);
    }
}
