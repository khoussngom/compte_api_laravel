<?php

namespace App\Exceptions;

use App\Exceptions\ApiException;

class ClientConflictException extends ApiException
{
    public function __construct($message = 'Conflit client', $status = 409)
    {
        parent::__construct($message, $status);
    }
}
