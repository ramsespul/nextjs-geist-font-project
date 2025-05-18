<?php
class AuthController {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }

    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare(
                "SELECT id, nombre, correo, contraseña, rol 
                FROM usuarios 
                WHERE correo = :email"
            );
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['contraseña'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['nombre'];
                $_SESSION['role'] = $user['rol'];
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            return false;
        }
    }

    public function logout() {
        session_destroy();
        session_start();
        return true;
    }

    public function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'administrador';
    }

    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }

        try {
            $stmt = $this->db->prepare(
                "SELECT id, nombre, correo, rol, departamento_id 
                FROM usuarios 
                WHERE id = :id"
            );
            
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return null;
        }
    }
}
?>
