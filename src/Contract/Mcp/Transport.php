<?php

namespace Dingo\Api\Contract\Mcp;

interface Transport
{
    /**
     * Connect to the MCP server and establish a session.
     *
     * @return mixed
     */
    public function connect();

    /**
     * Send a request to the MCP server.
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function send($method, array $params = []);

    /**
     * Close the connection to the MCP server.
     *
     * @return void
     */
    public function disconnect();

    /**
     * Check if the transport is connected.
     *
     * @return bool
     */
    public function isConnected();
}
