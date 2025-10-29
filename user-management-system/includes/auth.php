<?php
require_once 'config/database.php';

class Auth {
    private $conn;
    private $table_users = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function register($username, $email, $password, $first_name, $last_name, $phone, $role = 'User') {
        $query = "INSERT INTO " . $this->table_users . " 
                 SET username=:username, email=:email, password=:password, 
                     first_name=:first_name, last_name=:last_name, phone=:phone, role=:role";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":role", $role);
        
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password", $password_hash);
        
        return $stmt->execute();
    }

    public function login($username, $password) {
        $query = "SELECT id, username, password, role, first_name, last_name, status FROM " . $this->table_users . " 
                 WHERE (username = :username OR email = :username) AND status = 'Active' LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                return true;
            }
        }
        return false;
    }

    public function requestPasswordReset($email) {
        $query = "SELECT id, first_name FROM " . $this->table_users . " WHERE email = :email AND status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $token = generateToken();
            $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));
            
            $updateQuery = "UPDATE " . $this->table_users . " 
                           SET reset_token = :token, token_expiry = :expiry 
                           WHERE email = :email";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":token", $token);
            $updateStmt->bindParam(":expiry", $expiry);
            $updateStmt->bindParam(":email", $email);
            
            if($updateStmt->execute()) {
                return sendResetEmail($email, $token);
            }
        }
        return false;
    }

    public function resetPassword($token, $newPassword) {
        $query = "SELECT id FROM " . $this->table_users . " 
                 WHERE reset_token = :token AND token_expiry > NOW() AND status = 'Active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":token", $token);
        $stmt->execute();
        
        if($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $password_hash = password_hash($newPassword, PASSWORD_BCRYPT);
            
            $updateQuery = "UPDATE " . $this->table_users . " 
                           SET password = :password, reset_token = NULL, token_expiry = NULL 
                           WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(":password", $password_hash);
            $updateStmt->bindParam(":id", $user['id']);
            
            return $updateStmt->execute();
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_users . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $query = "SELECT * FROM " . $this->table_users . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $query = "UPDATE " . $this->table_users . " 
                 SET first_name = :first_name, last_name = :last_name, email = :email, 
                     phone = :phone, role = :role, status = :status 
                 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }

    public function deleteUser($id) {
        $query = "DELETE FROM " . $this->table_users . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>