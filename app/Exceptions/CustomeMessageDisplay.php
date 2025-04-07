<?php

namespace App\Exceptions;

use Exception;

class CustomeMessageDisplay extends Exception
{
    public function __construct($message)
    {
        parent::__construct($message);
    }

    // Optionally, customize the report method if needed
    public function report()
    {
        // Log the error or perform any reporting actions
    }

    // Customize the render method to display a user-friendly message
    public function render($request)
    {
        return response()->view('errors.custom', ['message' => $this->getMessage()], 400);
    }
}
