<?php

class ChatService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getById($chat_id) {
        $sql = "SELECT * FROM chat WHERE id = :id";
        $params = array(':id' => $chat_id);
        $result = $this->db->query($sql, $params);
        
        if ($result?->rowCount() > 0) {
            $chatData = $result->fetch(PDO::FETCH_ASSOC);
            $chat = new Chat($chatData['id'], $chatData['name'], $chatData['is_public'], $chatData['description']);
            return $chat;
        }
        return null;
    }

    private function getChats($sql, $params) {
        $result = $this->db->query($sql, $params);
    
        $chats = array();
        if ($result) {
            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $chatData) {
                $chats[] = new Chat($chatData['id'], $chatData['name'], $chatData['is_public'], $chatData['description']);
            }
        }

        return $chats;
    }

    public function getPublicChats() {
        $sql = "SELECT * FROM chat WHERE is_public = true";    
        return $this->getChats($sql, array());
    }
    
    public function getUserChats($user_id) {
        $sql = "SELECT * FROM chat, (SELECT chat_id FROM chat_user WHERE user_id = :user_id) AS a WHERE a.chat_id = chat.id";
        $params = array(':user_id' => $user_id);
        return $this->getChats($sql, $params);
    }

    public function create($name, $public, $description) {
        $columns = [];
        $params = [];
        if (isset($name) && strlen($name) > 0) {
            $params[':name'] = $name;
            $columns[] = 'name';
        }
    
        if (isset($public)) {
            $params[':public'] = (int)$public;
            $columns[] = 'is_public';
        }
    
        if (isset($description) && strlen($description) > 0) {
            $params[':description'] = $description;
            $columns[] = 'description';
        }
        $sql = "INSERT INTO chat (" . implode(', ', $columns) . ") VALUES (" . implode(', ', array_keys($params)) . ")";
        if (!$this->db->query($sql, $params)) {
            return;
        }
        $chat_id = $this->db->getLastInsertId();

        $sql = "INSERT INTO chat_user (user_id, chat_id, role) VALUES (:user_id, :chat_id, 'OWNER')";
        global $user_id;
        $params = [":user_id" => $user_id,":chat_id"=> $chat_id];
        $this->db->query($sql, $params);
    }

    public function updateChatData($chat_id, $name, $description, $public) {
        if (!isset($description) && !isset($name) && !isset($public)) {
            return;
        }

        $chars = array();
        $params = array();
    
        if ($name !== null) {
            $chars[] = "name = :name";
            $params[':name'] = $name;
        }
        if ($description !== null) {
            $chars[] = "description = :description";
            $params[':description'] = $description;
        }
        if ($public !== null) {
            $chars[] = "is_public = :public";
            $params[':public'] = $public ? 1 : 0;
        }
    
        
        $sql = "UPDATE chat SET " . implode(", ", $chars) . " WHERE id = :id";
        $params[':id'] = $chat_id;    
        $this->db->query($sql, $params);
    }
    

    public function delete($chat_id) {
        $sql = "DELETE FROM chat WHERE id = :id";
        $params = array(':id' => $chat_id);
        $this->db->query($sql, $params);
    }

    public function leave($chat_id, $user_id) {
        $sql = "DELETE FROM chat_user WHERE chat_id = :chat_id AND user_id = :user_id";
        $params = array(':chat_id' => $chat_id, ':user_id'=> $user_id);
        $this->db->query($sql, $params);
    }

    public function addChatUser($chat_id, $user_id) {
        if ($this->getChatRole($user_id, $chat_id)) {
            return false;
        }
        
        $sql = "INSERT INTO chat_user (chat_id, user_id) VALUES (:chat_id, :user_id)";
        $params = array(':chat_id' => $chat_id, ':user_id'=> $user_id);
        $this->db->query($sql, $params);
        return true;
    }

    public function getChatUsers($chat_id) {
        $sql = "SELECT id, username, action_time, a.role FROM (SELECT user_id, role FROM chat_user WHERE chat_id=:chat_id) as a JOIN user ON a.user_id = user.id";
        $params = array(':chat_id' => $chat_id);
        $result = $this->db->query($sql, $params);
    
        $users = array();
        if ($result) {
            foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $userData) {
                $users[] = new User($userData['id'], $userData['username'], null, null, null, $userData["role"], $userData['action_time']);
            }
        }

        return $users;
    }

    public function getChatRole($user_id, $chat_id) {
        $sql = "SELECT role FROM chat_user WHERE chat_id=:chat_id AND user_id=:user_id";
        $params = array(':chat_id' => $chat_id, ':user_id'=> $user_id);
        $result = $this->db->query($sql, $params);
        if ($result->rowCount() > 0) {
            
            return $result->fetch(PDO::FETCH_ASSOC)['role'];
        }
        return false;
    }

    public function changeRole($chat_id, $user_id, $role) {
        error_log($chat_id .' '. $user_id .' '. $role);
        $sql = "UPDATE chat_user SET role = :role WHERE chat_id = :chat_id AND user_id = :user_id";
        $params = [":chat_id" => $chat_id, ":user_id"=> $user_id, ":role"=> $role];
        $this->db->query($sql, $params);
    }
}