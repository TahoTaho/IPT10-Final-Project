<?php
namespace App\Controllers;

use App\Models\Sales;
use App\Models\Product;

class SalesController extends BaseController
{
    public function __construct()
    {
        $this->startSession(); // Ensures session is started
    }

    public function showAddSales()
    {
        $data = [
            'title' => 'Dashboard',
            'username' => 'John Doe', // For navbar welcome text
            'student' => 'Kyle Mathew P. Salvador', // Specific to dashboard
        ];
        echo $this->renderPage('add-sales', $data);
    }

    public function showManageSales()
    {
        $salesModel = new Sales();
        $salesData = $salesModel->getAllSales();
        $data = [
            'sales' => $salesData,
            'message' => $_SESSION['msg'] ?? null,
            'msg_type' => $_SESSION['msg_type'] ?? null
        ];
        unset($_SESSION['msg'], $_SESSION['msg_type']);
        echo $this->renderPage('manage-sales', $data);
    }

    public function editSale()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid sale ID.';
            $_SESSION['msg_type'] = 'danger';
            $this->redirect('/manage-sales');
        }

        $salesModel = new Sales();
        $sale = $salesModel->getSaleById($id);
        if (!$sale) {
            $_SESSION['msg'] = 'Sale not found.';
            $_SESSION['msg_type'] = 'danger';
            $this->redirect('/manage-sales');
        }

        $productModel = new Product();
        $product = $productModel->getProductById($sale['product_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_name = $_POST['product_name'] ?? $product['name']; // Fallback to existing product name
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];
            $sale_date = $_POST['sale_date'];

            // Now, update the product's name in the products table
            $updateProduct = $productModel->updateProductName($product['id'], $product_name);

            if (!$updateProduct) {
                $_SESSION['msg'] = 'Failed to update product name.';
                $_SESSION['msg_type'] = 'danger';
            }

            // Proceed to update the sale
            $result = $salesModel->updateSale($id, $product['id'], $quantity, $price, $sale_date);

            if ($result > 0) {
                $_SESSION['msg'] = 'Sale updated successfully.';
                $_SESSION['msg_type'] = 'success';
            } else {
                $_SESSION['msg'] = 'Failed to update sale.';
                $_SESSION['msg_type'] = 'danger';
            }

            $this->redirect('/manage-sales');
        }

        $data = [
            'title' => 'Edit Sale',
            'sale' => $sale,
            'product' => $product,
            'sale_id' => $sale['id']
        ];
        echo $this->renderPage('edit-sale', $data);
    }

    public function deleteSale() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid sale ID.';
            $_SESSION['msg_type'] = 'danger'; // Set message type to error
            $this->redirect('/manage-sales');
        }

        $salesModel = new Sales();
        $result = $salesModel->deleteSale($id);

        if ($result > 0) {
            $_SESSION['msg'] = 'Sale deleted successfully.';
            $_SESSION['msg_type'] = 'success'; 
        } else {
            $_SESSION['msg'] = 'Failed to delete sale.';
            $_SESSION['msg_type'] = 'danger'; // Set message type to error
        }

        $this->redirect('/manage-sales');
    }
}
