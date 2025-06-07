<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';

class HomeController extends Controller {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        // Get latest products for the homepage
        $latestProducts = $this->productModel->findAll();
        $latestProducts = array_slice($latestProducts, 0, 4); // Get only 4 latest products

        $this->render('home/index', [
            'latestProducts' => $latestProducts
        ]);
    }
}