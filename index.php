<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание</title>
</head>

<body>
    <form action = "/csv_handler.php" enctype="multipart/form-data" method="post">
        <h1>Загрузка файла</h1>
        <input type="file" name="csv-file" accept=".csv">
        <br> <br>
        <button type="submit">Загрузить</button>
    </form>
    <br><br>
    <form action="/update.php" method="post">
        <h1>Обновление по полю "Код"</h1>
        <input type="text" name="code" placeholder="Код">
        <input type="text" name="name" placeholder="Название">
        <br> <br>
        <button type="submit">Обновить</button>
    </form>
</body>

</html>