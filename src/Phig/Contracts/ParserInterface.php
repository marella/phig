<?php

namespace Phig\Contracts;

interface ParserInterface
{
    /**
     * Parse a file and return its contents.
     *
     * @param string $path
     *
     * @return mixed
     */
    public function parse($path);
}
