<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class Category extends BaseModel
{
    // Save a new category
    public function save($category_name) {
        $sql = "INSERT INTO categories (name) VALUES (:name)";
        
        $statement = $this->db->prepare($sql);
        
        // Bind parameters and execute
        $this->bindAndExecute($statement, [
            ':name' => $category_name,
        ]);
        
        return $statement->rowCount();
    }

    // Get all categories
    public function getAllCategories()
    {
        $query = "SELECT id, name FROM categories ORDER BY id ASC";
        $categories = $this->fetchAll($query);
        
        // Add sequential index to categories
        foreach ($categories as $key => &$category) {
            $category['sequence'] = $key + 1; // Adding sequence starting from 1
        }
        
        return $categories;
    }

    // Fetch category data
    public function getCategoryData() {
        $sql = "SELECT * FROM categories";
        return $this->fetchAll($sql, '\App\Models\Category');
    }

    // Private helper method to fetch all data
    private function fetchAll($query, $class = null) {
        $statement = $this->db->prepare($query);
        $statement->execute();
        
        return $class ? $statement->fetchAll(PDO::FETCH_CLASS, $class) : $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    // Private helper method to bind and execute the query
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


    // Get category by ID
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }


    public function update($id, $category_name)
    {
        $sql = "UPDATE categories SET name = :name WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':name', $category_name);
        $statement->bindValue(':id', $id);
        $statement->execute();

        return $statement->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM categories WHERE id = :id";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(':id', $id);
        $statement->execute();
        
        return $statement->rowCount();
    }
}
