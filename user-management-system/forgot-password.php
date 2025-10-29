<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if(isLoggedIn()) {
    redirect('dashboard.php');
}

$auth = new Auth();
$message = '';

if($_POST) {
    $email = sanitize($_POST['email']);
    if($auth->requestPasswordReset($email)) {
        $message = "Password reset link has been sent to your email!";
    } else {
        $message = "Error: Email not found or unable to send reset link.";
    }
}

$page_title = "Forgot Password";
?>
<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-key"></i>
            <h2>Reset Password</h2>
            <p class="mb-0">Enter your email to receive reset link</p>
        </div>
        <div class="auth-body">
            <?php if($message): ?>
                <div class="alert <?php echo strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!$message || strpos($message, 'Error') !== false): ?>
            <form method="POST">
                <div class="mb-4">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <small class="text-muted">We'll send a password reset link to your email.</small>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Send Reset Link
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