<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Вход</h1>
        <div class="card mx-auto" style="max-width: 400px;">
            <div class="card-body">
                <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_GET['error']; ?>
                </div>
                <?php endif; ?>
                <form method="post" action="/api/user/login">
                    <div class="form-group">
                        <label for="username">Логин:</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Войти</button>
                </form>

                <p class="mt-3 text-center">Нет аккаунта?<a href="/register">Зарегистрируйся</a></p>
            </div>
        </div>
    </div>
</body>

</html>