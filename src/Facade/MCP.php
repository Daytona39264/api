<?php

namespace Dingo\Api\Facade;

use Illuminate\Support\Facades\Facade;

class MCP extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'api.mcp';
    }
}
