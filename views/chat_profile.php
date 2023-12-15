<?php
global $chat, $chatRole, $users;
function checkRole($role) {
    switch ($role) {
    case 'USER':
        echo 'Участник';
        break;
    case 'ADMIN':
        echo 'Администратор';
        break;
    case 'OWNER':
        echo 'Владелец';
        break;
    default:
        echo 'Неопределенная роль';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Чат</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <div class="mb-4">
            <form method="get" action="/" class="mb-2 w-100">
                <button type="submit" class="btn btn-primary w-100">Главная</button>
            </form>
            <form method="get" action="/chat/<?php echo $chat->id; ?>" class="mb-2 w-100">
                <button type="submit" class="btn btn-primary w-100">Чат</button>
            </form>
        </div>


        <div class="card mb-5">
            <div class="card-header">
                <h5 class="card-title"><?php echo $chat->name; ?></h5>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo $chat->description; ?></p>
                <p class="card-text"><strong>ID:</strong> <?php echo $chat->id; ?></p>
                <p class="card-text"><strong>Публичный:</strong> <?php echo $chat->public ? 'Да' : 'Нет'; ?></p>
                <form method="post" <?php if ($chatRole == "OWNER") {
                    echo 'action="/api/chat/delete/' . $chat->id . '">';
                    echo '<button type="submit" class="btn btn-danger">Удалить чат</button>';
                    } else {
                        echo 'action="/api/chat/leave/' . $chat->id . '">';
                        echo '<button type="submit" class="btn btn-danger">Выйти из чата</button>';
                    }
                    ?> </form>
            </div>
        </div>
        <?php if ($chatRole == "ADMIN" || $chatRole == "OWNER") : ?>
        <div class="card mb-5">
            <div class="card-header">
                <h5 class="card-title">Edit Profile</h5>
            </div>
            <div class="card-body">
                <form onsubmit="update(); return false;">
                    <div class="form-group">
                        <label for="chatName">Название</label>
                        <input type="text" class="form-control" id="chatName" value="<?php echo $chat->name; ?>">
                    </div>
                    <div class="form-group">
                        <label for="chatDescription">Описание</label>
                        <textarea class="form-control" id="chatDescription"><?php echo $chat->description; ?></textarea>
                    </div>
                    <?php if ($chatRole == "OWNER") : ?>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="publicCheckbox"
                            <?php echo $chat->public ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="publicCheckbox">Публичность</label>
                    </div>
                    <?php endif ?>
                    <button type="submit" class="btn btn-primary mt-3">Сохранить</button>
                </form>
            </div>
        </div>
        <?php endif ?>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Участники</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Логин</th>
                            <th>Роль</th>
                            <?php if ($chatRole == "OWNER") : ?>
                            <th>Действия</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user->username; ?></td>
                            <td><?php checkRole($user->chatRole); ?></td>
                            <?php if ($chatRole == "OWNER") : ?>
                            <td>
                                <form method="post" action="/api/chat/changerole/<?php echo $chat->id ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user->id ?>">
                                    <?php if ($user->chatRole == "USER") : ?>
                                    <input type="hidden" name="role" value="ADMIN">
                                    <button type="submit" class="btn btn-success">Повысить</button>
                                    <?php elseif ($user->chatRole == "ADMIN") : ?>
                                    <input type="hidden" name="role" value="USER">
                                    <button type="submit" class="btn btn-warning">Понизить</button>
                                    <?php endif; ?>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
    function update() {
        const chatName = document.getElementById('chatName').value;
        const chatDescription = document.getElementById('chatDescription').value;
        const publicCheckbox = document.getElementById('publicCheckbox');

        fetch('/api/chat/update/<?php echo $chat->id ?>', {
                method: 'POST',
                body: JSON.stringify({
                    "name": chatName,
                    "description": chatDescription,
                    "public": publicCheckbox ? publicCheckbox.checked : null
                }),
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                }
                return response.json();
            })
            .then(data => {
                console.log('Update successful:', data);
            })
            .catch(error => console.error('Error updating chat:', error));
    }
    </script>

</body>

</html>