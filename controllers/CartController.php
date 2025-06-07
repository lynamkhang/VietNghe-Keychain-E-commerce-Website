<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Cart.php';

class CartController extends Controller {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    public function index() {
        $this->requireAuth();
        $userId = $this->getCurrentUserId();
        $cartItems = $this->cartModel->getCartItems($userId);
        $total = $this->cartModel->getCartTotal($userId);
        $this->render('cart/index', ['cartItems' => $cartItems, 'total' => $total]);
    }

    public function add() {
        try {
            $this->requireAuth();
            if (!$this->isPost()) {
                throw new Exception('Invalid request method');
            }

            $data = $this->getPostData();
            if (empty($data['product_id'])) {
                throw new Exception('Product ID is required');
            }

            $userId = $this->getCurrentUserId();
            $this->cartModel->addToCart($userId, $data['product_id'], $data['quantity'] ?? 1);

            if ($this->isAjaxRequest()) {
                echo json_encode(['success' => true]);
                exit;
            } else {
                $this->redirect('/cart');
            }
        } catch (Exception $e) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                exit;
            } else {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('/cart');
            }
        }
    }

    public function update() {
        try {
            header('Content-Type: application/json');
            
            if (!$this->isPost()) {
                throw new Exception('Invalid request method');
            }

            $this->requireAuth();

            $data = $this->getPostData();
            if (empty($data['cart_item_id']) || !isset($data['quantity'])) {
                throw new Exception('Missing required fields');
            }

            $userId = $this->getCurrentUserId();
            $success = $this->cartModel->updateQuantity($data['cart_item_id'], $data['quantity']);
            
            if (!$success) {
                throw new Exception('Failed to update quantity');
            }

            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function remove() {
        try {
            header('Content-Type: application/json');
            
            if (!$this->isPost()) {
                throw new Exception('Invalid request method');
            }

            $this->requireAuth();

            $data = $this->getPostData();
            if (empty($data['cart_item_id'])) {
                throw new Exception('Missing cart item ID');
            }

            $userId = $this->getCurrentUserId();
            $success = $this->cartModel->removeFromCart($data['cart_item_id']);
            
            if (!$success) {
                throw new Exception('Failed to remove item');
            }

            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function clear() {
        $this->requireAuth();
        if (!$this->isPost()) {
            $this->redirect('/cart');
        }

        $userId = $this->getCurrentUserId();
        $this->cartModel->clearCart($userId);
        $this->redirect('/cart');
    }

    protected function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
} 