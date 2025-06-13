<?php
require_once __DIR__ . '/Model.php';

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';

    public function __construct()
    {
        parent::__construct();
    }

    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted = 0 ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function searchProducts($keyword)
    {
        $keyword = $this->db->real_escape_string($keyword);
        $sql = "SELECT * FROM {$this->table} WHERE deleted = 0 AND (name LIKE '%{$keyword}%' OR description LIKE '%{$keyword}%')";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStock($id, $quantity)
    {
        $id = intval($id);
        $quantity = intval($quantity);
        $sql = "UPDATE {$this->table} SET stock_quantity = stock_quantity - {$quantity} WHERE {$this->primaryKey} = {$id} AND stock_quantity >= {$quantity} AND deleted = 0";
        return $this->db->query($sql);
    }

    public function getAll()
    {
        $sql = "SELECT * FROM {$this->table} WHERE deleted = 0 ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function count() {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE deleted = 0";
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }
        $row = $result->fetch_assoc();
        return $row['count'];
    }

    public function softDelete($id) {
        try {
            // Get product image URL before deletion
            $sql = "SELECT image_url FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            // Delete the image file if it exists
            if ($product && !empty($product['image_url'])) {
                $imagePath = __DIR__ . '/../public' . $product['image_url'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            // Check if product exists in any orders
            $sql = "SELECT COUNT(*) as count FROM order_items WHERE product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            // Mark the product as deleted
            $sql = "UPDATE {$this->table} SET deleted = 1 WHERE {$this->primaryKey} = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error soft deleting product: " . $e->getMessage());
            throw $e;
        }
    }

    public function create($data)
    {
        // Handle image upload
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/products/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedTypes)) {
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // FIXED: Use absolute path from web root
                    $imageUrl = '/vietnghe-keychain/public/uploads/products/' . $fileName;
                }
            }
        }

        // Get current user ID
        $created_by = null;
        if (isset($_SESSION['user']['user_id'])) {
            $created_by = $_SESSION['user']['user_id'];
        } elseif (isset($_SESSION['user']['id'])) {
            $created_by = $_SESSION['user']['id'];
        }

        // Ensure price is handled as an integer
        $data['price'] = (int)$data['price'];

        // Prepare data for insertion using prepared statements
        $sql = "INSERT INTO {$this->table} (name, description, price, color, material, stock_quantity, image_url, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->error);
        }

        $stmt->bind_param(
            "ssissisi",
            $data['name'],
            $data['description'],
            $data['price'],
            $data['color'],
            $data['material'],
            $data['stock_quantity'],
            $imageUrl,
            $created_by
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to create product: " . $stmt->error);
        }

        $stmt->close();
        return $this->db->insert_id;
    }

    public function update($id, $data)
    {
        // Handle image upload if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/products/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedTypes)) {
                $fileName = uniqid() . '.' . $fileExtension;
                $uploadFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                    // Delete old image if exists
                    $sql = "SELECT image_url FROM {$this->table} WHERE {$this->primaryKey} = ?";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $oldProduct = $result->fetch_assoc();

                    if ($oldProduct && !empty($oldProduct['image_url'])) {
                        $oldImagePath = __DIR__ . '/../public' . $oldProduct['image_url'];
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }

                    // Set new image URL
                    $data['image_url'] = '/vietnghe-keychain/public/uploads/products/' . $fileName;
                }
            }
        }

        // Ensure price is handled as an integer if it's being updated
        if (isset($data['price'])) {
            $data['price'] = (int)$data['price'];
        }

        // Build the SET part of the query
        $setParts = [];
        $types = '';
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($value !== null) {
                $setParts[] = "$key = ?";
                // Determine the type of the value
                if ($key === 'price') {
                    $types .= 'i'; // Use integer for price
                } elseif (is_int($value)) {
                    $types .= 'i';
                } else {
                    $types .= 's';
                }
                $values[] = $value;
            }
        }

        // Add the ID to the values array
        $types .= 'i';
        $values[] = $id;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE {$this->primaryKey} = ?";
        
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->db->error);
        }

        // Create the bind_param arguments
        $bindParams = array($types);
        foreach ($values as $key => $value) {
            $bindParams[] = &$values[$key];
        }
        
        call_user_func_array(array($stmt, 'bind_param'), $bindParams);

        $success = $stmt->execute();
        $stmt->close();
        
        return $success;
    }
}
