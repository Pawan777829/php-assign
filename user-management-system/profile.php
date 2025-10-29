<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

$auth = new Auth();
$user = $auth->getUserById($_SESSION['user_id']);
$success = $error = $success_pass = $error_pass = '';

// Handle profile update
if($_POST && isset($_POST['update_profile'])) {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name = sanitize($_POST['last_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    
    $data = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'phone' => $phone,
        'role' => $user['role'],
        'status' => $user['status'],
        'id' => $_SESSION['user_id']
    ];
    
    if($auth->updateUser($_SESSION['user_id'], $data)) {
        $_SESSION['username'] = $username;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['full_name'] = $first_name . ' ' . $last_name;
        $success = "Profile updated successfully!";
        $user = $auth->getUserById($_SESSION['user_id']); // Refresh user data
    } else {
        $error = "Failed to update profile!";
    }
}

// Handle password change
if($_POST && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if(password_verify($current_password, $user['password'])) {
        if($new_password === $confirm_password) {
            $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
            $query = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $auth->conn->prepare($query);
            $stmt->bindParam(":password", $password_hash);
            $stmt->bindParam(":id", $_SESSION['user_id']);
            
            if($stmt->execute()) {
                $success_pass = "Password changed successfully!";
            } else {
                $error_pass = "Failed to change password!";
            }
        } else {
            $error_pass = "New passwords do not match!";
        }
    } else {
        $error_pass = "Current password is incorrect!";
    }
}

$page_title = "My Profile";
?>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <!-- Profile Header -->
    <div class="profile-header">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'] . ' ' . $user['last_name']); ?>&background=fff&color=007bff&size=100" 
             class="profile-avatar" alt="<?php echo htmlspecialchars($user['first_name']); ?>">
        <h2><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
        <p class="lead mb-2">@<?php echo htmlspecialchars($user['username']); ?></p>
        <div>
            <?php 
            if (function_exists('getRoleBadge') && isset($user['role'])) {
                echo getRoleBadge($user['role']);
            }
            if (function_exists('getStatusBadge') && isset($user['status'])) {
                echo getStatusBadge($user['status']);
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Profile Update Form -->
            <div class="dashboard-card">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-user-edit me-2"></i>Update Profile
                    </h4>
                    
                    <?php if($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" disabled>
                            <small class="text-muted">Username cannot be changed</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['role'] ?? ''); ?>" disabled>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Profile
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Change Form -->
            <div class="dashboard-card mt-4" id="password">
                <div class="card-body">
                    <h4 class="card-title mb-4">
                        <i class="fas fa-key me-2"></i>Change Password
                    </h4>
                    
                    <?php if($success_pass): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success_pass); ?></div>
                    <?php endif; ?>
                    <?php if($error_pass): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_pass); ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" name="change_password" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>