<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class LoginController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $errors = [];

            if (empty($email)) {
                $errors[] = 'Email is required';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            }

            if (empty($errors)) {
                $user = $this->userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password'])) {
                    // Update last login time
                    $this->userModel->updateLastLogin($user['user_id']);

                    // Store user in session (excluding password)
                    unset($user['password']);
                    $_SESSION['user'] = $user;

                    // Redirect to home page
                    $this->redirect('/');
                } else {
                    $errors[] = 'Invalid email or password';
                }
            }

            // If there are errors, show them in the login form
            $this->render('auth/login', [
                'errors' => $errors,
                'email' => $email
            ]);
        } else {
            // Show login form
            $this->render('auth/login');
        }
    }

    public function logout() {
        // Clear all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();
        
        // Start a new session for flash messages
        session_start();
        $_SESSION['success'] = 'You have been successfully logged out.';
        
        // Redirect to home page with absolute path
        header('Location: ' . $this->basePath . '/');
        exit;
    }
} 