<?php
class User {
    public $id;
    public $username;
    public $email;
    private $passwordHash;
    public $role;
    public $chatRole;

    public $action_time;

    public function __construct($id, $username, $email, $passwordHash, $role, $chatRole, $action_time) {
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->chatRole = $chatRole;
        $this->action_time = $action_time;
    }

    public function verifyPassword($enteredPassword) {
        return password_verify($enteredPassword, $this->passwordHash);
    }    
}