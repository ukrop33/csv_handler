<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Тестовое задание</title>
</head>

<body>
    <form enctype="multipart/form-data" method="post">
        <h1>Загрузка файла</h1>
        <input type="file" name="csv-file" accept=".csv">
        <br> <br>
        <button type="submit">Отправить</button>
        <?php
        var_dump($_FILES);

        #Сохранение загружаемого файла
        $uploaddir = '/uploads';
        $uploadfile = $uploaddir . basename($_FILES['csv-file']['name']);

        if (move_uploaded_file($_FILES['csv-file']['tmp_name'], $uploadfile)) {
            $message = "Файл корректен и был успешно загружен.";
        } else {
            $message = "Произошла ошибка при загрузке файла.";
        }
        #Сохранение загружаемого файла


        ?>


        <!-- Вывод информации -->
        <p> <?=$message?> </p>
    </form>
</body>

</html>