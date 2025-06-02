<?php
require_once __DIR__ . '/config.php';
require_once 'functions.php';
// This script should send XKCD updates to all registered emails.

$logFile = __DIR__ . '/cron.log';
$logMessage = '[' . date('Y-m-d H:i:s') . '] ';

try {
    $emailsFile = __DIR__ . '/registered_emails.txt';
    if (!file_exists($emailsFile)) {
        throw new Exception("No registered emails file found.");
    }
    
    $emails = file($emailsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($emails)) {
        $logMessage .= "No subscribers found.\n";
        file_put_contents($logFile, $logMessage, FILE_APPEND);
        exit(0);
    }
    
    if (sendXKCDUpdatesToSubscribers()) {
        $logMessage .= "XKCD updates sent successfully to " . count($emails) . " subscribers.\n";
    } else {
        throw new Exception("Failed to send XKCD updates to some or all subscribers.");
    }
} catch (Exception $e) {
    $logMessage .= "ERROR: " . $e->getMessage() . "\n";
}

file_put_contents($logFile, $logMessage, FILE_APPEND);
echo $logMessage;