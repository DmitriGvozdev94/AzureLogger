<?php

namespace Gvod\AzureLogger;

use Illuminate\Support\ServiceProvider;
use Monolog\Logger;

class AzureLogAnalyticsLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('azure.logger', function ($app) {
            $config = $app['config']->get('logging.channels.azure');

            $level = Logger::toMonologLevel($config['level'] ?? 'debug');
            $logType = $config['logType'] ?? 'MyCustomLog';

            return new Logger('azure', [
                new AzureLogAnalyticsHandler(
                    $config['workspaceId'], 
                    $config['sharedKey'], 
                    $level, 
                    $logType
                ),
            ]);
        });
    }
}