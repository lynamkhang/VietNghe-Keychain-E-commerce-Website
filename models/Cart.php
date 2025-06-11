<?php
require_once __DIR__ . '/Model.php';

class Cart extends Model {
    protected $table = 'cart_items';
    protected $primaryKey = 'cart_item_id';

    public function getCartItems($userId) {
        $sql = "SELECT ci.*, p.name, p.price, p.image_url, p.stock_quantity 
                FROM cart_items ci 
                JOIN shopping_carts sc ON ci.cart_id = sc.cart_id 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE sc.user_id = ? AND p.deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addToCart($userId, $productId, $quantity) {
        // Check stock quantity first
        $sql = "SELECT stock_quantity FROM products WHERE product_id = ? AND deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            throw new Exception("Product not available");
        }

        if ($quantity > $product['stock_quantity']) {
            throw new Exception("Not enough stock available");
        }

        // Get or create shopping cart
        $cartId = $this->getOrCreateCart($userId);
        
        // Check if item already exists in cart
        $sql = "SELECT ci.*, p.stock_quantity 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.cart_id = ? AND ci.product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $cartId, $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingItem = $result->fetch_assoc();

        if ($existingItem) {
            // Update quantity if item exists
            $newQuantity = $existingItem['quantity'] + $quantity;
            if ($newQuantity > $product['stock_quantity']) {
                throw new Exception("Not enough stock available");
            }
            return $this->update($existingItem['cart_item_id'], ['quantity' => $newQuantity]);
        } else {
            // Add new item if it doesn't exist
            return $this->create([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }
    }

    public function updateQuantity($cartItemId, $quantity) {
        // Get the current cart item and product details
        $sql = "SELECT ci.*, p.stock_quantity 
                FROM cart_items ci 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE ci.cart_item_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cartItemId);
        $stmt->execute();
        $result = $stmt->get_result();
        $item = $result->fetch_assoc();

        if (!$item) {
            throw new Exception("Cart item not found");
        }

        if ($quantity > $item['stock_quantity']) {
            throw new Exception("Cannot exceed available stock quantity (" . $item['stock_quantity'] . ")");
        }

        return $this->update($cartItemId, ['quantity' => $quantity]);
    }

    private function getOrCreateCart($userId) {
        // Check if user has an active cart
        $sql = "SELECT cart_id FROM shopping_carts WHERE user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart = $result->fetch_assoc();

        if ($cart) {
            return $cart['cart_id'];
        }

        // Create new cart if none exists
        $sql = "INSERT INTO shopping_carts (user_id) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function removeFromCart($cartItemId) {
        return $this->delete($cartItemId);
    }

    public function clearCart($userId) {
        $cartId = $this->getOrCreateCart($userId);
        $sql = "DELETE FROM cart_items WHERE cart_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $cartId);
        return $stmt->execute();
    }

    public function getCartTotal($userId) {
        $sql = "SELECT SUM(ci.quantity * p.price) as total 
                FROM cart_items ci 
                JOIN shopping_carts sc ON ci.cart_id = sc.cart_id 
                JOIN products p ON ci.product_id = p.product_id 
                WHERE sc.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getCartItemCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM cart_items ci 
                JOIN shopping_carts sc ON ci.cart_id = sc.cart_id 
                WHERE sc.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'] ?? 0;
    }
} 