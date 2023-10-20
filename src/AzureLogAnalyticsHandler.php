<?php
namespace Gvod\AzureLogger;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

use Illuminate\Support\Facades\Log;

class AzureLogAnalyticsHandler extends AbstractProcessingHandler
{
    protected $workspaceId;
    protected $sharedKey;
    protected $logType;

    public function __construct($workspaceId, $sharedKey, $logType = "MyCustomLog", $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->workspaceId = $workspaceId;
        $this->sharedKey = $sharedKey;
        $this->logType = $logType;
    }

    protected function write(array $record): void
    {
        $data = [
            'TimeGenerated' => $record['datetime']->format('Y-m-d\TH:i:s.uO'),
            'level' => $record['level_name'],
            'message' => $record['message'],
        ];
    
        $jsonData = json_encode([$data]); // Ensure it's an array of log entries for LA's expectations

        $date = gmdate('D, d M Y H:i:s T', time());
        $stringToSign = "POST\n" . strlen($jsonData) . "\napplication/json\n" . "x-ms-date:" . $date . "\n/api/logs";
        $signature = base64_encode(
            hash_hmac('sha256', $stringToSign, base64_decode($this->sharedKey), true)
        );

        $authHeader = "SharedKey " . $this->workspaceId . ":" . $signature;

        $ch = curl_init('https://' . $this->workspaceId . '.ods.opinsights.azure.com/api/logs?api-version=2016-04-01');
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $authHeader,
            'Log-Type: ' . $this->logType,
            'x-ms-date: ' . $date,
            'time-generated-field: TimeGenerated',
            'Content-Type: application/json',
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            error_log('AzureLogAnalyticsHandler CURL error: ' . curl_error($ch));
        } else if ($result !== '') {
            // Log Analytics specific error output
            error_log('AzureLogAnalyticsHandler error: ' . $result);
        }

        curl_close($ch);
    }
}
