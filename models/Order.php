<?php
require_once __DIR__ . '/Model.php';

class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'order_id';

    public function createOrder($userId, $cartItems, $shippingInfo) {
        $this->db->begin_transaction();

        try {
            // Get cart ID from the first cart item
            $cartId = $cartItems[0]['cart_id'];

            // Format shipping address
            $formattedAddress = $shippingInfo['address'] . "\n" . 
                              $shippingInfo['city'] . ", " . 
                              $shippingInfo['country'];

            // Create the order
            $orderData = [
                'user_id' => $userId,
                'cart_id' => $cartId,
                'total_amount' => $this->calculateTotal($cartItems),
                'shipping_address' => $formattedAddress,
                'status' => 'pending',
                'order_date' => date('Y-m-d H:i:s')
            ];

            $this->create($orderData);
            $orderId = $this->db->insert_id;

            // Create order items and update product stock
            foreach ($cartItems as $item) {
                // Check if enough stock is available
                if (!$this->checkStockAvailability($item['product_id'], $item['quantity'])) {
                    throw new Exception("Insufficient stock for product ID: " . $item['product_id']);
                }

                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ];
                $this->createOrderItem($orderItemData);

                // Decrease product stock
                $this->updateProductStock($item['product_id'], -$item['quantity']);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function createOrderItem($data) {
        $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiid", $data['order_id'], $data['product_id'], $data['quantity'], $data['price']);
        return $stmt->execute();
    }

    private function calculateTotal($cartItems) {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    private function checkStockAvailability($productId, $quantity) {
        $sql = "SELECT stock_quantity FROM products WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        return $product && $product['stock_quantity'] >= $quantity;
    }

    private function updateProductStock($productId, $quantityChange) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $quantityChange, $productId);
        return $stmt->execute();
    }

    public function getOrdersByUser($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY order_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOrderDetails($orderId) {
        // Get order information
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();
        
        if (!$order) {
            return null;
        }

        // Get order items with product details, including deleted status
        $sql = "SELECT oi.*, p.name, p.image_url, p.deleted 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order['items'] = $result->fetch_all(MYSQLI_ASSOC);

        return $order;
    }

    public function updateOrderStatus($orderId, $status) {
        $this->db->begin_transaction();

        try {
            $oldOrder = $this->getOrderDetails($orderId);
            if (!$oldOrder) {
                throw new Exception("Order not found");
            }

            // If cancelling an order, restore the product quantities
            if ($status === 'cancelled' && $oldOrder['status'] !== 'cancelled') {
                foreach ($oldOrder['items'] as $item) {
                    // Increase product stock (restore quantities)
                    $this->updateProductStock($item['product_id'], $item['quantity']);
                }
            }
            
            // If reactivating a cancelled order, decrease the product quantities again
            if ($oldOrder['status'] === 'cancelled' && $status !== 'cancelled') {
                foreach ($oldOrder['items'] as $item) {
                    // Check if enough stock is available
                    if (!$this->checkStockAvailability($item['product_id'], $item['quantity'])) {
                        throw new Exception("Insufficient stock to reactivate order for product ID: " . $item['product_id']);
                    }
                    // Decrease product stock
                    $this->updateProductStock($item['product_id'], -$item['quantity']);
                }
            }

            // Update the order status
            $result = $this->update($orderId, ['status' => $status]);
            if (!$result) {
                throw new Exception("Failed to update order status");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function getRecentOrders($limit = 5) {
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email as customer_email 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.order_date DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getOrdersByStatus($status) {
        $sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.email as customer_email 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE o.status = ?
                ORDER BY o.order_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll() {
        $sql = "SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name 
                FROM {$this->table} o 
                JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.order_date DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function countOrdersByStatus($status) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['count'];
    }
} 