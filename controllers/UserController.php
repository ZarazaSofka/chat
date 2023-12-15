<?php

class UserController {
    private $userService;

    public function __construct() {
        global $db;
        $this->userService = new UserService($db);
    }

    public function register() {
        $error = $this->userService->register($_POST["username"], $_POST["email"], $_POST["password"]);
        if (!isset($error)) {
            return header('Location: /login');
        }
        return header('Location: /register?error=' . $error);
    }

    public function registerPage() {
        view('register.php');
    }

    public function login() {
        list($error, $user_id, $role) = $this->userService->authorize($_POST["username"], $_POST["password"]);
        if (!isset($error)) {
            $_SESSION["user_id"] = $user_id;
            $_SESSION["role"] = $role;
            return header('Location: /');
        }
        return header('Location: /login?error=' . $error);
    }

    public function loginPage() {
        view('login.php');
    }

    public function logout() {
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        session_destroy();
        header('Location: /login');
    }

    public function getUsers() {
        $users = $this->userService->getUsers();
        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function deleteUser() {
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $this->userService->delete($data->user_id);
        header('Content-Type: application/json');
        echo json_encode(array('message'=> 'Удалено'));
    }

    public function usersPage() {
        view("users.php");
    }
}