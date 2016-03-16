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
    const INI = 3;
    const XML = 4;
    const YAML = 5;
}
