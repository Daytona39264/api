<?php

namespace Dingo\Api\Mcp;

use Illuminate\Container\Container;

class Mcp
{
    /**
     * Illuminate container instance.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Array of available MCP transports.
     *
     * @var array
     */
    protected $transports = [];

    /**
     * Create a new MCP instance.
     *
     * @param \Illuminate\Container\Container $container
     * @param array                           $transports
     *
     * @return void
     */
    public function __construct(Container $container, array $transports = [])
    {
        $this->container = $container;
        $this->transports = $transports;
    }

    /**
     * Get a transport by name.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \Dingo\Api\Contract\Mcp\Transport
     */
    public function transport($name)
    {
        if (!isset($this->transports[$name])) {
            throw new \InvalidArgumentException("MCP transport [{$name}] is not registered.");
        }

        return $this->transports[$name];
    }

    /**
     * Get all registered transports.
     *
     * @return array
     */
    public function getTransports()
    {
        return $this->transports;
    }

    /**
     * Register a new transport.
     *
     * @param string                              $name
     * @param \Dingo\Api\Contract\Mcp\Transport $transport
     *
     * @return void
     */
    public function addTransport($name, $transport)
    {
        $this->transports[$name] = $transport;
    }

    /**
     * Check if a transport is registered.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasTransport($name)
    {
        return isset($this->transports[$name]);
    }

    /**
     * Remove a transport.
     *
     * @param string $name
     *
     * @return void
     */
    public function removeTransport($name)
    {
        unset($this->transports[$name]);
    }

    /**
     * Extend the MCP layer with a custom transport.
     *
     * @param string          $key
     * @param object|callable $transport
     *
     * @return void
     */
    public function extend($key, $transport)
    {
        if (is_callable($transport)) {
            $transport = call_user_func($transport, $this->container);
        }

        $this->transports[$key] = $transport;
    }
}
