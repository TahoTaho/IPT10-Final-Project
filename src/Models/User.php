<?php

namespace App\Models;

use \PDO;
use App\Models\BaseModel;

class User extends BaseModel
{
    public function save($username, $email, $first_name, $last_name, $password) {
        $sql = "INSERT INTO users (username, email, first_name, last_name, password_hash) 
                VALUES (:username, :email, :first_name, :last_name, :password_hash)";
        
        $statement = $this->db->prepare($sql);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Bind parameters and execute
        $this->bindAndExecute($statement, [
            ':username' => $username,
            ':email' => $email,
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':password_hash' => $hashed_password,
        ]);
        
        return $statement->rowCount();
    }

    public function getAllUsers()
    {
        $query = "SELECT id, first_name, last_name, email FROM users";
        return $this->fetchAll($query);
    }

    public function getPassword($username) {
        $sql = "SELECT password_hash FROM users WHERE username = :username";
        $statement = $this->db->prepare($sql);
        $statement->execute(['username' => $username]);
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['password_hash'] ?? null;
    }

    public function getData() {
        $sql = "SELECT * FROM users";
        return $this->fetchAll($sql, '\App\Models\User');
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
}
