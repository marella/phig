<?php

namespace Phig\Parsers;

use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ParserException;

class IniParser implements ParserInterface
{
    /**
     * Parse an INI file and return the data.
     *
     * @param string $path
     *
     * @throws \Phig\Exceptions\ParserException
     *
     * @return array
     */
    public function parse($path)
    {
        $data = @parse_ini_file($path, true);

        if ($data === false) {
            $error = error_get_last();
            throw new ParserException("$path: $error[message]", ParserException::INI);
        }

        return $data;
    }
}
