<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            if (empty($data['email']) || empty($data['password'])) {
                $this->redirect('/login?error=missing_fields');
            }

            $user = $this->userModel->findByUsernameOrEmail($data['email']);
            
            if ($user && password_verify($data['password'], $user['password'])) {
                // Set session data properly to match what Controller expects
                $_SESSION['user'] = [
                    'id' => $user['user_id'],
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name']
                ];
                
                // Also set individual session keys for backward compatibility
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['username'];
                
                // Redirect admin users to admin dashboard, others to home page
                if ($user['role'] === 'admin') {
                    $this->redirect('/admin');
                } else {
                    $this->redirect('/');
                }
            } else {
                $this->redirect('/login?error=invalid_credentials');
            }
        }

        $this->render('auth/login');
    }

    public function register() {
        if ($this->isPost()) {
            $data = $this->getPostData();
            
            // Validate required fields
            $requiredFields = ['username', 'email', 'password', 'confirm_password'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    $this->redirect('/register?error=missing_fields');
                }
            }

            // Validate password match
            if ($data['password'] !== $data['confirm_password']) {
                $this->redirect('/register?error=password_mismatch');
            }

            // Check if email already exists
            if ($this->userModel->findByEmail($data['email'])) {
                $this->redirect('/register?error=email_exists');
            }

            // Check if username already exists
            if ($this->userModel->findByUsername($data['username'])) {
                $this->redirect('/register?error=username_exists');
            }

            // Create user
            try {
                $this->userModel->create([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'first_name' => $data['first_name'] ?? null,
                    'last_name' => $data['last_name'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'address' => $data['address'] ?? null
                ]);

                $_SESSION['success'] = 'registration_success';
                $this->redirect('/login');
            } catch (Exception $e) {
                $this->redirect('/register?error=registration_failed');
            }
        }

        $this->render('auth/register');
    }

    public function logout() {
        session_destroy();
        $this->redirect('/login');
    }

    public function profile() {
        $this->requireAuth();
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('/logout');
        }
        $this->render('auth/profile', ['user' => $user]);
    }

    public function updateProfile() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect('/profile');
        }

        $userId = $this->getCurrentUserId();
        $data = $this->getPostData();
        
        // Validate required fields
        if (empty($data['first_name']) || empty($data['last_name'])) {
            $this->redirect('/profile?error=missing_fields');
        }

        try {
            $updateData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null
            ];

            if ($this->userModel->updateProfile($userId, $updateData)) {
                // Update session data
                $_SESSION['user']['first_name'] = $data['first_name'];
                $_SESSION['user']['last_name'] = $data['last_name'];
                $this->redirect('/profile?success=updated');
            } else {
                $this->redirect('/profile?error=update_failed');
            }
        } catch (Exception $e) {
            $this->redirect('/profile?error=update_failed');
        }
    }

    public function changePassword() {
        $this->requireAuth();
        
        if (!$this->isPost()) {
            $this->redirect('/profile');
        }

        $userId = $this->getCurrentUserId();
        $data = $this->getPostData();
        
        // Validate required fields
        if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
            $this->redirect('/profile?error=missing_fields');
        }

        // Verify new passwords match
        if ($data['new_password'] !== $data['confirm_password']) {
            $this->redirect('/profile?error=password_mismatch');
        }

        // Verify current password
        $user = $this->userModel->findById($userId);
        if (!password_verify($data['current_password'], $user['password'])) {
            $this->redirect('/profile?error=invalid_password');
        }

        try {
            $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
            if ($this->userModel->changePassword($userId, $hashedPassword)) {
                $this->redirect('/profile?success=password_updated');
            } else {
                $this->redirect('/profile?error=update_failed');
            }
        } catch (Exception $e) {
            $this->redirect('/profile?error=update_failed');
        }
    }
}