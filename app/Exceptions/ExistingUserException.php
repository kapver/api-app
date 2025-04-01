<?php

namespace App\Exceptions;

use Exception;

class ExistingUserException extends Exception
{
    public function __construct()
    {
        parent::__construct('User with this phone or email already exists', 409);
    }
}
