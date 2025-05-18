<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrador') {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit('Método no permitido');
}

try {
    // Validate input
    $required_fields = ['nombre', 'correo', 'password', 'rol', 'departamento_id'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("El campo {$field} es requerido");
        }
    }

    // Validate email
    if (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Correo electrónico inválido');
    }

    // Validate role
    $valid_roles = ['empleado', 'supervisor', 'administrador'];
    if (!in_array($_POST['rol'], $valid_roles)) {
        throw new Exception('Rol inválido');
    }

    // Hash password
    $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Connect to database
    $db = new Database();
    $conn = $db->connect();

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE correo = :correo");
    $stmt->bindParam(':correo', $_POST['correo']);
    $stmt->execute();

    if ($stmt->fetch()) {
        throw new Exception('El correo electrónico ya está registrado');
    }

    // Insert new user
    $stmt = $conn->prepare(
        "INSERT INTO usuarios (nombre, correo, contraseña, rol, departamento_id) 
         VALUES (:nombre, :correo, :password, :rol, :departamento_id)"
    );

    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->bindParam(':correo', $_POST['correo']);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':rol', $_POST['rol']);
    $stmt->bindParam(':departamento_id', $_POST['departamento_id']);

    if (!$stmt->execute()) {
        throw new Exception('Error al crear el usuario');
    }

    $_SESSION['flash_message'] = 'Usuario creado exitosamente';
    $_SESSION['flash_type'] = 'success';

} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'error';
}

header('Location: ../index.php?route=admin');
?>
