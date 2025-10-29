<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if(isLoggedIn()) {
    redirect('dashboard.php');
}

$auth = new Auth();
$message = '';

if(!isset($_GET['token'])) {
    $message = "Invalid reset token!";
} else {
    $token = $_GET['token'];
    
    if($_POST) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if($new_password === $confirm_password) {
            if($auth->resetPassword($token, $new_password)) {
                $message = "Password reset successfully! <a href='login.php' class='alert-link'>Login here</a>";
            } else {
                $message = "Invalid or expired reset token!";
            }
        } else {
            $message = "Passwords do not match!";
        }
    }
}

$page_title = "Reset Password";
?>
<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-lock"></i>
            <h2>Set New Password</h2>
            <p class="mb-0">Create your new password</p>
        </div>
        <div class="auth-body">
            <?php if($message): ?>
                <div class="alert <?php echo (strpos($message, 'Invalid') !== false || strpos($message, 'Error') !== false) ? 'alert-danger' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!$message || strpos($message, 'successfully') === false): ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required>
                    </div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Reset Password
                    </button>
                </div>
            </form>
            <?php endif; ?>

            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Back to Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>