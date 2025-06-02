<?php
require_once 'functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && !isset($_POST['verification_code'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Invalid email format.";
        } else {
            $code = generateVerificationCode();
            $codeDir = __DIR__ . '/codes/';
            if (!is_dir($codeDir)) {
                mkdir($codeDir, 0755, true);
            }
            file_put_contents($codeDir . "{$email}.txt", $code);

            if (sendVerificationEmail($email, $code)) {
                $message = "Verification code sent to your email.";
            } else {
                $message = "Failed to send verification code. Please try again.";
            }
        }
    }

    if (isset($_POST['verification_code']) && isset($_POST['email'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $code = $_POST['verification_code'];

        if (verifyCode($email, $code)) {
            if (registerEmail($email)) {
                $message = "Email successfully verified and registered!";
            } else {
                $message = "This email is already registered.";
            }
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>XKCD Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        form { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        h2 { color: #333; }
        input { margin: 10px 0; padding: 8px; width: 100%; }
        button { padding: 8px 15px; background: #4285f4; color: white; border: none; cursor: pointer; }
        .message { padding: 10px; background: #f2f2f2; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>XKCD Daily Comic Subscription</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h3>Step 1: Enter your email</h3>
    <form method="POST">
        <input type="email" name="email" required placeholder="Your email address">
        <button type="submit" id="submit-email">Submit</button>
    </form>

    <h3>Step 2: Verify your email</h3>
    <form method="POST">
        <input type="email" name="email" required placeholder="Enter your email again">
        <input type="text" name="verification_code" maxlength="6" required placeholder="Enter 6-digit code">
        <button type="submit" id="submit-verification">Verify</button>
    </form>
</body>
</html>