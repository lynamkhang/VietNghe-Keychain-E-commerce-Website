<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class RegisterController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function register() {
        if ($this->isPost()) {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $firstName = $_POST['first_name'] ?? '';
            $lastName = $_POST['last_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';

            // Validate input
            $errors = [];
            
            if (empty($username)) {
                $errors[] = 'Username is required';
            } elseif ($this->userModel->findByUsername($username)) {
                $errors[] = 'Username already exists';
            }

            if (empty($email)) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } elseif ($this->userModel->findByEmail($email)) {
                $errors[] = 'Email already exists';
            }

            if (empty($password)) {
                $errors[] = 'Password is required';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters';
            }

            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }

            if (empty($errors)) {
                // Create user
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'address' => $address
                ];

                if ($this->userModel->create($userData)) {
                    // Set success message and redirect to login
                    $_SESSION['success'] = 'Registration successful! Please login.';
                    $this->redirect('/login');
                } else {
                    $errors[] = 'Registration failed. Please try again.';
                }
            }

            // If there are errors, show the form again with errors
            $this->render('auth/register', [
                'errors' => $errors,
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'address' => $address
            ]);
        } else {
            $this->render('auth/register');
        }
    }
} 