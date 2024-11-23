<?php
namespace App\Controllers;

use App\Models\Category;

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->startSession(); // Ensures session is started
    }

    public function showCategories()
    {
        $categoryModel = new Category();
        $categories = $categoryModel->getAllCategories();
        $message = isset($_SESSION['msg']) ? $_SESSION['msg'] : '';

        // Debugging message value
        error_log("Session message: " . $message);

        $data = [
            'categories' => $categories,
            'message' => $message,
            'msg_type' => isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'info',  // Default type can be 'info'
        ];

        // Clear message from session after rendering it once
        unset($_SESSION['msg']);
        unset($_SESSION['msg_type']);

        echo $this->renderPage('categories', $data);
    }

    public function addCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
            $category_name = $_POST['category_name'];

            // Validate input
            if (empty($category_name)) {
                $_SESSION['msg'] = 'Category name cannot be empty.';
                $_SESSION['msg_type'] = 'danger';  // Set message type to error
            } else {
                $categoryModel = new Category();
                $result = $categoryModel->save($category_name);

                if ($result > 0) {
                    $_SESSION['msg'] = 'Category added successfully.';
                    $_SESSION['msg_type'] = 'success';  // Set message type to success
                } else {
                    $_SESSION['msg'] = 'Failed to add category.';
                    $_SESSION['msg_type'] = 'danger';  // Set message type to error
                }
            }
        }
        // Redirect back to the same page to display the message
        $this->redirect('/categories');
    }

    public function editCategory()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid category ID.';
            $_SESSION['msg_type'] = 'danger';  // Set message type to error
            $this->redirect('/categories');
        }

        $categoryModel = new Category();
        $category = $categoryModel->getCategoryById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_name'])) {
            $category_name = $_POST['category_name'];

            // Validate input
            if (empty($category_name)) {
                $_SESSION['msg'] = 'Category name cannot be empty.';
                $_SESSION['msg_type'] = 'danger';  // Set message type to error
                $this->redirect('/edit-category?id=' . $id);
            }

            $result = $categoryModel->update($id, $category_name);

            if ($result > 0) {
                $_SESSION['msg'] = 'Category updated successfully.';
                $_SESSION['msg_type'] = 'success';  // Set message type to success
            } else {
                $_SESSION['msg'] = 'Failed to update category.';
                $_SESSION['msg_type'] = 'danger';  // Set message type to error
            }

            $this->redirect('/categories');
        }

        $data = [
            'category' => $category
        ];
        echo $this->renderPage('edit-category', $data);
    }

    public function deleteCategory()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['msg'] = 'Invalid category ID.';
            $_SESSION['msg_type'] = 'danger';  // Set message type to error
            $this->redirect('/categories');
            return;
        }

        $categoryModel = new Category();
        $result = $categoryModel->delete($id);

        if ($result > 0) {
            $_SESSION['msg'] = 'Category deleted successfully.';
            $_SESSION['msg_type'] = 'success';  // Set message type to success
        } else {
            $_SESSION['msg'] = 'Failed to delete category.';
            $_SESSION['msg_type'] = 'danger';  // Set message type to error
        }

        $this->redirect('/categories');
    }
}
