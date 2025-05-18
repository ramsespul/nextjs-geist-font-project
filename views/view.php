<?php
if (!isset($_GET['id'])) {
    header('Location: index.php?route=dashboard');
    exit();
}

$documentController = new DocumentController($database->connect());
$procedimiento = $documentController->getProcedimiento($_GET['id']);

if (!$procedimiento) {
    $_SESSION['flash_message'] = 'Documento no encontrado';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php?route=dashboard');
    exit();
}

// Register view in history
$documentController->registrarVisualizacion($_SESSION['user_id'], $procedimiento['id']);
?>

<div class="container mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <!-- Document Header -->
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <?php echo htmlspecialchars($procedimiento['titulo']); ?>
                </h1>
                <p class="text-gray-600">
                    Departamento: <?php echo htmlspecialchars($procedimiento['departamento_nombre']); ?>
                </p>
            </div>
            
            <button onclick="printDocument()" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                Imprimir Documento
            </button>
        </div>

        <!-- PDF Viewer -->
        <div id="viewerContainer" class="w-full h-[800px] border border-gray-300 rounded-lg">
            <iframe id="pdfViewer" 
                    src="viewer.php?id=<?php echo $procedimiento['id']; ?>" 
                    class="w-full h-full"
                    style="pointer-events: none;">
            </iframe>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.11.338/pdf.min.js"></script>
<script>
// Prevent default browser actions
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && 
        (e.key === 's' || e.key === 'p' || e.key === 'u' || 
         e.key === 'i' || e.key === 'j')) {
        e.preventDefault();
    }
});

// Custom print function
function printDocument() {
    // Register print action
    fetch('api/register_print.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            procedimiento_id: <?php echo $procedimiento['id']; ?>,
            usuario_id: <?php echo $_SESSION['user_id']; ?>
        })
    });

    // Create a new window for printing
    const printWindow = window.open('print.php?id=<?php echo $procedimiento['id']; ?>', 
                                  'print_window',
                                  'width=800,height=600');

    // Close print window after printing
    printWindow.onafterprint = function() {
        printWindow.close();
    };
}

// Disable iframe interactions except scrolling
const viewer = document.getElementById('pdfViewer');
viewer.addEventListener('mousedown', function(e) {
    if (e.button !== 1) { // Allow middle mouse button for scrolling
        e.preventDefault();
    }
});
</script>
