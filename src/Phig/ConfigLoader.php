<?php

namespace Phig;

use Phig\Contracts\ConfigLoaderInterface;
use Phig\Contracts\ParserInterface;
use Phig\Exceptions\ExtensionNotFoundException;
use Phig\Exceptions\FileNotFoundException;
use Phig\Exceptions\UnsupportedExtensionException;
use Phig\Parsers\PhpParser;
use Phig\Support\Arr;
use UnexpectedValueException;

class ConfigLoader implements ConfigLoaderInterface
{
    /**
     * Parsers for different file extensions.
     *
     * @var array
     */
    protected $parsers = [];

    public function __construct()
    {
        $this->registerParsers();
    }

    /**
     * Load config from specified paths and return a Config instance.
     *
     * @param array|string $paths
     * @param array|string $optional
     *
     * @return \Phig\Config
     */
    public function load($paths, $optional = [])
    {
        $config = $this->loadPaths($paths, $optional);

        return new Config($config);
    }

    /**
     * Load config from specified paths and return a read-only Config instance.
     *
     * @param array|string $paths
     * @param array|string $optional
     *
     * @return \Phig\ReadConfig
     */
    public function read($paths, $optional = [])
    {
        $config = $this->loadPaths($paths, $optional);

        return new ReadConfig($config);
    }

    /**
     * Set parser for a file extension.
     *
     * @param string   $extension
     * @param callable $parser    Callable which returns a \Phig\Contracts\ParserInterface instance
     */
    public function setParser($extension, callable $parser)
    {
        $this->parsers[$extension] = $parser;
    }

    /**
     * Get parser for a file extension.
     *
     * @param string $extension
     *
     * @throws \UnexpectedValueException                      when callable does not return
     *                                                        a \Phig\Contracts\ParserInterface instance
     * @throws \Phig\Exceptions\UnsupportedExtensionException when parser is not set for extension
     *
     * @return \Phig\Contracts\ParserInterface
     */
    public function getParser($extension)
    {
        if (isset($this->parsers[$extension])) {
            if (is_callable($this->parsers[$extension])) {
                $parser = call_user_func($this->parsers[$extension]);

                if (!$parser instanceof ParserInterface) {
                    throw new UnexpectedValueException("Parser for $extension is not of type ".ParserInterface::class);
                }

                $this->parsers[$extension] = $parser;
            }

            return $this->parsers[$extension];
        }

        throw new UnsupportedExtensionException($extension);
    }

    /**
     * Register default parsers.
     */
    protected function registerParsers()
    {
        $this->setParser('php', function () {
            return new PhpParser();
        });
    }

    /**
     * Resolve file paths.
     *
     * @param array|string $paths
     *
     * @return array
     */
    protected function getPaths($paths)
    {
        if (is_array($paths)) {
            return $paths;
        }

        if (is_dir($paths)) {
            return glob("$paths/*");
        }

        return (array) $paths;
    }

    /**
     * Load config from file paths.
     *
     * @param array|string $paths
     * @param array|string $optional
     *
     * @return array Config array
     */
    protected function loadPaths($paths, $optional = [])
    {
        $paths = $this->getPaths($paths);
        $optional = $this->getPaths($optional);

        $config = [];

        foreach ($paths as $path) {
            $config = $this->loadPath($path, $config);
        }

        foreach ($optional as $path) {
            if (is_file($path)) {
                $config = $this->loadPath($path, $config);
            }
        }

        return $config;
    }

    /**
     * Load config from a single file and append it to config.
     *
     * @param string $path
     * @param array  $config
     *
     * @throws \Phig\Exceptions\FileNotFoundException      when path is not a file
     * @throws \Phig\Exceptions\ExtensionNotFoundException when path does not have an extension
     * @throws \UnexpectedValueException                   when file contents can not be resolved to an array
     *
     * @return array Updated config array
     */
    protected function loadPath($path, array $config)
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        if (strpos($path, '.') === false) {
            throw new ExtensionNotFoundException($path);
        }

        $parts = pathinfo($path);
        $extension = $parts['extension'];
        $filename = $parts['filename'];

        $contents = $this->getParser($extension)->parse($path);

        // treat values inside hidden files as "globals"
        if (!empty($filename) && $filename[0] !== '.') {
            $contents = [$filename => $contents];
        }

        if (!is_array($contents)) {
            throw new UnexpectedValueException("Config is not an array: $path");
        }

        $contents = Arr::dot($contents);
        foreach ($contents as $key => $value) {
            Arr::set($config, $key, $value);
        }

        return $config;
    }
}
