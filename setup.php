<?php
$codeDir = __DIR__ . '/codes';
if (!is_dir($codeDir)) {
    mkdir($codeDir, 0755, true);
}

$emailsFile = __DIR__ . '/registered_emails.txt';
if (!file_exists($emailsFile)) {
    file_put_contents($emailsFile, '');
}

echo "Setup completed: directories and files created!";