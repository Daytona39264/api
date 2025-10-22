<?php

namespace Dingo\Api\Mcp\Transport;

use Dingo\Api\Contract\Mcp\Transport;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HttpTransport implements Transport
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * The MCP server URL.
     *
     * @var string
     */
    protected $url;

    /**
     * The transport name/identifier.
     *
     * @var string
     */
    protected $name;

    /**
     * Connection state.
     *
     * @var bool
     */
    protected $connected = false;

    /**
     * Additional headers for requests.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Create a new HTTP transport instance.
     *
     * @param string $name
     * @param string $url
     * @param array  $options
     */
    public function __construct($name, $url, array $options = [])
    {
        $this->name = $name;
        $this->url = $url;
        $this->headers = $options['headers'] ?? [];

        $this->client = new Client([
            'base_uri' => $url,
            'timeout' => $options['timeout'] ?? 30,
            'verify' => $options['verify'] ?? true,
        ]);
    }

    /**
     * Connect to the MCP server and establish a session.
     *
     * @return mixed
     */
    public function connect()
    {
        try {
            // Test connection with a ping or initialization request
            $response = $this->client->get('', [
                'headers' => array_merge([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ], $this->headers),
            ]);

            $this->connected = $response->getStatusCode() === 200;

            return $this->connected;
        } catch (GuzzleException $e) {
            $this->connected = false;
            throw new \RuntimeException(
                "Failed to connect to MCP server [{$this->name}] at {$this->url}: {$e->getMessage()}"
            );
        }
    }

    /**
     * Send a request to the MCP server.
     *
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     */
    public function send($method, array $params = [])
    {
        if (!$this->connected) {
            $this->connect();
        }

        try {
            $response = $this->client->post('', [
                'headers' => array_merge([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ], $this->headers),
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => $method,
                    'params' => $params,
                    'id' => uniqid('mcp_', true),
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['error'])) {
                throw new \RuntimeException(
                    "MCP server error: {$body['error']['message']} (code: {$body['error']['code']})"
                );
            }

            return $body['result'] ?? null;
        } catch (GuzzleException $e) {
            throw new \RuntimeException(
                "Failed to send request to MCP server [{$this->name}]: {$e->getMessage()}"
            );
        }
    }

    /**
     * Close the connection to the MCP server.
     *
     * @return void
     */
    public function disconnect()
    {
        $this->connected = false;
    }

    /**
     * Check if the transport is connected.
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->connected;
    }

    /**
     * Get the transport name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the transport URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
