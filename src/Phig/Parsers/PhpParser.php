<?php

namespace Phig\Parsers;

use Phig\Contracts\ParserInterface;

class PhpParser implements ParserInterface
{
    /**
     * Get the return value of a PHP file.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function parse($path)
    {
        return require $path;
    }
}
