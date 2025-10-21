<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
    protected $status;

    public function __construct($message = 'Erreur API', $status = 400)
    {
        parent::__construct($message);
        $this->status = $status;
    }

    public function render()
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], $this->status);
    }
}
