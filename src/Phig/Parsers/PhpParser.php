<?php

namespace Phig\Parsers;

use Exception;
use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ParserException;

class PhpParser implements ParserInterface
{
    /**
     * Get the return value of a PHP file.
     *
     * @param string $path
     *
     * @throws \Phig\Exceptions\ParserException
     *
     * @return mixed
     */
    public function parse($path)
    {
        try {
            return require $path;
        } catch (Exception $e) {
            throw new ParserException($path, ParserException::PHP, $e);
        }
    }
}
