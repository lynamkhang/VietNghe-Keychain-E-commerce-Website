<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/User.php';

class CheckoutController extends Controller {
    private $cartModel;
    private $orderModel;

    public function __construct() {
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
    }

    public function index() {
        $this->requireAuth();
        $userId = $this->getCurrentUserId();
        $cartItems = $this->cartModel->getCartItems($userId);
        $total = $this->cartModel->getCartTotal($userId);

        if (empty($cartItems)) {
            $this->redirect('/cart');
        }

        // Get user's address information
        $userModel = new User();
        $user = $userModel->findById($userId);

        $this->render('checkout/index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'user' => $user
        ]);
    }

    public function process() {
        $this->requireAuth();
        if (!$this->isPost()) {
            $this->redirect('/checkout');
        }

        $userId = $this->getCurrentUserId();
        $data = $this->getPostData();

        // Validate required fields
        $requiredFields = ['address', 'city', 'country'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $_SESSION['error'] = 'Please fill in all required fields.';
                $this->redirect('/checkout');
            }
        }

        try {
            // Get cart items
            $cartItems = $this->cartModel->getCartItems($userId);
            if (empty($cartItems)) {
                throw new Exception('Cart is empty');
            }

            // Create shipping info array
            $shippingInfo = [
                'address' => $data['address'],
                'city' => $data['city'],
                'country' => $data['country']
            ];

            // Create order
            $orderId = $this->orderModel->createOrder($userId, $cartItems, $shippingInfo);
            
            if (!$orderId) {
                throw new Exception('Failed to create order');
            }

            // Clear cart
            $this->cartModel->clearCart($userId);

            // Set success message
            $_SESSION['success'] = 'Order placed successfully!';

            // Redirect to order confirmation
            $this->redirect("/orders/{$orderId}");
        } catch (Exception $e) {
            // Log the error
            error_log('Error creating order: ' . $e->getMessage());
            
            // Set error message for user
            $_SESSION['error'] = 'Sorry, there was a problem processing your order. Please try again.';
            
            $this->redirect('/checkout');
        }
    }
} 