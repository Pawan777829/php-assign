<?php
require_once 'includes/functions.php';

if(isLoggedIn()) {
    redirect('dashboard.php');
}

$page_title = "Welcome";
?>
<?php include 'includes/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <i class="fas fa-users-cog"></i>
            <h2>College User Management</h2>
            <p class="mb-0">Role-Based Access Control System</p>
        </div>
        <div class="auth-body text-center">
            <h4 class="mb-4">Welcome to UMS</h4>
            <p class="text-muted mb-4">A secure role-based user management system for educational institutions</p>
            
            <div class="d-grid gap-3">
                <a href="login.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to System
                </a>
                <a href="register.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </a>
            </div>
            
            <hr class="my-4">
            
            <div class="row text-start">
                <div class="col-12">
                    <h6 class="mb-3">System Features:</h6>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-check text-success me-2"></i>Role-based Access Control</li>
                        <li><i class="fas fa-check text-success me-2"></i>Secure Authentication</li>
                        <li><i class="fas fa-check text-success me-2"></i>User Management</li>
                        <li><i class="fas fa-check text-success me-2"></i>Password Recovery</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>