<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';

class AdminController extends Controller {
    private $userModel;
    private $productModel;
    private $orderModel;

    public function __construct() {
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->requireAdmin();
    }

    public function index() {
        $this->render('admin/dashboard', [
            'totalUsers' => $this->userModel->count(),
            'totalProducts' => $this->productModel->count(),
            'totalOrders' => $this->orderModel->count(),
            'recentOrders' => $this->orderModel->getRecentOrders(5)
        ]);
    }

    // User Management
    public function users() {
        $users = $this->userModel->getAll();
        $this->render('admin/users/index', ['users' => $users]);
    }

    public function editUser($id) {
        $user = $this->userModel->findById($id);
        if (!$user) {
            $this->redirect('/admin/users?error=User not found');
        }
        
        $error = null;
        $success = null;
        
        $this->render('admin/users/edit', [
            'user' => $user,
            'basePath' => $this->basePath,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function updateUser($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/users');
        }

        $data = $this->getPostData();
        try {
            // Validate required fields
            if (empty($data['username']) || empty($data['email']) || empty($data['first_name']) || 
                empty($data['last_name']) || empty($data['role'])) {
                throw new Exception('Please fill in all required fields');
            }

            // Validate email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format');
            }

            // Handle password update
            if (!empty($data['new_password'])) {
                if (strlen($data['new_password']) < 6) {
                    throw new Exception('Password must be at least 6 characters long');
                }
                $data['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            }
            unset($data['new_password']); // Remove new_password from data array

            // Prepare update data with all fields
            $updateData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'role' => $data['role'],
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null
            ];

            // Add password to update data if it was changed
            if (isset($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            // Update the user
            if ($this->userModel->update($id, $updateData)) {
                $this->redirect('/admin/users?success=updated');
            } else {
                throw new Exception('Failed to update user');
            }
        } catch (Exception $e) {
            // If update fails, redirect back with error
            $this->redirect('/admin/users?error=' . urlencode($e->getMessage()));
        }
    }

    public function deleteUser($id) {
        try {
            // Prevent deleting the last admin
            if ($this->userModel->findById($id)['role'] === 'admin') {
                $adminCount = $this->userModel->countAdmins();
                if ($adminCount <= 1) {
                    $this->redirect('/admin/users?error=Cannot delete the last admin user');
                    return;
                }
            }

            // Delete the user and all related data
            if ($this->userModel->deleteUser($id)) {
                $this->redirect('/admin/users?success=deleted');
            } else {
                $this->redirect('/admin/users?error=delete_failed');
            }
        } catch (Exception $e) {
            error_log("Error deleting user: " . $e->getMessage());
            $this->redirect('/admin/users?error=delete_failed');
        }
    }

    // Product Management
    public function products() {
        $products = $this->productModel->getAll();
        $this->render('admin/products/index', ['products' => $products]);
    }

    public function createProduct() {
        $error = null;
        $success = null;
        
        if ($this->isPost()) {
            $data = $this->getPostData();
            try {
                // Validate required fields
                if (empty($data['name']) || empty($data['price']) || !isset($data['stock_quantity'])) {
                    throw new Exception('Please fill in all required fields');
                }
                
                // Validate price
                if (!is_numeric($data['price']) || $data['price'] < 0) {
                    throw new Exception('Price must be a valid positive number');
                }
                
                // Validate stock quantity
                if (!is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
                    throw new Exception('Stock quantity must be a valid positive number');
                }
                
                $productId = $this->productModel->create($data);
                if ($productId) {
                    $this->redirect('/admin/products?success=created');
                } else {
                    throw new Exception('Failed to create product');
                }
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        $this->render('admin/products/create', [
            'basePath' => $this->basePath,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function editProduct($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            $this->redirect('/admin/products?error=Product not found');
        }
        
        $error = null;
        $success = null;
        
        // Handle POST request (form submission)
        if ($this->isPost()) {
            $data = $this->getPostData();
            try {
                // Validate required fields
                if (empty($data['name']) || empty($data['price']) || !isset($data['stock_quantity'])) {
                    throw new Exception('Please fill in all required fields');
                }
                
                // Validate price
                if (!is_numeric($data['price']) || $data['price'] < 0) {
                    throw new Exception('Price must be a valid positive number');
                }
                
                // Validate stock quantity
                if (!is_numeric($data['stock_quantity']) || $data['stock_quantity'] < 0) {
                    throw new Exception('Stock quantity must be a valid positive number');
                }
                
                // Convert numeric values to proper types
                $data['price'] = floatval($data['price']);
                $data['stock_quantity'] = intval($data['stock_quantity']);
                
                // Update the product
                $updateResult = $this->productModel->update($product['product_id'], $data);
                if ($updateResult) {
                    // Redirect to products list with success message
                    $this->redirect('/admin/products?success=Product updated successfully');
                } else {
                    error_log("Failed to update product {$product['product_id']}: " . print_r($data, true));
                    throw new Exception('Failed to update product. Please try again.');
                }
                
            } catch (Exception $e) {
                error_log("Error updating product {$product['product_id']}: " . $e->getMessage());
                $error = $e->getMessage();
                // Refresh product data to show current information
                $product = $this->productModel->findById($id);
            }
        }
        
        $this->render('admin/products/edit', [
            'product' => $product,
            'basePath' => $this->basePath,
            'error' => $error,
            'success' => $success
        ]);
    }

    public function updateProduct($id) {
        // This method is now handled by editProduct method above
        // You can remove this method or redirect to editProduct
        $this->redirect('/admin/products/edit/' . $id);
    }

    public function deleteProduct($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/products');
        }

        try {
            $this->productModel->softDelete($id);
            $this->redirect('/admin/products?success=deleted');
        } catch (Exception $e) {
            $this->redirect('/admin/products?error=' . urlencode($e->getMessage()));
        }
    }

    // Order Management
    public function orders() {
        $status = $_GET['status'] ?? null;
        $orders = $status ? $this->orderModel->getOrdersByStatus($status) : $this->orderModel->getAll();
        $this->render('admin/orders/index', [
            'orders' => $orders,
            'currentStatus' => $status,
            'totalPending' => $this->orderModel->countOrdersByStatus('pending'),
            'totalProcessing' => $this->orderModel->countOrdersByStatus('processing'),
            'totalShipped' => $this->orderModel->countOrdersByStatus('shipped'),
            'totalDelivered' => $this->orderModel->countOrdersByStatus('delivered'),
            'totalCancelled' => $this->orderModel->countOrdersByStatus('cancelled')
        ]);
    }

    public function viewOrder($id) {
        $order = $this->orderModel->getOrderDetails($id);
        if (!$order) {
            $this->redirect('/admin/orders');
        }
        
        // Get customer details
        $user = $this->userModel->findById($order['user_id']);
        $order['customer_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $order['customer_email'] = $user['email'];
        
        $this->render('admin/orders/view', ['order' => $order]);
    }

    public function updateOrderStatus($id) {
        if (!$this->isPost()) {
            $this->redirect('/admin/orders');
        }

        $data = $this->getPostData();
        try {
            $this->orderModel->updateOrderStatus($id, $data['status']);
            
            // Send email notification to customer
            $order = $this->orderModel->getOrderDetails($id);
            $user = $this->userModel->findById($order['user_id']);
            $this->sendOrderStatusUpdateEmail($user['email'], $order['order_id'], $data['status']);
            
            $this->redirect('/admin/orders?success=updated');
        } catch (Exception $e) {
            error_log('Error updating order status: ' . $e->getMessage());
            $this->redirect('/admin/orders?error=update_failed');
        }
    }

    private function sendOrderStatusUpdateEmail($email, $orderId, $status) {
        // Implement email sending functionality here
        // This is a placeholder for the email sending logic
        $subject = "Order #$orderId Status Update";
        $message = "Your order #$orderId has been updated to: " . ucfirst($status);
        
        // You would typically use a proper email service/library here
        mail($email, $subject, $message);
    }

    // Helper method for status badge classes - ADDED THIS METHOD
    protected function getStatusBadgeClass($status) {
        switch ($status) {
            case 'pending':
                return 'warning';
            case 'processing':
                return 'info';
            case 'shipped':
                return 'primary';
            case 'delivered':
                return 'success';
            case 'cancelled':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    protected function requireAdmin() {
        $this->requireAuth();
        if (!isset($_SESSION['user']['role']) || $_SESSION['user']['role'] !== 'admin') {
            $this->redirect('/');
        }
    }
}