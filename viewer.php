<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/DocumentController.php';

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 403 Forbidden');
    exit('Acceso denegado');
}

// Validate document ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    exit('ID de documento inválido');
}

// Get document info
$db = new Database();
$documentController = new DocumentController($db->connect());
$procedimiento = $documentController->getProcedimiento($_GET['id']);

if (!$procedimiento) {
    header('HTTP/1.1 404 Not Found');
    exit('Documento no encontrado');
}

// Set file path
$file_path = 'uploads/procedures/' . $procedimiento['archivo'];

if (!file_exists($file_path)) {
    header('HTTP/1.1 404 Not Found');
    exit('Archivo no encontrado');
}

// Stream the PDF file with security headers
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Content-Security-Policy: default-src \'self\'; object-src \'self\'');

// Add watermark if printing
if (isset($_GET['print'])) {
    // Future enhancement: Add watermark using FPDI/FPDF
    // For now, just stream the file
    readfile($file_path);
} else {
    readfile($file_path);
}
?>

<script>
// Additional security measures
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && 
        (e.key === 's' || e.key === 'p' || e.key === 'u' || 
         e.key === 'i' || e.key === 'j')) {
        e.preventDefault();
    }
});
</script>
