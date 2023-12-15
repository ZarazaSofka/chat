<?php

class MessageService {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    public function create($chat_id, $user_id, $text) {
        if (!isset($chat_id) || !is_numeric($chat_id)) {
            return "Неверный чат";
        }
    
        if (!isset($user_id) || !is_numeric($user_id)) {
            return "Неверный пользователь";
        }
    
        if (!isset($text) || !strlen($text) > 0) {
            return "Неверный формат сообщения";
        }
        $sql = "INSERT INTO message (chat_id, user_id, text) VALUES (:chat_id, :user_id, :text)";
        $params = array(":chat_id" => $chat_id, ":user_id" => $user_id, ":text" => $text);

        $this->db->query($sql, $params);
    }

    public function getMessages($chat_id) {
        $sql = "SELECT m.*, u.username FROM message AS m JOIN user AS u ON m.user_id = u.id WHERE m.chat_id = :chat_id ORDER BY m.create_date";
        $params = array(":chat_id" => $chat_id);
        $result = $this->db->query($sql, $params);

        $messages = array();
        if ($result) {
            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $messageData) {
                $messages[] = new Message($messageData['chat_id'], $messageData['id'], $messageData['user_id'], $messageData['username'], $messageData['text'], $messageData['create_date']);
            }
        }

        return $messages;
    }
}