<?php
// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $auth = new AuthController($database->connect());
    if ($auth->login($_POST['email'], $_POST['password'])) {
        header('Location: index.php?route=dashboard');
        exit();
    } else {
        $_SESSION['flash_message'] = 'Credenciales inválidas';
        $_SESSION['flash_type'] = 'error';
    }
}
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Intranet de Calidad
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Ingrese sus credenciales para acceder al sistema
            </p>
        </div>
        <form class="mt-8 space-y-6" action="index.php?route=login" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Correo electrónico</label>
                    <input id="email" name="email" type="email" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Correo electrónico">
                </div>
                <div>
                    <label for="password" class="sr-only">Contraseña</label>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                           placeholder="Contraseña">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Iniciar Sesión
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Additional security measures
document.addEventListener('DOMContentLoaded', function() {
    // Disable autocomplete
    document.querySelector('form').setAttribute('autocomplete', 'off');
    
    // Clear form on page load
    document.querySelector('form').reset();
    
    // Prevent form resubmission
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
});
</script>
