<?php 

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController {

    public function __construct()
     {
         $this->startSession(); // Ensures session is started
     }

    public function showLoginForm() {
        $this->startSession();
        return $this->render('login-form', [
            'remaining_attempts' => null,
            'form_disabled' => false // Default state for form
        ]);
    }

    public function login() {
        $this->startSession();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            $errors = $this->validateLogin($username, $password);
            if (!empty($errors)) {
                return $this->showLoginFormWithErrors($errors);
            }

            $user = new User();
            $saved_password_hash = $user->getPassword($username);

            if ($saved_password_hash && password_verify($password, $saved_password_hash)) {
                $this->successfulLogin($username);
            } else {
                return $this->handleFailedLogin();
            }
        }

        return $this->showLoginForm();
    }

    private function validateLogin($username, $password) {
        $errors = [];

        if (empty($username) || empty($password)) {
            $errors[] = "Username and password are required.";
        }

        return $errors;
    }

    private function successfulLogin($username) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['is_logged_in'] = true;
        $_SESSION['username'] = $username;

        header("Location: /welcome");
        exit;
    }

    private function handleFailedLogin() {
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $max_attempts = 3;

        if ($_SESSION['login_attempts'] >= $max_attempts) {
            $errors[] = "Too many failed login attempts. Please try again later.";
            return $this->showLoginFormWithErrors($errors, null, true); // Disable form
        } else {
            $remaining_attempts = $max_attempts - $_SESSION['login_attempts'];
            $errors[] = "Invalid username or password. Attempts remaining: $remaining_attempts.";
            return $this->showLoginFormWithErrors($errors, $remaining_attempts);
        }
    }

    private function showLoginFormWithErrors($errors, $remaining_attempts = null, $form_disabled = false) {
        return $this->render('login-form', [
            'errors' => $errors,
            'remaining_attempts' => $remaining_attempts,
            'form_disabled' => $form_disabled // Pass the disabled state
        ]);
    }

    public function logout() {
        $this->startSession();
        session_destroy();
        header("Location: /login-form");
        exit;
    }
}