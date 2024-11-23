<?php
namespace App\Controllers;

use App\Models\Product;

class PageController extends BaseController
{
    public function __construct()
    {
        $this->startSession(); // Ensures session is started
    }

    public function dashboard()
    {
        $productModel = new Product();
        $products = $productModel->getAllProducts();

        $data = [
            'products' => $products        
        ];
        // Now uses the renderPage method from BaseController
        echo $this->renderPage('dashboard', $data);
    }

    public function manageProducts()
    {
        $productModel = new Product();
        $products = $productModel->getAllProducts();

        $data = [
            'products' => $products        
        ];
        // Now uses the renderPage method from BaseController
        echo $this->renderPage('managed-products', $data);
    }
}
