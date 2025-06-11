<?php
require_once __DIR__ . '/Model.php';

class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';

    public function __construct() {
        parent::__construct();
    }

    public function findByEmail($email) {
        $email = $this->db->real_escape_string($email);
        $sql = "SELECT * FROM {$this->table} WHERE email = '{$email}' LIMIT 1";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    public function findByUsername($username) {
        $username = $this->db->real_escape_string($username);
        $sql = "SELECT * FROM {$this->table} WHERE username = '{$username}' LIMIT 1";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    public function findByUsernameOrEmail($login) {
        $login = $this->db->real_escape_string($login);
        $sql = "SELECT * FROM {$this->table} WHERE username = '{$login}' OR email = '{$login}' LIMIT 1";
        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }

    public function verifyPassword($email, $password) {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return true;
        }
        return false;
    }

    public function create($data) {
        // Prepare user data
        $userData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => $data['role'] ?? 'customer',
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null
        ];
        return parent::create($userData);
    }

    public function update($id, $data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        return parent::update($id, $data);
    }

    public function updateLastLogin($userId) {
        $userId = intval($userId);
        $sql = "UPDATE {$this->table} SET last_login = CURRENT_TIMESTAMP WHERE {$this->primaryKey} = {$userId}";
        return $this->db->query($sql);
    }

    public function updateProfile($userId, $data) {
        $sql = "UPDATE {$this->table} SET 
                first_name = ?,
                last_name = ?,
                phone = ?,
                address = ?,
                city = ?,
                country = ?
                WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssssssi", 
            $data['first_name'],
            $data['last_name'],
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['country'],
            $userId
        );
        return $stmt->execute();
    }

    public function changePassword($userId, $newPassword) {
        $sql = "UPDATE {$this->table} SET password = ? WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $newPassword, $userId);
        return $stmt->execute();
    }

    public function getAll() {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}