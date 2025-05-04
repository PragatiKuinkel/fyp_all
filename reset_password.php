<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/EmailVerification.php';

$emailVerification = new EmailVerification($dbh);
$message = '';
$success = false;
$showForm = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($code)) {
        $message = 'Please enter the reset code.';
    } elseif (empty($newPassword)) {
        $message = 'Please enter a new password.';
    } elseif (empty($confirmPassword)) {
        $message = 'Please confirm your new password.';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Passwords do not match.';
    } else {
        try {
            // Verify the reset code
            $userId = $emailVerification->verifyPasswordResetCode($code);
            
            if ($userId) {
                // Validate new password
                if (strlen($newPassword) < 8) {
                    $message = 'Password must be at least 8 characters long.';
                } elseif (!preg_match('/[A-Z]/', $newPassword)) {
                    $message = 'Password must contain at least one uppercase letter.';
                } elseif (!preg_match('/[a-z]/', $newPassword)) {
                    $message = 'Password must contain at least one lowercase letter.';
                } elseif (!preg_match('/[0-9]/', $newPassword)) {
                    $message = 'Password must contain at least one number.';
                } elseif (!preg_match('/[^A-Za-z0-9]/', $newPassword)) {
                    $message = 'Password must contain at least one special character.';
                } else {
                    // Update password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $dbh->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $userId]);
                    
                    $message = 'Password has been reset successfully. You can now login with your new password.';
                    $success = true;
                    $showForm = false;
                }
            } else {
                $message = 'Invalid or expired reset code.';
            }
        } catch (PDOException $e) {
            error_log("Database error in reset_password.php: " . $e->getMessage());
            $message = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Event Management System</title>
    <link rel="stylesheet" href="assets/css/reset_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="reset-password-container">
        <div class="reset-password-box">
            <div class="reset-password-header">
                <h2>Reset Password</h2>
                <p>Enter the reset code sent to your email and your new password.</p>
            </div>

            <?php if (!empty($message)): ?>
                <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                    <i class="fas <?php echo $success ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($showForm): ?>
                <form action="" method="POST" class="reset-password-form">
                    <div class="form-group">
                        <label for="code">
                            <i class="fas fa-key"></i> Reset Code
                        </label>
                        <input type="text" id="code" name="code" required 
                               value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>"
                               placeholder="Enter the 6-digit code">
                    </div>

                    <div class="form-group">
                        <label for="newPassword">
                            <i class="fas fa-lock"></i> New Password
                        </label>
                        <input type="password" id="newPassword" name="newPassword" required 
                               placeholder="Enter your new password">
                    </div>

                    <div class="form-group">
                        <label for="confirmPassword">
                            <i class="fas fa-lock"></i> Confirm New Password
                        </label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required 
                               placeholder="Confirm your new password">
                    </div>

                    <div class="password-requirements">
                        <h4>Password Requirements:</h4>
                        <ul>
                            <li id="length">At least 8 characters long</li>
                            <li id="uppercase">Contains at least one uppercase letter</li>
                            <li id="lowercase">Contains at least one lowercase letter</li>
                            <li id="number">Contains at least one number</li>
                            <li id="special">Contains at least one special character</li>
                        </ul>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                </form>
            <?php else: ?>
                <div class="reset-success">
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Go to Login
                    </a>
                </div>
            <?php endif; ?>

            <div class="back-to-login">
                <a href="login.php">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('newPassword');
            const requirements = {
                length: document.getElementById('length'),
                uppercase: document.getElementById('uppercase'),
                lowercase: document.getElementById('lowercase'),
                number: document.getElementById('number'),
                special: document.getElementById('special')
            };

            newPassword.addEventListener('input', function() {
                const password = this.value;
                
                // Check requirements
                requirements.length.classList.toggle('valid', password.length >= 8);
                requirements.uppercase.classList.toggle('valid', /[A-Z]/.test(password));
                requirements.lowercase.classList.toggle('valid', /[a-z]/.test(password));
                requirements.number.classList.toggle('valid', /[0-9]/.test(password));
                requirements.special.classList.toggle('valid', /[^A-Za-z0-9]/.test(password));
            });
        });
    </script>
</body>
</html> 