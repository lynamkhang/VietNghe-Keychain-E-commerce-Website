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

    public function deleteUser($userId) {
        try {
            $this->db->begin_transaction();

            // 1. Delete user's cart items
            $sql = "DELETE ci FROM cart_items ci 
                    JOIN shopping_carts sc ON ci.cart_id = sc.cart_id 
                    WHERE sc.user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // 2. Delete user's order items
            $sql = "DELETE oi FROM order_items oi 
                    JOIN orders o ON oi.order_id = o.order_id 
                    WHERE o.user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // 3. Delete user's orders
            $sql = "DELETE FROM orders WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // 4. Delete user's shopping carts
            $sql = "DELETE FROM shopping_carts WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // 5. Update orders to set processed_by to NULL if the user was processing them
            $sql = "UPDATE orders SET processed_by = NULL WHERE processed_by = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // 6. Update products to set created_by to NULL if the user created them
            $sql = "UPDATE products SET created_by = NULL WHERE created_by = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            // 7. Finally, delete the user
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error deleting user: " . $e->getMessage());
            throw $e;
        }
    }

    public function countAdmins() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE role = 'admin'";
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return $row['count'];
    }
}