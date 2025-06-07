<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';

class ProfileController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function profile() {
        $this->requireAuth();
        $userId = $this->getCurrentUserId();
        $user = $this->userModel->findById($userId);
        
        $this->render('profile/index', [
            'user' => $user
        ]);
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
            $_SESSION['error'] = 'First name and last name are required.';
            $this->redirect('/profile');
        }

        // Update profile
        if ($this->userModel->updateProfile($userId, $data)) {
            $_SESSION['success'] = 'Profile updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update profile.';
        }

        $this->redirect('/profile');
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
            $_SESSION['error'] = 'All password fields are required.';
            $this->redirect('/profile');
        }

        // Verify current password
        $user = $this->userModel->findById($userId);
        if (!password_verify($data['current_password'], $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect.';
            $this->redirect('/profile');
        }

        // Verify new passwords match
        if ($data['new_password'] !== $data['confirm_password']) {
            $_SESSION['error'] = 'New passwords do not match.';
            $this->redirect('/profile');
        }

        // Update password
        $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
        if ($this->userModel->changePassword($userId, $hashedPassword)) {
            $_SESSION['success'] = 'Password changed successfully.';
        } else {
            $_SESSION['error'] = 'Failed to change password.';
        }

        $this->redirect('/profile');
    }
} 