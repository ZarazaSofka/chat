<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    .chat-link {
        color: inherit;
        text-decoration: none;
        cursor: pointer;
    }

    .chat-link:hover {
        text-decoration: none;
    }
    </style>
    <title>Чаты</title>
</head>

<body>
    <div class="container mt-4">
        <div class="mb-4">
            <form method="get" action="/logout" class="mb-2 w-100">
                <button type="submit" class="btn btn-danger w-100">Выйти</button>
            </form>
        </div>
        <div class="container border border-info rounded p-4">
            <form onsubmit="getUserChats(); return false;" class="mb-2 w-100">
                <button type="submit" class="btn btn-primary w-100">Мои чаты</button>
            </form>
            <form onsubmit="getPublicChats(); return false;" class="mb-2 w-100">
                <button type="submit" class="btn btn-success w-100">Публичные чаты</button>
            </form>
        </div>
        <h1 id="chats_header">Ваши чаты</h1>
        <div id="chats_holder" class="container mt-4">

        </div>
    </div>
    <div class="container mt-4 mb-4 border border-info rounded p-4">
        <form onsubmit="joinChat(document.getElementById('chat_id').value); return false;" class="mt-4">
            <label for="chat_id" class="form-label">ID:</label>
            <input type="number" id="chat_id" name="chat_id" min="1" class="form-control">

            <button type="submit" class="btn btn-primary mt-4">Присоединиться к чату</button>
        </form>
    </div>
    <div class="container mt-4 mb-4 border border-info rounded p-4">
        <form onsubmit="createChat(); return false;" class="mt-4">
            <label for="chat_name" class="form-label">Название:</label>
            <input type="text" id="chat_name" name="name" maxlength="50" class="form-control">

            <label for="chat_public" class="form-label mt-3">Публичность:</label>
            <select id="chat_public" name="public" class="form-select">
                <option value="1">Да</option>
                <option value="0">Нет</option>
            </select>

            <label for="chat_description" class="form-label mt-3">Описание:</label>
            <input type="text" id="chat_description" name="description" maxlength="100" class="form-control">

            <button type="submit" class="btn btn-success mt-4">Создать чат</button>
        </form>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        getUserChats();
    });

    async function getUserChats() {
        try {
            const response = await fetch('/api/chats/user', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch user chats');
            }

            const data = await response.json();
            document.getElementById('chats_header').innerText = 'Ваши чаты';
            const chatsHolder = document.getElementById('chats_holder');
            chatsHolder.innerHTML = '';

            data.forEach(chat => {
                const chatCard = document.createElement('div');
                chatCard.className = 'card mb-3';

                const chatLink = document.createElement('a');
                chatLink.href = `chat/${chat.id}`;
                chatLink.className = 'chat-link';

                const cardBody = document.createElement('div');
                cardBody.className = 'card-body';

                const cardTitle = document.createElement('h5');
                cardTitle.className = 'card-title';
                cardTitle.textContent = chat.name;

                cardBody.appendChild(cardTitle);

                chatLink.appendChild(cardBody);
                chatCard.appendChild(chatLink);

                chatsHolder.appendChild(chatCard);
            });
        } catch (error) {
            console.error('Error fetching user chats:', error.message);
        }
    }

    async function getPublicChats() {
        try {
            const response = await fetch('/api/chats/public', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch public chats');
            }

            const data = await response.json();

            document.getElementById('chats_header').innerText = 'Публичные чаты';
            const chatsHolder = document.getElementById('chats_holder');
            chatsHolder.innerHTML = '';

            data.forEach(chat => {
                const chatCard = document.createElement('div');
                chatCard.className = 'card mb-3';

                const cardBody = document.createElement('div');
                cardBody.className = 'card-body';

                const cardTitle = document.createElement('h5');
                cardTitle.className = 'card-title';
                cardTitle.textContent = chat.name;

                const cardJoin = document.createElement('p');
                cardJoin.innerHTML = `<form onsubmit="joinChat(${chat.id}); return false;">
                <button type="submit" class="btn btn-primary w-100"> Присоединиться </button>
                </form>`

                cardBody.appendChild(cardTitle);
                cardBody.appendChild(cardJoin);

                chatCard.appendChild(cardBody);

                chatsHolder.appendChild(chatCard);
            });
        } catch (error) {
            console.error('Error fetching public chats:', error.message);
        }
    }

    async function joinChat(chatId) {
        try {
            const response = await fetch(`/api/chat/join/${chatId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to join chat');
            }

            getUserChats();
        } catch (error) {
            console.error('Error joining chat:', error.message);
        }
    }

    async function createChat() {
        try {
            const name = document.getElementById('chat_name').value;
            const isPublic = document.getElementById('chat_public').value;
            const description = document.getElementById('chat_description').value;

            const response = await fetch('/api/chat/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    name: name,
                    public: isPublic,
                    description: description,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to create chat');
            }
            getUserChats();
        } catch (error) {
            console.error('Error creating chat:', error.message);
        }
    }
    </script>
</body>

</html>