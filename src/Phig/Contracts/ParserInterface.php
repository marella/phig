<?php

namespace Phig\Contracts;

interface ParserInterface
{
    /**
     * Parse a file and return its contents.
     *
     * @param string $path
     *
     * @throws \Phig\Exceptions\ParserException
     *
     * @return mixed
     */
    public function parse($path);
}
