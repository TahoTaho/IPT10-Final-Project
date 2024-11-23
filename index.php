<?php

require "vendor/autoload.php";
require "init.php";

// Database connection object (from init.php (DatabaseConnection))
global $conn;

try {
    // Create Router instance
    $router = new \Bramus\Router\Router();

    // Define routes
    $router->get('/login-form', '\App\Controllers\LoginController@showLoginForm');
    $router->post('/login', '\App\Controllers\LoginController@login');
    $router->get('/logout', '\App\Controllers\LoginController@logout');


    $router->get('/', '\App\Controllers\PageController@dashboard');
    $router->get('/documentation', '\App\Controllers\PageController@documentation');


    $router->get('/manage-products', '\App\Controllers\PageController@manageProducts');


    // Define Categories routes
    $router->get('/categories', '\App\Controllers\CategoryController@showCategories');
    $router->post('/add-category', '\App\Controllers\CategoryController@addCategory');
    $router->get('/edit-category', '\App\Controllers\CategoryController@editCategory');  // For GET requests
    $router->post('/edit-category', '\App\Controllers\CategoryController@editCategory'); // For POST requests
    $router->get('/delete-category', '\App\Controllers\CategoryController@deleteCategory'); // For DELETE requests

    // Define Sales routes
    $router->get('/manage-sales', '\App\Controllers\SalesController@showManageSales');
    $router->get('/add-sales', '\App\Controllers\SalesController@showAddSales');
    $router->get('/edit-sale', '\App\Controllers\SalesController@editSale');
    $router->post('/edit-sale', '\App\Controllers\SalesController@editSale');
    $router->get('/delete-sale', '\App\Controllers\SalesController@deleteSale');

    // Define Sales Report routes
    $router->get('/sales-by-date', '\App\Controllers\SalesReportController@showSalesByDate');
    $router->get('/monthly-sales', '\App\Controllers\SalesReportController@showMonthlySales');
    $router->get('/daily-sales', '\App\Controllers\SalesReportController@showDailySales');
    $router->post('/sales-by-date', '\App\Controllers\SalesReportController@showSalesByDateRange');
    $router->get('/export-sales-pdf', '\App\Controllers\SalesReportController@exportSalesToPDF');


    // Run it!
    $router->run();

} catch (Exception $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
