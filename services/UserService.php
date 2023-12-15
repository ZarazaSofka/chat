<?php

class UserService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function register($username, $email, $password) {
        if (strlen($password) < 8) {
            return "Минимальная длина пароля - 8";
        }
        $sql = "SELECT * FROM user WHERE username = :username";
        $params = array(":username" => $username);
        $result = $this->db->query($sql, $params);

        if ($result && $result->rowCount() > 0) {
            return "Данный логин уже существует";
        }
    
        $sql = "SELECT * FROM user WHERE email = :email";
        $params = array(":email" => $email);
        $result = $this->db->query($sql, $params);

        if ($result && $result->rowCount() > 0) {
            return "Данная почта уже использовалась";
        }
    
        $sql = "INSERT INTO user (username, email, password) VALUES (:username, :email, :password)";
        $params = array(":username" => $username, ":email" => $email, ":password" => password_hash($password, PASSWORD_DEFAULT));
    
        if (!$this->db->query($sql, $params)) {
            return "Ошибка регистрации";
        }
    }

    public function authorize($username, $password) {
        $sql = "SELECT * FROM user WHERE username = :username";
        $params = array(":username" => $username);
        $result = $this->db->query($sql, $params);

        if ($result->rowCount() == 0) {
            return ["Неверный логин", 0];
        }
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $user = new User($row['id'], $row['username'], $row['email'], $row['password'], $row['role'], null, $row["action_time"]);
        return [$user->verifyPassword($password) ? null : "Неверный пароль", $user->id, $user->role];
    }

    public function getById($user_id) {
        $sql = "SELECT * FROM user WHERE id = :id";
        $params = array(":id" => $user_id);
        $result = $this->db->query($sql, $params);

        if ($result->rowCount() == 0) {
            return null;
        }
        $row = $result->fetch(PDO::FETCH_ASSOC);
        return new User($row['id'], $row['username'], $row['email'], $row['password'], $row['role'], null, $row["action_time"]);
    }

    public function getUsers() {
        $query = "SELECT * FROM user";
        $result = $this->db->query($query);

        $users = array();
        if ($result) {
            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $users[] = new User($row['id'], $row['username'], $row['email'], null, $row['role'], null, $row["action_time"]);
            }
        }

        return $users;
    }

    public function delete($userId) {
        $sql = "DELETE FROM user WHERE id = :id";
        $params = array(":id"=> $userId);
        $this->db->query($sql, $params);
    }
}