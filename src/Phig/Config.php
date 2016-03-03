<?php

namespace Phig;

use ArrayAccess;
use InvalidArgumentException;
use Phig\Contracts\ConfigInterface;
use Phig\Support\Arr;
use UnexpectedValueException;

class Config implements ArrayAccess, ConfigInterface
{
    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @throws \InvalidArgumentException when key is null
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException('key should not be null');
        }

        return Arr::get($this->items, $key, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param array|string $key
     * @param mixed        $value
     *
     * @throws \InvalidArgumentException when key is null
     */
    public function set($key, $value = null)
    {
        if (is_null($key)) {
            throw new InvalidArgumentException('key should not be null');
        }

        if (is_array($key)) {
            foreach ($key as $innerKey => $innerValue) {
                Arr::set($this->items, $innerKey, $innerValue);
            }
        } else {
            Arr::set($this->items, $key, $value);
        }
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \UnexpectedValueException when value of key is not array
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        if (!is_array($array)) {
            throw new UnexpectedValueException("$key is not array");
        }

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws \UnexpectedValueException when value of key is not array
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        if (!is_array($array)) {
            throw new UnexpectedValueException("$key is not array");
        }

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Get all of the configuration items for the application.
     *
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param string $key
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }
}
