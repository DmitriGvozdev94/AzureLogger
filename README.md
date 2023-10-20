
# Add to Laravel Project

## Add to composer.json
```
...,
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/DmitriGvozdev94/AzureLogger.git"
    }
],
...
```

## Add via composer newpkg from git
`composer require gvod/azurelogger:master`

## Add to .env
```
AZURE_WORKSPACE_ID=workspace_id_from_azure
AZURE_SHARED_KEY=workspace_key_from_azure
```

## Add to config/logging.php

```
'channels' => [
    ...
    'azure' => [
        'driver' => 'custom',
        'via' => Gvod\AzureLogger\AzureLogAnalyticsLogger::class,
        'level' => 'debug',
        'workspaceId' => env('AZURE_WORKSPACE_ID'),
        'sharedKey' => env('AZURE_SHARED_KEY'),
        'logType' => 'MyCustomLog',
    ],
    ...
]
```

## Use somewhere
`Log::channel('azure')->info('This is an info log for Azure Log Analytics.');`

