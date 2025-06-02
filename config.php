<?php
// Show PHP errors (for development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('UTC');

// Log file for debugging (optional)
define('LOG_FILE', __DIR__ . '/app.log');

function log_message($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents(LOG_FILE, $log_entry, FILE_APPEND);
}

// ✅ Gmail SMTP configuration using Render environment variables
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);

// These values will be read from Render → Environment tab
define('SMTP_USERNAME', getenv('SMTP_USERNAME')); // e.g. homeassist021@gmail.com
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD')); // Gmail App Password

define('FROM_EMAIL', 'homeassist021@gmail.com');
define('FROM_NAME', 'XKCD Updates');

