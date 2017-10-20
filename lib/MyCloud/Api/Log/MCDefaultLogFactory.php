<?php

namespace MyCloud\Api\Log;

use Psr\Log\LoggerInterface;

/**
 * Class MCDefaultLogFactory
 *
 * This factory is the default implementation of Log factory.
 *
 * @package MyCloud\Api\Log
 */
class MCDefaultLogFactory implements MCLogFactory
{
    /**
     * Returns logger instance implementing LoggerInterface.
     *
     * @param string $className
     * @return LoggerInterface instance of logger object implementing LoggerInterface
     */
    public function getLogger($className)
    {
        return new MCLogger($className);
    }
}
