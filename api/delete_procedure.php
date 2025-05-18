<?php
session_start();
require_once '../config/database.php';

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrador') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !is_numeric($data['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['success' => false, 'message' => 'ID de procedimiento inválido']);
    exit();
}

try {
    // Connect to database
    $db = new Database();
    $conn = $db->connect();

    // Get procedure info to delete file
    $stmt = $conn->prepare("SELECT archivo FROM procedimientos WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    $stmt->execute();
    
    $procedimiento = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$procedimiento) {
        throw new Exception('Procedimiento no encontrado');
    }

    // Delete procedure from database
    $stmt = $conn->prepare("DELETE FROM procedimientos WHERE id = :id");
    $stmt->bindParam(':id', $data['id']);
    
    if (!$stmt->execute()) {
        throw new Exception('Error al eliminar el procedimiento de la base de datos');
    }

    // Delete file
    $file_path = '../uploads/procedures/' . $procedimiento['archivo'];
    if (file_exists($file_path)) {
        if (!unlink($file_path)) {
            // Log error but don't throw exception as DB record is already deleted
            error_log("Error deleting file: {$file_path}");
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Procedimiento eliminado exitosamente'
    ]);

} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
