<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php global $chat; echo $chat->name ?></title>
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <div class="mb-4">
            <form method="get" action="/" class="mb-2 w-100">
                <button type="submit" class="btn btn-primary w-100">Главная</button>
            </form>
        </div>

        <div class="card">
            <a href="/chat/profile/<?php echo $chat->id ?>" class="card-header chat-link">
                <h5 class="card-title"><?php echo $chat->name ?></h5>
            </a>
            <div id="message_holder" class="card-body" style="height: 300px; overflow-y: scroll;">

            </div>
            <div class="card-footer">
                <form onsubmit="sendMessage(); return false;">
                    <div class="form-group">
                        <textarea id="messageInput" class="form-control" name="message"
                            placeholder="Введите сообщение здесь..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function getMessages() {
        fetch('/api/chat/messages/<?php echo $chat->id ?>')
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                }
                return response.json();
            })
            .then(messages => {
                const messageHolder = document.getElementById('message_holder');
                messageHolder.innerHTML = '';
                messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('media', 'mb-3');

                    messageDiv.innerHTML = `
                        <div class="media-body">
                            <h5 class="mt-0">${message.username}</h5>
                            <p>${message.text}</p>
                            <small class="text-muted">${message.create_date}</small>
                        </div>
                    `;
                    messageHolder.appendChild(messageDiv);
                });
                messageHolder.scrollTop = messageHolder.scrollHeight;
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    function sendMessage() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value;
        fetch('/api/chat/send/<?php echo $chat->id ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    "text": message,
                }),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                messageInput.value = '';
                getMessages();
            })
            .catch(error => console.error('Error sending message:', error));
    }
    getMessages();
    setInterval(getMessages, 3000);
    </script>
</body>

</html>