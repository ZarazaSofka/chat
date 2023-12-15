<?php

class Message {
    public $chat_id;
    public $id;
    public $user_id;
    public $username;
    public $text;
    public $create_date;
    public function __construct($chat_id, $id, $user_id, $username, $text, $create_date) {
        $this->chat_id = $chat_id;
        $this->id = $id;
        $this->user_id = $user_id;
        $this->username = $username;
        $this->text = $text;
        $this->create_date = $create_date;
    }
}