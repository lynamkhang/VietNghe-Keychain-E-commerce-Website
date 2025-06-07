<?php
abstract class Controller {
    protected $basePath = '/vietnghe-keychain';

    protected function render($view, $data = []) {
        extract($data);
        
        $viewPath = __DIR__ . "/../views/{$view}.php";
        if (!file_exists($viewPath)) {
            throw new Exception("View {$view} not found");
        }
        
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        require __DIR__ . "/../views/layouts/main.php";
    }

    protected function redirect($url) {
        if (strpos($url, 'http') !== 0) {
            $url = $this->basePath . $url;
        }
        header("Location: {$url}");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function getPostData() {
        $data = $_POST;
        
        // Handle file uploads
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $file) {
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $data[$key] = $file;
                }
            }
        }
        
        return $data;
    }

    protected function getQueryParams() {
        return $_GET;
    }

    protected function isAuthenticated() {
        return isset($_SESSION['user']);
    }

    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Authentication required'
                ]);
                exit;
            }
            $this->redirect('/login');
        }
    }

    protected function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function getCurrentUserId() {
        return $_SESSION['user']['id'] ?? null;
    }

    protected function getCurrentUserName() {
        return $_SESSION['user']['username'] ?? null;
    }

    protected function getCurrentUserEmail() {
        return $_SESSION['user']['email'] ?? null;
    }
} 