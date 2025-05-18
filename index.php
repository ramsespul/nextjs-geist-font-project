<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/DocumentController.php';

// Router
$route = $_GET['route'] ?? 'login';

// Check if user is authenticated for protected routes
if (!isset($_SESSION['user_id']) && $route !== 'login') {
    header('Location: index.php?route=login');
    exit();
}

// Basic routing
switch ($route) {
    case 'login':
        include 'views/login.php';
        break;
    case 'dashboard':
        include 'views/dashboard.php';
        break;
    case 'admin':
        if ($_SESSION['role'] !== 'administrador') {
            header('Location: index.php?route=dashboard');
            exit();
        }
        include 'views/admin.php';
        break;
    default:
        include 'views/404.php';
}
?>
