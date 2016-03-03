<?php

namespace Phig;

use BadMethodCallException;

class ReadConfig extends Config
{
    /**
     * Cache of the configuration items.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        if (!isset($this->cache['has'][$key])) {
            $this->cache['has'][$key] = parent::has($key);
        }

        return $this->cache['has'][$key];
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!isset($this->cache['get'][$key])) {
            $this->cache['get'][$key] = parent::get($key);
        }

        return $this->cache['get'][$key];
    }

    /**
     * Set a given configuration value.
     *
     * @param array|string $key
     * @param mixed        $value
     *
     * @throws \BadMethodCallException always
     */
    public function set($key, $value = null)
    {
        throw new BadMethodCallException('Write operation on ReadConfig is not allowed.');
    }
}
