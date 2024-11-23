<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Product extends BaseModel
{
    public function save($name, $quantity, $buy_price, $sale_price, $categorie_id, $media_id = 0) {
        $sql = "INSERT INTO products (name, quantity, buy_price, sale_price, categorie_id, media_id, date) 
                VALUES (:name, :quantity, :buy_price, :sale_price, :categorie_id, :media_id, NOW())";
        
        $statement = $this->db->prepare($sql);
        
        // Bind parameters and execute
        $this->bindAndExecute($statement, [
            ':name' => $name,
            ':quantity' => $quantity,
            ':buy_price' => $buy_price,
            ':sale_price' => $sale_price,
            ':categorie_id' => $categorie_id,
            ':media_id' => $media_id,
        ]);
        
        return $statement->rowCount();
    }

    public function getAllProducts()
    {
        $sql = "
            SELECT
                p.id AS product_id,
                p.name AS product_name,
                p.quantity,
                p.buy_price,
                p.sale_price,
                p.date AS product_date,
                c.name AS category_name,
                m.file_name AS media_file_name
            FROM
                products p
            JOIN
                categories c ON p.categorie_id = c.id
            LEFT JOIN
                media m ON p.media_id = m.id
            ORDER BY
                p.id;
        ";
        
        return $this->fetchAll($sql);
    }

    public function getProductById($id) {
        $sql = "SELECT p.id, p.name, p.quantity, p.buy_price, p.sale_price, p.date, c.name AS category, m.file_name
                FROM products p
                LEFT JOIN categories c ON p.categorie_id = c.id
                LEFT JOIN media m ON p.media_id = m.id
                WHERE p.id = :id";
        $statement = $this->db->prepare($sql);
        $statement->execute(['id' => $id]);
        
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    private function fetchAll($query, $class = null) {
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $class ? $statement->fetchAll(PDO::FETCH_CLASS, $class) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function bindAndExecute($statement, $parameters) {
        foreach ($parameters as $key => $value) {
            $statement->bindValue($key, $value);
        }
        
        try {
            $statement->execute();
        } catch (\PDOException $e) {
            throw new \Exception("Error executing statement: " . $e->getMessage());
        }
    }
    
    public function updateProductName($product_id, $product_name)
    {
        $sql = "UPDATE products SET name = :name WHERE id = :product_id";
        
        $statement = $this->db->prepare($sql);
        $statement->execute([
            ':name' => $product_name,
            ':product_id' => $product_id
        ]);

        return $statement->rowCount() > 0; // Returns true if the update was successful
    }
}
