<?php

namespace Dingo\Api\Provider;

use Dingo\Api\Mcp\Mcp;
use Dingo\Api\Mcp\Transport\HttpTransport;

class McpServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerMcp();
    }

    /**
     * Register the MCP instance.
     *
     * @return void
     */
    protected function registerMcp()
    {
        $this->app->singleton('api.mcp', function ($app) {
            $transports = [];

            // Get MCP configuration
            $config = $this->config('mcp', []);

            // Register configured transports
            if (isset($config['transports']) && is_array($config['transports'])) {
                foreach ($config['transports'] as $name => $transportConfig) {
                    if (isset($transportConfig['type']) && $transportConfig['type'] === 'http') {
                        $transports[$name] = new HttpTransport(
                            $name,
                            $transportConfig['url'],
                            $transportConfig['options'] ?? []
                        );
                    }
                }
            }

            return new Mcp($app, $transports);
        });

        // Register alias
        $this->app->alias('api.mcp', Mcp::class);
    }
}
