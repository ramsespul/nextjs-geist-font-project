<?php
$documentController = new DocumentController($database->connect());
$departamentos = $documentController->getDepartamentos();

// Handle search and department filter
$departamento_id = $_GET['departamento'] ?? null;
$search = $_GET['search'] ?? '';

if (!empty($search)) {
    $procedimientos = $documentController->buscarProcedimientos($search);
} else {
    $procedimientos = $documentController->getProcedimientos($departamento_id);
}
?>

<div class="container mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4 md:mb-0">
                Procedimientos de Calidad
            </h1>
            
            <!-- Search and Filter Section -->
            <div class="w-full md:w-2/3 flex flex-col md:flex-row gap-4">
                <form action="" method="GET" class="flex-1">
                    <input type="hidden" name="route" value="dashboard">
                    <input type="text" 
                           name="search" 
                           placeholder="Buscar procedimientos..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </form>
                
                <select name="departamento" 
                        onchange="window.location.href='index.php?route=dashboard&departamento='+this.value"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos los departamentos</option>
                    <?php foreach ($departamentos as $dept): ?>
                        <option value="<?php echo $dept['id']; ?>"
                                <?php echo $departamento_id == $dept['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Procedures List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($procedimientos as $proc): ?>
            <div class="bg-gray-50 rounded-lg p-6 hover:shadow-md transition-shadow">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    <?php echo htmlspecialchars($proc['titulo']); ?>
                </h3>
                <p class="text-gray-600 text-sm mb-4">
                    <?php echo htmlspecialchars($proc['descripcion']); ?>
                </p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">
                        <?php echo htmlspecialchars($proc['departamento_nombre']); ?>
                    </span>
                    <a href="index.php?route=view&id=<?php echo $proc['id']; ?>" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Ver Documento
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($procedimientos)): ?>
        <div class="text-center py-8">
            <p class="text-gray-500">No se encontraron procedimientos.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Prevent text selection
document.addEventListener('selectstart', function(e) {
    e.preventDefault();
});

// Debounce search input
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
searchInput.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.form.submit();
    }, 500);
});
</script>
