<?php

class ChatsController {
    private $chatService;
    private $messageService;
    
    public function __construct() {
        global $db;
        $this->chatService = new ChatService($db);
        $this->messageService = new MessageService($db);
    }

    public function joinChat() {
        global $user_id, $vars;
        header('Content-Type: application/json');
        if ($this->chatService->addChatUser($vars[0], $user_id)) {
            echo json_encode("success");
            return;
        }
        http_response_code(400);
        echo json_encode("failure");
    }

    public function createChat() {
        $jsonData = file_get_contents("php://input");
        $data = json_decode($jsonData, true);
        $this->chatService->create($data['name'], $data['public'], $data['description']);
        header('Content-Type: application/json');
        echo json_encode("success");
    }

    public function getPublicChats() {
        $chats = $this->chatService->getPublicChats();
        header('Content-Type: application/json');
        echo json_encode($chats);
    }

    public function getUserChats() {
        global $user_id;
        $chats = $this->chatService->getUserChats($user_id);
        header('Content-Type: application/json');
        echo json_encode($chats);
    }

    public function sendMessage() {
        global $user_id, $role, $vars;
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON data']);
            return;
        }

        $text = $data->text;
        if ($this->checkMembership() || $role == 'ADMIN') {
            $this->messageService->create($vars[0], $user_id, $text);
            echo json_encode(['message' => 'success']);
            return;
        }
        http_response_code(400);
    }

    public function getMessages() {
        global $vars, $role;
        if ($this->checkMembership() || $role == 'ADMIN') {
            $messages = $this->messageService->getMessages($vars[0]);
            header('Content-Type: application/json');
            echo json_encode($messages);
            return;
        }
        http_response_code(400);
    }

    public function getChatProfile() {
        global $chatRole, $role;
        $chatRole = $this->checkMembership();
        if ($chatRole || $role == 'ADMIN') {
            global $vars, $chat, $users;
            $chat = $this->chatService->getById($vars[0]);
            $users = $this->chatService->getChatUsers($vars[0]);
            if ($chat != null) {
                return view("chat_profile.php");
            }
        }
        http_response_code(400);
    }
    
    public function getChats() {
        view("chats.php");
    }

    public function getChat() {
        global $vars, $chat, $role;
        if ($this->checkMembership() || $role == 'ADMIN') {
            $chat = $this->chatService->getById($vars[0]);
            if ($chat != null) {
                return view("messages.php");
            }
        }
        http_response_code(400);
    }

    public function updateChat() {
        global $role;
        $chatRole = $this->checkMembership();
        if (!$chatRole && $role != "ADMIN") {
            http_response_code(400);
            return;
        }
        
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $name = $data->name ?: null;
        $description = $data->description ?: null;
        $public = $chatRole == "OWNER" || $role == 'ADMIN' ? $data->public : null;
        global $vars;
        $this->chatService->updateChatData($vars[0], $name, $description, $public);
        header("Location: /chat/profile/" . $vars[0]);
    }

    public function deleteChat() {
        global $role;
        $chatRole = $this->checkMembership();
        if ($chatRole != "OWNER" && $role != 'ADMIN') {
            return http_response_code(400);
        }
        global $vars;
        $this->chatService->delete($vars[0]);
        header("Location: /");
    }

    public function leaveChat() {
        $chatRole = $this->checkMembership();
        if (!$chatRole || $chatRole == "OWNER") {
            return http_response_code(400);
        }
        global $vars, $user_id;
        $this->chatService->leave($vars[0], $user_id);
        header("Location: /");
    }

    public function changeRole() {
        global $role;
        $chatRole = $this->checkMembership();
        if ($chatRole != "OWNER" && $role != "ADMIN") {
            return http_response_code(400);
        }
        if (!isset($_POST["user_id"], $_POST["role"]) || ($_POST["role"] != "ADMIN" && $_POST["role"] != "USER")) {
            return http_response_code(400);
        }
        global $vars;
        $this->chatService->changeRole($vars[0], $_POST["user_id"], $_POST["role"]);
        header("Location: /chat/profile/" . $vars[0]);
    }

    private function checkMembership() {
        global $user_id, $vars;
        return $this->chatService->getChatRole($user_id, $vars[0]);
    }
}