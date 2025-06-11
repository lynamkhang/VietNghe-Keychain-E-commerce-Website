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
                $this->redirect('/register?error=missing_fields');
                return;
            } elseif ($this->userModel->findByUsername($username)) {
                $this->redirect('/register?error=username_exists');
                return;
            }

            if (empty($email)) {
                $this->redirect('/register?error=missing_fields');
                return;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->redirect('/register?error=invalid_email');
                return;
            } elseif ($this->userModel->findByEmail($email)) {
                $this->redirect('/register?error=email_exists');
                return;
            }

            if (empty($password)) {
                $this->redirect('/register?error=missing_fields');
                return;
            } elseif (strlen($password) < 6) {
                $this->redirect('/register?error=password_too_short');
                return;
            }

            if ($password !== $confirmPassword) {
                $this->redirect('/register?error=password_mismatch');
                return;
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
                    $_SESSION['success'] = 'registration_success';
                    $this->redirect('/login');
                } else {
                    $this->redirect('/register?error=registration_failed');
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