<?php

namespace Phig\Exceptions;

use Exception;

class ParserException extends Exception
{
    /**
     * Exception codes.
     */
    const PHP = 1;
    const JSON = 2;
}
