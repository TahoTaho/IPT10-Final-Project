<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;
use App\Models\Product;

class Sales extends BaseModel
{
    // Save a new sale
    public function save($product_id, $qty, $date)
    {
        $sql = "INSERT INTO sales (product_id, qty, date) VALUES (:product_id, :qty, :date)";
        
        $statement = $this->db->prepare($sql);
        
        $this->bindAndExecute($statement, [
            ':product_id' => $product_id,
            ':qty' => $qty,
            ':date' => $date,
        ]);

        return $statement->rowCount();
    }

    public function getSalesByDateRange($startDate, $endDate)
    {
        if ($startDate && $endDate) {
            $sql = "
                SELECT
                    s.id AS sale_id,
                    p.name AS product_name,
                    p.buy_price,
                    p.sale_price,
                    s.qty AS quantity_sold,
                    (s.qty * p.sale_price) AS total,
                    (s.qty * p.buy_price) AS cost_of_goods_sold,
                    s.date AS sale_date
                FROM
                    sales s
                JOIN
                    products p ON s.product_id = p.id
                WHERE
                    s.date BETWEEN :start_date AND :end_date
                ORDER BY
                    s.date DESC;
            ";
    
            $statement = $this->db->prepare($sql);
            $statement->bindValue(':start_date', $startDate);
            $statement->bindValue(':end_date', $endDate);
            $statement->execute();
    
            // Fetch all sales data
            $salesData = $statement->fetchAll(PDO::FETCH_ASSOC);
    
            // Calculate Grand Total and Profit
            $grandTotal = 0;
            $profit = 0;
    
            foreach ($salesData as $sale) {
                $grandTotal += $sale['total'];
                $profit += ($sale['total'] - $sale['cost_of_goods_sold']);
            }
    
            // Return sales data along with the calculated totals
            return [
                'sales' => $salesData,
                'grand_total' => $grandTotal,
                'profit' => $profit
            ];
        }
        return [];
    }

    // Get all sales
    public function getAllSales()
    {
        $query = "
            SELECT
                s.id AS sale_id,
                p.name AS product_name,
                s.qty AS quantity,
                s.price,
                s.date AS sale_date
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            ORDER BY
                s.date DESC;
        ";
        return $this->fetchAll($query);
    }

    public function getMonthlySales()
    {
        $sql = "
            SELECT
                s.id AS sale_id,
                p.name AS product_name,
                s.qty AS quantity_sold,
                (s.qty * p.sale_price) AS total,  
                s.date AS sale_date
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            WHERE
                MONTH(s.date) = MONTH(CURRENT_DATE())  
            ORDER BY
                s.date DESC;
        ";

        return $this->fetchAll($sql);
    }

    public function getDailySales()
    {
        $sql = "
            SELECT
                s.id AS sale_id,
                p.name AS product_name,
                s.qty AS quantity_sold,
                (s.qty * p.sale_price) AS total,  
                s.date AS sale_date
            FROM
                sales s
            JOIN
                products p ON s.product_id = p.id
            WHERE
                DATE(s.date) = CURDATE() 
            ORDER BY
                s.date DESC;
        ";

        return $this->fetchAll($sql);
    }

    // Get sale by ID
    public function getSaleById($id)
    {
        $sql = "SELECT * FROM sales WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    // Update a sale
    public function updateSale($id, $product_id, $qty, $price, $date)
    {
        $sql = "UPDATE sales 
                SET product_id = :product_id, qty = :qty, price = :price, date = :date 
                WHERE id = :id";

        $statement = $this->db->prepare($sql);
        $this->bindAndExecute($statement, [
            ':id' => $id,
            ':product_id' => $product_id,
            ':qty' => $qty,
            ':price' => $price,
            ':date' => $date,
        ]);
        return $statement->rowCount(); // Return the number of rows affected
    }


    // Delete a sale
    public function deleteSale($id)
    {
        $sql = "DELETE FROM sales WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        return $statement->rowCount();
    }

    // Helper to fetch all records
    private function fetchAll($query, $class = null)
    {
        $statement = $this->db->prepare($query);
        $statement->execute();

        return $class ? $statement->fetchAll(PDO::FETCH_CLASS, $class) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Helper to bind and execute parameters
    private function bindAndExecute($statement, $parameters)
    {
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value);
        }
    
        try {
            $statement->execute();
        } catch (\PDOException $e) {
            error_log("Error executing statement: " . $e->getMessage());  // Log any error that occurs
            throw new \Exception("Error executing statement: " . $e->getMessage());
        }
    }
}
