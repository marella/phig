<?php

namespace Phig\Parsers;

use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ParserException;

class JsonParser implements ParserInterface
{
    /**
     * Decode a JSON file and return the data as an array.
     *
     * @param string $path
     *
     * @throws \Phig\Exceptions\ParserException
     *
     * @return mixed
     */
    public function parse($path)
    {
        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParserException("$path: ".json_last_error_msg(), ParserException::JSON);
        }

        return $data;
    }
}
