<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$auth = new Auth();
$total_users = count($auth->getAllUsers());
$current_user = $auth->getUserById($_SESSION['user_id']);

$page_title = "Dashboard";
?>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Welcome back, <?php echo $_SESSION['first_name']; ?>!</h1>
                <p class="lead mb-0">Here's what's happening with your account today.</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="feature-icon mx-auto">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="dashboard-card stats-card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $total_users; ?></h3>
                <p class="text-muted mb-0">Total Users</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card stats-card">
                <div class="card-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3><?php echo $_SESSION['role']; ?></h3>
                <p class="text-muted mb-0">Your Role</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card stats-card">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3>Active</h3>
                <p class="text-muted mb-0">Account Status</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="dashboard-card stats-card">
                <div class="card-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <h3><?php echo date('M j, Y'); ?></h3>
                <p class="text-muted mb-0">Today's Date</p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">System Features</h4>
                    <div class="row">
                        <?php if(hasRole('Admin')): ?>
                        <div class="col-md-4 mb-4">
                            <div class="text-center p-3">
                                <div class="feature-icon bg-primary text-white mx-auto">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h5 class="mt-3">Admin Panel</h5>
                                <p class="text-muted">Full system control, user management, and role assignments.</p>
                                <a href="users.php" class="btn btn-outline-primary btn-sm">Manage Users</a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(hasRole('Manager') || hasRole('Admin')): ?>
                        <div class="col-md-4 mb-4">
                            <div class="text-center p-3">
                                <div class="feature-icon bg-success text-white mx-auto">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h5 class="mt-3">User Management</h5>
                                <p class="text-muted">View and manage user accounts, profiles, and permissions.</p>
                                <a href="users.php" class="btn btn-outline-success btn-sm">View Users</a>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="col-md-4 mb-4">
                            <div class="text-center p-3">
                                <div class="feature-icon bg-info text-white mx-auto">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <h5 class="mt-3">Profile Management</h5>
                                <p class="text-muted">Update your personal information and change password.</p>
                                <a href="profile.php" class="btn btn-outline-info btn-sm">Edit Profile</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="dashboard-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">Quick Actions</h4>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="profile.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user me-2"></i>My Profile
                            </a>
                        </div>
                        <?php if(hasRole('Admin') || hasRole('Manager')): ?>
                        <div class="col-md-3 mb-3">
                            <a href="users.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="col-md-3 mb-3">
                            <a href="profile.php#password" class="btn btn-outline-warning w-100">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="logout.php" class="btn btn-outline-danger w-100">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>