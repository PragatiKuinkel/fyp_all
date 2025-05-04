<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/EmailVerification.php';

$emailVerification = new EmailVerification($dbh);
$message = '';
$success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    if ($emailVerification->verifyEmailToken($token)) {
        $message = 'Email verified successfully! You can now login.';
        $success = true;
    } else {
        $message = 'Invalid or expired verification link.';
    }
} else {
    $message = 'No verification token provided.';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Event Management System</title>
    <link rel="stylesheet" href="assets/css/verify_email.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="verification-container">
        <div class="verification-box">
            <div class="verification-icon">
                <?php if ($success): ?>
                    <i class="fas fa-check-circle success"></i>
                <?php else: ?>
                    <i class="fas fa-times-circle error"></i>
                <?php endif; ?>
            </div>
            <h2><?php echo $success ? 'Email Verified!' : 'Verification Failed'; ?></h2>
            <p><?php echo $message; ?></p>
            <div class="verification-actions">
                <a href="login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Go to Login
                </a>
            </div>
        </div>
    </div>
</body>
</html> 