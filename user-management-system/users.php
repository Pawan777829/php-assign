<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

if(!isLoggedIn() || (!hasRole('Admin') && !hasRole('Manager'))) {
    redirect('dashboard.php');
}

$auth = new Auth();

// Handle user actions
if(isset($_POST['action'])) {
    $action = $_POST['action'];
    $user_id = $_POST['user_id'];
    
    switch($action) {
        case 'delete':
            if(hasRole('Admin') && $user_id != $_SESSION['user_id']) {
                $auth->deleteUser($user_id);
            }
            break;
            
        case 'update_role':
            if(hasRole('Admin')) {
                $data = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'role' => $_POST['role'],
                    'status' => $_POST['status'],
                    'id' => $user_id
                ];
                $auth->updateUser($user_id, $data);
            }
            break;
    }
}

$users = $auth->getAllUsers();
$page_title = "Manage Users";
?>
<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">User Management</h1>
            <p class="text-muted mb-0">Manage system users and their permissions</p>
        </div>
        <?php if(hasRole('Admin')): ?>
        <a href="register.php" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Add New User
        </a>
        <?php endif; ?>
    </div>

    <div class="table-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'].' '.$user['last_name']); ?>&background=007bff&color=fff" 
                                         class="user-avatar me-3" alt="<?php echo $user['first_name']; ?>">
                                    <div>
                                        <h6 class="mb-0"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h6>
                                        <small class="text-muted">@<?php echo $user['username']; ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div><i class="fas fa-envelope me-2 text-muted"></i><?php echo $user['email']; ?></div>
                                    <?php if($user['phone']): ?>
                                    <div><i class="fas fa-phone me-2 text-muted"></i><?php echo $user['phone']; ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if(hasRole('Admin')): ?>
                                    <select name="role" class="form-select form-select-sm" onchange="updateUser(this, <?php echo $user['id']; ?>, 'role')">
                                        <option value="User" <?php echo $user['role'] == 'User' ? 'selected' : ''; ?>>User</option>
                                        <option value="Manager" <?php echo $user['role'] == 'Manager' ? 'selected' : ''; ?>>Manager</option>
                                        <option value="Admin" <?php echo $user['role'] == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                <?php else: ?>
                                    <?php echo getRoleBadge($user['role']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if(hasRole('Admin')): ?>
                                    <select name="status" class="form-select form-select-sm" onchange="updateUser(this, <?php echo $user['id']; ?>, 'status')">
                                        <option value="Active" <?php echo $user['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo $user['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                <?php else: ?>
                                    <?php echo getStatusBadge($user['status']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#userModal<?php echo $user['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if(hasRole('Admin') && $user['id'] != $_SESSION['user_id']): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>

                        <!-- User Modal -->
                        <div class="modal fade" id="userModal<?php echo $user['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">User Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="text-center mb-4">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'].' '.$user['last_name']); ?>&background=007bff&color=fff&size=100" 
                                                 class="rounded-circle mb-3" alt="<?php echo $user['first_name']; ?>">
                                            <h4><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h4>
                                            <p class="text-muted">@<?php echo $user['username']; ?></p>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <strong>Email:</strong><br>
                                                <?php echo $user['email']; ?>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <strong>Phone:</strong><br>
                                                <?php echo $user['phone'] ?: 'Not provided'; ?>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <strong>Role:</strong><br>
                                                <?php echo getRoleBadge($user['role']); ?>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <strong>Status:</strong><br>
                                                <?php echo getStatusBadge($user['status']); ?>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <strong>Member Since:</strong><br>
                                                <?php echo date('F j, Y g:i A', strtotime($user['created_at'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function updateUser(select, userId, field) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const actionInput = document.createElement('input');
    actionInput.name = 'action';
    actionInput.value = 'update_role';
    form.appendChild(actionInput);
    
    const userIdInput = document.createElement('input');
    userIdInput.name = 'user_id';
    userIdInput.value = userId;
    form.appendChild(userIdInput);
    
    const valueInput = document.createElement('input');
    valueInput.name = field;
    valueInput.value = select.value;
    form.appendChild(valueInput);
    
    document.body.appendChild(form);
    form.submit();
}

function deleteUser(userId) {
    if(confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        const actionInput = document.createElement('input');
        actionInput.name = 'action';
        actionInput.value = 'delete';
        form.appendChild(actionInput);
        
        const userIdInput = document.createElement('input');
        userIdInput.name = 'user_id';
        userIdInput.value = userId;
        form.appendChild(userIdInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?>