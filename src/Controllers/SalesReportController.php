<?php
namespace App\Controllers;

use App\Models\Sales;
use FPDF;

class SalesReportController extends BaseController
{
    public function __construct()
    {
        $this->startSession(); // Ensures session is started
    }

    public function showSalesByDate()
    {
        $data = [
            'title' => 'Monthly Sales'
        ];

        // Now uses the renderPage method from BaseController
        echo $this->renderPage('sales-by-date', $data);
    }

    public function showMonthlySales()
    {
        $salesModel = new Sales();
        $salesData = $salesModel->getMonthlySales();

        $data = [
            'title' => 'Monthly Sales',
            'sales' => $salesData,        
        ];

        // Now uses the renderPage method from BaseController
        echo $this->renderPage('sales', $data);
    }

    public function showDailySales()
    {
        $salesModel = new Sales();
        $salesData = $salesModel->getDailySales();

        $data = [
            'title' => 'Daily Sales',
            'sales' => $salesData,        
        ];

        // Now uses the renderPage method from BaseController
        echo $this->renderPage('sales', $data);
    }

    public function showSalesByDateRange()
    {
        // Get the start and end date from the POST request
    $startDate = $_POST['start-date'] ?? null;
    $endDate = $_POST['end-date'] ?? null;

    // Initialize the sales model
    $salesModel = new Sales();

    // Fetch sales data based on the selected date range
    $salesData = $salesModel->getSalesByDateRange($startDate, $endDate);

    // Format Grand Total and Profit
    $grandTotal = number_format($salesData['grand_total'], 2, '.', ',');
    $profit = number_format($salesData['profit'], 2, '.', ',');

    // Pass data to the view
    $data = [
        'title' => 'Sales Report',
        'sales' => $salesData['sales'],
        'grand_total' => $grandTotal,
        'profit' => $profit,
        'start_date' => $startDate,  // Pass start date to the view
        'end_date' => $endDate,      // Pass end date to the view
    ];

    // Render the page
    echo $this->renderPage('sales-by-date', $data);
    }

    public function exportSalesToPDF()
    {
        // Get the start and end date from the GET parameters
        $startDate = $_GET['start-date'] ?? null;
        $endDate = $_GET['end-date'] ?? null;

        // Initialize the Sales object
        $salesModel = new Sales();
        
        // Fetch sales data for the specified date range
        $salesData = $salesModel->getSalesByDateRange($startDate, $endDate);

        // Format the grand total and profit
        $grandTotal = number_format($salesData['grand_total'], 2, '.', ',');
        $profit = number_format($salesData['profit'], 2, '.', ',');

        // Create a new FPDF instance
        $pdf = new FPDF();
        $pdf->AddPage();

        // Set title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(190, 10, 'Sales Report', 0, 1, 'C');

        // Set date range
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(190, 10, 'Date Range: ' . $startDate . ' to ' . $endDate, 0, 1, 'C');
        $pdf->Ln(10);

        // Table header for sales data
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'Date', 1);
        $pdf->Cell(40, 10, 'Product Title', 1);
        $pdf->Cell(30, 10, 'Buying Price', 1);
        $pdf->Cell(30, 10, 'Selling Price', 1);
        $pdf->Cell(30, 10, 'Quantity Sold', 1);
        $pdf->Cell(30, 10, 'Total', 1);
        $pdf->Ln();

        // Add sales data to the PDF
        $pdf->SetFont('Arial', '', 12);
        foreach ($salesData['sales'] as $sale) {
            $pdf->Cell(30, 10, $sale['sale_date'], 1);
            $pdf->Cell(40, 10, $sale['product_name'], 1);
            $pdf->Cell(30, 10, number_format($sale['buy_price'], 2), 1);
            $pdf->Cell(30, 10, number_format($sale['sale_price'], 2), 1);
            $pdf->Cell(30, 10, $sale['quantity_sold'], 1);
            $pdf->Cell(30, 10, number_format($sale['total'], 2), 1);
            $pdf->Ln();
        }

        // Add grand total and profit
        $pdf->Cell(160, 10, 'Grand Total', 1);
        $pdf->Cell(30, 10, $grandTotal, 1, 1, 'R');
        
        $pdf->Cell(160, 10, 'Profit', 1);
        $pdf->Cell(30, 10, $profit, 1, 1, 'R');

        // Output the PDF (either download or display)
        $pdf->Output('D', 'sales_report_' . $startDate . '_to_' . $endDate . '.pdf'); // 'D' forces download
    }
}
