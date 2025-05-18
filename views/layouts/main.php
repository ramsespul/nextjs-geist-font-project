<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intranet - Sistema de Procedimientos de Calidad</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Prevent right click -->
    <script>
        document.addEventListener('contextmenu', event => event.preventDefault());
        
        // Prevent keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && 
                (e.key === 's' || e.key === 'p' || e.key === 'u' || 
                 e.key === 'i' || e.key === 'j')) {
                e.preventDefault();
            }
            if (e.key === 'F12') {
                e.preventDefault();
            }
        });
    </script>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php if (isset($_SESSION['user_id'])): ?>
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-xl font-bold text-gray-800">Intranet Calidad</span>
                    </div>
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="index.php?route=dashboard" 
                           class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-800">
                            Inicio
                        </a>
                        <?php if ($_SESSION['role'] === 'administrador'): ?>
                        <a href="index.php?route=admin" 
                           class="inline-flex items-center px-1 pt-1 text-gray-600 hover:text-gray-800">
                            Administración
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-600 mr-4"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <a href="index.php?route=logout" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="mb-4 px-4 py-3 rounded-lg <?php echo $_SESSION['flash_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                <?php 
                echo htmlspecialchars($_SESSION['flash_message']);
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                ?>
            </div>
        <?php endif; ?>

        <?php include $content; ?>
    </main>

    <footer class="bg-white shadow-lg mt-8">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                © <?php echo date('Y'); ?> Sistema de Procedimientos de Calidad. Todos los derechos reservados.
            </p>
        </div>
    </footer>
</body>
</html>
