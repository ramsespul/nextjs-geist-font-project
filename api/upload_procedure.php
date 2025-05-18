<?php
session_start();
require_once '../config/database.php';
require_once '../controllers/DocumentController.php';

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
    // Validate file upload
    if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Error en la carga del archivo');
    }

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['archivo']['tmp_name']);
    finfo_close($finfo);

    if ($mime_type !== 'application/pdf') {
        throw new Exception('Solo se permiten archivos PDF');
    }

    // Generate unique filename
    $filename = uniqid() . '.pdf';
    $upload_dir = '../uploads/procedures/';
    
    // Create upload directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($_FILES['archivo']['tmp_name'], $upload_dir . $filename)) {
        throw new Exception('Error al guardar el archivo');
    }

    // Save to database
    $db = new Database();
    $documentController = new DocumentController($db->connect());
    
    $result = $documentController->subirProcedimiento(
        $_POST['titulo'],
        $_POST['descripcion'],
        $filename,
        $_POST['departamento_id']
    );

    if (!$result) {
        // Remove uploaded file if database insert fails
        unlink($upload_dir . $filename);
        throw new Exception('Error al guardar en la base de datos');
    }

    $_SESSION['flash_message'] = 'Procedimiento subido exitosamente';
    $_SESSION['flash_type'] = 'success';
    header('Location: ../index.php?route=admin');

} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'error';
    header('Location: ../index.php?route=admin');
}
?>
