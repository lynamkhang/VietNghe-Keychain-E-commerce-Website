<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController extends Controller {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        $products = $this->productModel->findAll();
        $this->render('products/list', ['products' => $products]);
    }

    public function show($id) {
        $product = $this->productModel->findById($id);
        if (!$product) {
            $this->redirect('/404');
        }
        $this->render('products/detail', ['product' => $product]);
    }

    public function search() {
        if (!$this->isGet()) {
            $this->redirect('/');
        }

        $keyword = $this->getQueryParams()['keyword'] ?? '';
        $products = $this->productModel->searchProducts($keyword);
        $this->render('products/list', [
            'products' => $products,
            'keyword' => $keyword
        ]);
    }
} 