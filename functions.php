<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/config.php'; // ✅ Use constants from here

function generateVerificationCode(): string {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function registerEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
        return true;
    }
    return false;
}

function unsubscribeEmail(string $email): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return false;
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $updatedEmails = array_diff($emails, [$email]);
    if (count($emails) === count($updatedEmails)) return false;
    $content = implode(PHP_EOL, $updatedEmails);
    if (!empty($updatedEmails)) $content .= PHP_EOL;
    return file_put_contents($file, $content) !== false;
}

function sendVerificationEmail(string $email, string $code): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "<p>Your verification code is: <strong>{$code}</strong></p>";
        $mail->AltBody = "Your verification code is: {$code}";
        return $mail->send();
    } catch (Exception $e) {
        error_log("PHPMailer Exception while sending to $email: " . $e->getMessage());
        return false;
    }
}

function sendUnsubscribeVerificationEmail(string $email, string $code): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Confirm Un-subscription';
        $mail->Body = "<p>To confirm un-subscription, use this code: <strong>{$code}</strong></p>";
        $mail->AltBody = "To confirm un-subscription, use this code: {$code}";
        return $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $e->getMessage());
        return false;
    }
}

function verifyCode(string $email, string $code): bool {
    $sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    $filenameSafeEmail = preg_replace('/[^a-zA-Z0-9_@.-]/', '_', $sanitizedEmail);
    $codeFile = __DIR__ . "/codes/{$filenameSafeEmail}.txt";
    if (file_exists($codeFile)) {
        $storedCode = trim(file_get_contents($codeFile));
        if ($storedCode === $code) {
            unlink($codeFile);
            return true;
        }
    }
    return false;
}

function fetchAndFormatXKCDData(): string {
    $latestComicInfo = @file_get_contents("https://xkcd.com/info.0.json");
    if ($latestComicInfo === false) throw new Exception("Could not fetch latest XKCD comic ID.");
    $latestComicData = json_decode($latestComicInfo, true);
    if (!isset($latestComicData['num'])) throw new Exception("Invalid data for latest XKCD comic ID.");
    $maxComicId = $latestComicData['num'];
    $randomComicId = rand(1, $maxComicId);
    $url = "https://xkcd.com/{$randomComicId}/info.0.json";
    $response = @file_get_contents($url);
    if ($response === false) throw new Exception("Could not fetch XKCD comic data.");
    $data = json_decode($response, true);
    if (!$data || !isset($data['img'], $data['title'], $data['alt'])) throw new Exception("Invalid XKCD comic data.");

    return "<h2>XKCD Comic</h2>
            <img src=\"{$data['img']}\" alt=\"{$data['alt']}\">";
}


function sendXKCDUpdatesToSubscribers(): bool {
    $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) {
        error_log("No registered emails file found.");
        return false;
    }
    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (empty($emails)) {
        error_log("No subscribers found.");
        return true;
    }
    $overallSuccess = true;
    try {
        $comicHtml = fetchAndFormatXKCDData();
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Skipping invalid email: {$email}");
                continue;
            }
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USERNAME;
                $mail->Password = SMTP_PASSWORD;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = SMTP_PORT;
                $mail->setFrom(FROM_EMAIL, FROM_NAME);
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your XKCD Comic';
                $unsubscribeLink = "https://xkcd-comic-app.onrender.com/unsubscribe.php?email=" . urlencode($email);
                $mailBody = $comicHtml . "<p><a href=\"$unsubscribeLink\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
                $mail->Body = $mailBody;
                $mail->AltBody = "View today's XKCD comic. To unsubscribe, visit: " . $unsubscribeLink;
                $mail->send();
            } catch (Exception $e) {
                error_log("Error sending to {$email}: " . $e->getMessage());
                $overallSuccess = false;
            }
        }
        return $overallSuccess;
    } catch (Exception $e) {
        error_log("Error fetching comic: " . $e->getMessage());
        return false;
    }
}
