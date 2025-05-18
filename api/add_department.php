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
    if (!isset($_POST['nombre']) || empty($_POST['nombre'])) {
        throw new Exception('El nombre del departamento es requerido');
    }

    // Connect to database
    $db = new Database();
    $conn = $db->connect();

    // Check if department already exists
    $stmt = $conn->prepare("SELECT id FROM departamentos WHERE nombre = :nombre");
    $stmt->bindParam(':nombre', $_POST['nombre']);
    $stmt->execute();

    if ($stmt->fetch()) {
        throw new Exception('El departamento ya existe');
    }

    // Insert new department
    $stmt = $conn->prepare("INSERT INTO departamentos (nombre) VALUES (:nombre)");
    $stmt->bindParam(':nombre', $_POST['nombre']);

    if (!$stmt->execute()) {
        throw new Exception('Error al crear el departamento');
    }

    $_SESSION['flash_message'] = 'Departamento creado exitosamente';
    $_SESSION['flash_type'] = 'success';

} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'error';
}

header('Location: ../index.php?route=admin');
?>
