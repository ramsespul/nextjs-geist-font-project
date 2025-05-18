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

// Register print action
$documentController->registrarVisualizacion(
    $_SESSION['user_id'], 
    $procedimiento['id'], 
    'impreso'
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimiendo: <?php echo htmlspecialchars($procedimiento['titulo']); ?></title>
    <style>
        @media print {
            .watermark {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 60px;
                opacity: 0.2;
                pointer-events: none;
                z-index: 1000;
            }
            
            .print-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                padding: 10px;
                font-size: 12px;
                text-align: right;
                color: #666;
            }
            
            .print-footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 10px;
                font-size: 12px;
                text-align: center;
                color: #666;
            }
        }

        /* Hide elements when not printing */
        @media screen {
            .print-only {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Watermark -->
    <div class="watermark print-only">
        <?php echo htmlspecialchars($_SESSION['name']); ?>
    </div>

    <!-- Print Header -->
    <div class="print-header print-only">
        Impreso por: <?php echo htmlspecialchars($_SESSION['name']); ?><br>
        Fecha: <?php echo date('Y-m-d H:i:s'); ?>
    </div>

    <!-- Document Content -->
    <iframe src="viewer.php?id=<?php echo $procedimiento['id']; ?>&print=true" 
            style="width: 100%; height: 100vh; border: none;">
    </iframe>

    <!-- Print Footer -->
    <div class="print-footer print-only">
        Este documento es confidencial y solo para uso interno
    </div>

    <script>
    // Automatically trigger print dialog
    window.onload = function() {
        window.print();
    };

    // Prevent default browser actions
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && 
            (e.key === 's' || e.key === 'u' || 
             e.key === 'i' || e.key === 'j')) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>
