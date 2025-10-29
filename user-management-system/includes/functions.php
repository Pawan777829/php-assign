<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

function getRoleBadge($role) {
    $badges = [
        'Admin' => 'bg-danger',
        'Manager' => 'bg-warning',
        'User' => 'bg-primary'
    ];
    return '<span class="badge ' . $badges[$role] . '">' . $role . '</span>';
}

function getStatusBadge($status) {
    $badges = [
        'Active' => 'bg-success',
        'Inactive' => 'bg-secondary'
    ];
    return '<span class="badge ' . $badges[$status] . '">' . $status . '</span>';
}

function sendResetEmail($email, $token) {
    $subject = "Password Reset Request - College User Management System";
    $resetLink = "http://localhost/user-management-system/reset-password.php?token=" . $token;
    
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #007bff; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f8f9fa; }
            .button { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>College User Management System</h2>
            </div>
            <div class='content'>
                <h3>Password Reset Request</h3>
                <p>Hello,</p>
                <p>You requested to reset your password. Click the button below to proceed:</p>
                <p><a href='$resetLink' class='button'>Reset Password</a></p>
                <p>If the button doesn't work, copy and paste this link in your browser:</p>
                <p><code>$resetLink</code></p>
                <p>This link will expire in 1 hour.</p>
                <p><strong>Note:</strong> If you didn't request this, please ignore this email.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: College UMS <noreply@college.edu>" . "\r\n";
    
    return mail($email, $subject, $message, $headers);
}
?>