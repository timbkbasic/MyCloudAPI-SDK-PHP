<?php

namespace MyCloud\Api\Log;

use MyCloud\Api\Core\MCConfigManager;
use Psr\Log\LoggerInterface;

/**
 * Simple Logging Manager.
 * This does an error_log for now
 * Potential frameworks to use are PEAR logger, log4php from Apache
 */
class MCLoggingManager
{
    /**
     * @var array of logging manager instances with class name as key
     */
    private static $instances = array();

    /**
     * The logger to be used for all messages
     *
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Logger Name
     *
     * @var string
     */
    private $loggerName;

    /**
     * Returns the singleton object
     *
     * @param string $loggerName
     * @return $this
     */
    public static function getInstance($loggerName = __CLASS__)
    {
        if (array_key_exists($loggerName, MCLoggingManager::$instances)) {
            return MCLoggingManager::$instances[$loggerName];
        }
        $instance = new self($loggerName);
        MCLoggingManager::$instances[$loggerName] = $instance;
        return $instance;
    }

    /**
     * Default Constructor
     *
     * @param string $loggerName Generally represents the class name.
     */
    private function __construct($loggerName)
    {
        $config = MCConfigManager::getInstance()->getConfigHashmap();
        // Checks if custom factory defined, and is it an implementation of @PayPalLogFactory
        $factory = array_key_exists('log.AdapterFactory', $config) &&
			in_array('MyCloud\Api\Log\MCLogFactory', class_implements($config['log.AdapterFactory'])) ?
				$config['log.AdapterFactory'] : '\MyCloud\Api\Log\MCDefaultLogFactory';
        /** @var MCLogFactory $factoryInstance */
        $factoryInstance = new $factory();
        $this->logger = $factoryInstance->getLogger($loggerName);
        $this->loggerName = $loggerName;
    }

    /**
     * Log Error
     *
     * @param string $message
     */
    public function error($message)
    {
        $this->logger->error($message);
    }

    /**
     * Log Warning
     *
     * @param string $message
     */
    public function warning($message)
    {
        $this->logger->warning($message);
    }

    /**
     * Log Info
     *
     * @param string $message
     */
    public function info($message)
    {
        $this->logger->info($message);
    }

    /**
     * Log Fine
     *
     * @param string $message
     */
    public function fine($message)
    {
        $this->info($message);
    }

    /**
     * Log Debug
     *
     * @param string $message
     */
    public function debug($message)
    {
        $config = MCConfigManager::getInstance()->getConfigHashmap();
        // Disable debug in live mode.
		// REVIEW
        if ( array_key_exists('mode', $config) && $config['mode'] != 'live' ) {
            $this->logger->debug($message);
        }
    }
}
