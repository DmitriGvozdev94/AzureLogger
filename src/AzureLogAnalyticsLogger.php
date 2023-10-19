<?php

namespace Gvod\AzureLogger;

use Monolog\Logger;

class AzureLogAnalyticsLogger
{
    public function __invoke(array $config)
    {
        $level = Logger::toMonologLevel($config['level'] ?? 'debug');
        $logLevel = $config['logLevel'] ?? 'MyCustomLog';

        return new Logger('azure', [
            new AzureLogAnalyticsHandler($config['workspaceId'], $config['sharedKey'], $level, $logLevel),
        ]);
    }
}
