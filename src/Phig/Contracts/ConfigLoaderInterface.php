<?php

namespace Phig\Contracts;

interface ConfigLoaderInterface
{
    /**
     * Load config from specified paths and return a Config instance.
     *
     * @param array|string $paths
     * @param array|string $optional
     *
     * @return \Phig\Contracts\ConfigInterface
     */
    public function load($paths, $optional = []);

    /**
     * Load config from specified paths and return a read-only Config instance.
     *
     * @param array|string $paths
     * @param array|string $optional
     *
     * @return \Phig\Contracts\ConfigInterface
     */
    public function read($paths, $optional = []);

    /**
     * Set parser for a file extension.
     *
     * @param string   $extension
     * @param callable $parser
     */
    public function setParser($extension, callable $parser);

    /**
     * Get parser for a file extension.
     *
     * @param string $extension
     *
     * @return \Phig\Contracts\ParserInterface
     */
    public function getParser($extension);
}
