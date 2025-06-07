<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController extends Controller {
    private $orderModel;

    public function __construct() {
        $this->orderModel = new Order();
    }

    public function index() {
        $this->requireAuth();
        $userId = $this->getCurrentUserId();
        $orders = $this->orderModel->getOrdersByUser($userId);
        $this->render('orders/list', [
            'orders' => $orders,
            'basePath' => $this->basePath
        ]);
    }

    public function show($id) {
        $this->requireAuth();
        $userId = $this->getCurrentUserId();
        $order = $this->orderModel->getOrderDetails($id);
        
        // Check if order exists and belongs to the current user
        if (!$order || $order['user_id'] != $userId) {
            $this->redirect($this->basePath . '/orders?error=Order not found');
            return;
        }
        
        // Parse shipping address if it's stored as a single string
        if (!isset($order['shipping_city']) && isset($order['shipping_address'])) {
            $addressParts = explode("\n", $order['shipping_address']);
            $order['shipping_address'] = $addressParts[0] ?? '';
            
            if (isset($addressParts[1])) {
                $cityCountry = explode(", ", $addressParts[1]);
                $order['shipping_city'] = $cityCountry[0] ?? '';
                $order['shipping_country'] = $cityCountry[1] ?? '';
            }
        }
        
        $this->render('orders/detail', [
            'order' => $order,
            'basePath' => $this->basePath
        ]);
    }

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
} 