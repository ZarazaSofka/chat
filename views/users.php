<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Пользователи</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>

    <div class="container mt-5">
        <h2>Пользователи</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Почта</th>
                    <th>Дата регистрации</th>
                    <th>Роль</th>
                    <th>Удаление</th>
                </tr>
            </thead>
            <tbody id='users-list'></tbody>
        </table>
    </div>

    <script>
    function getUsers() {
        fetch('/api/users/get')
            .then(response => response.json())
            .then(users => {
                const usersList = document.getElementById('users-list');
                usersList.innerHTML = '';
                users.forEach(user => {
                    usersList.innerHTML += `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.username}</td>
                                <td>${user.email}</td>
                                <td>${user.action_time}</td>
                                <td>${user.role}</td>
                                <td><button class="btn btn-danger" onclick="deleteUser(${user.id})">Удалить</button></td>
                            </tr>
                        `;
                });
            })
            .catch(error => console.error('Error fetching users:', error));
    }

    function deleteUser(userId) {
        fetch('/api/users/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    user_id: userId
                }),
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                getUsers();
            })
            .catch(error => console.error('Error deleting user:', error));
    }

    getUsers();
    </script>

</body>

</html>