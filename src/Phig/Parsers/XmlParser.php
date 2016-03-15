<?php

namespace Phig\Parsers;

use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ParserException;

class XmlParser implements ParserInterface
{
    /**
     * Parse an XML file and return the data.
     *
     * @param string $path
     *
     * @throws \Phig\Exceptions\ParserException
     *
     * @return mixed
     */
    public function parse($path)
    {
        $old = libxml_use_internal_errors(true);

        $data = simplexml_load_file($path);

        $error = libxml_get_last_error();
        libxml_use_internal_errors($old);

        if ($data === false) {
            if ($error) {
                $error = "Error in {$error->file} on line {$error->line} at column {$error->column}: {$error->message}";
            }

            throw new ParserException("$path: $error", ParserException::XML);
        }

        $data = json_decode(json_encode($data), true);

        return $data;
    }
}
