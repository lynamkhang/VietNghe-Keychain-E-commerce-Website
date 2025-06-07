<?php
session_start();

// Define base path
define('BASE_PATH', __DIR__);

// Load helpers
require_once BASE_PATH . '/config/helpers.php';

// Autoload classes
spl_autoload_register(function ($class) {
    $directories = ['controllers', 'models'];
    foreach ($directories as $directory) {
        $file = BASE_PATH . '/' . $directory . '/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Simple router
$request_uri = isset($_GET['url']) ? $_GET['url'] : '';
$request_method = $_SERVER['REQUEST_METHOD'];

// Remove trailing slash
$request_uri = rtrim($request_uri, '/');

// Split the URI into segments
$segments = explode('/', trim($request_uri, '/'));

// Default route
if (empty($segments[0])) {
    $controller = new HomeController();
    $controller->index();
    exit;
}

// Route handling
$controller_name = ucfirst($segments[0]) . 'Controller';
$action = $segments[1] ?? 'index';
$id = $segments[2] ?? null;

// Special routing cases
switch ($segments[0]) {
    case 'products':
        $controller_name = 'ProductController';
        break;
    case 'orders':
        $controller_name = 'OrderController';
        break;
    case 'logout':
        $controller_name = 'AuthController';
        break;
    case 'language':
        $controller_name = 'LanguageController';
        break;
}

// Check if controller exists before instantiating
if (!class_exists($controller_name)) {
    // Set default content for 404 page
    $content = '<div class="text-center"><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></div>';
    require_once BASE_PATH . '/views/layouts/main.php';
    exit;
}

$controller = new $controller_name();

// Handle different routes
switch ($segments[0]) {
    case 'language':
        if ($action === 'switch') {
            $controller->switchLanguage();
        }
        break;

    case 'products':
    if (is_numeric($action)) {
        // If the second segment is numeric, treat it as product ID
        $controller->show($action);
    } elseif ($action === 'search') {
        $controller->search();
    } elseif ($action === 'category' && $id) {
        $controller->category($id);
    } else {
        $controller->index();
    }
    break;

    case 'cart':
        switch ($action) {
            case 'add':
                $controller->add();
                break;
            case 'update':
                $controller->update();
                break;
            case 'remove':
                $controller->remove();
                break;
            case 'clear':
                $controller->clear();
                break;
            default:
                $controller->index();
        }
        break;

    case 'checkout':
        if ($action === 'process') {
            $controller->process();
        } else {
            $controller->index();
        }
        break;

    case 'orders':
        if ($action === 'show' && $id) {
            $controller->show($id);
        } else {
            $controller->index();
        }
        break;

    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'profile':
        $controller = new AuthController();
        switch ($action) {
            case 'update':
                $controller->updateProfile();
                break;
            case 'password':
                $controller->changePassword();
                break;
            default:
                $controller->profile();
        }
        break;

    case 'admin':
        $controller = new AdminController();
        switch ($action) {
            case 'users':
                if ($id) {
                    $subAction = $segments[3] ?? null;
                    if ($subAction === 'edit') {
                        $controller->editUser($id);
                    } elseif ($subAction === 'delete') {
                        $controller->deleteUser($id);
                    } elseif ($subAction === 'update') {
                        $controller->updateUser($id);
                    }
                } else {
                    $controller->users();
                }
                break;
            case 'products':
                if (isset($segments[2]) && $segments[2] === 'create') {
                    // Handle /admin/products/create
                    $controller->createProduct();
                } elseif ($id) {
                    $subAction = $segments[3] ?? null;
                    if ($subAction === 'edit') {
                        $controller->editProduct($id);
                    } elseif ($subAction === 'update') {
                        $controller->editProduct($id); // Use editProduct for both GET and POST
                    } elseif ($subAction === 'delete') {
                        $controller->deleteProduct($id);
                    }
                } else {
                    $controller->products();
                }
                break;
            case 'orders':
                if ($id) {
                    $subAction = $segments[3] ?? null;
                    if ($subAction === 'status') {
                        $controller->updateOrderStatus($id);
                    } else {
                        $controller->viewOrder($id);
                    }
                } else {
                    $controller->orders();
                }
                break;
            default:
                $controller->index();
        }
        break;

    default:
        // Set default content for 404 page
        $content = '<div class="text-center"><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></div>';
        require_once BASE_PATH . '/views/layouts/main.php';
        exit;
}