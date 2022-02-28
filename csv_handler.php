<?php

require_once("my_sql_connection.php");

if (isset($_FILES)) {
    #var_dump($_FILES);

    $time = time();
    #Сохранение загружаемого файла
    $uploaddir = 'uploads/';
    $uploadfile = $uploaddir . $time . "_" . basename($_FILES['csv-file']['name']);

    if (move_uploaded_file($_FILES['csv-file']['tmp_name'], $uploadfile)) {
        $mess = "Файл корректен и был успешно загружен.";
    } else {
        $mess = "Произошла ошибка при загрузке файла.";
    }
    #Сохранение загружаемого файла
}

$file = fopen("russian_names.csv", "r");
$data = fgetcsv($file, 100, ",");

#создаем файл csv
$report_file_name = "created/" . $time . "_report.csv";
$fp = fopen($report_file_name, "w");

#задаем имена столбцов, и столбец ошибок
#для отправки $title = $data[0] . ";Error\r\n";
$title = "ID;code;error\r\n";
fwrite($fp, $title);

# Перебор каждой строки в csv-файле и формирование нового
while (($data = fgetcsv($file, 100, ",")) !== FALSE) {
    $item = explode(";", $data[0]);
    $code = $item[0];
    $name = $item[1];

    #Если "Название" - содержит русские и английские буквы, цифры, знак "-" и знак "." 
    if (preg_match('/^[a-zа-яё0-9.-]*$/iu', $name)) {
        # Добавление в базу данных 
        $sql = "INSERT INTO list(code, name) VALUES (?, ?)";
        $query = $pdo->prepare($sql);
        $query->execute([$code, $name]);
        # Формирование csv-строки
        $CSV_str = $code . ";" . $name . "\r\n";
        # Конвертация из utf-8 в cp1251, для корректного отображения киррилицы
        $CSV_str = iconv("UTF-8", "cp1251",  $CSV_str);
        # Запись в файл
        fwrite($fp, $CSV_str);

        //echo "Файлы добавлены!";
        //echo "<br>";
    } else {
        preg_match_all('/[^a-zа-яё0-9.-]/iu', $name, $string);
        var_dump($string);
        $symbol = $string[0];
        if (isset($symbol[1])) {
            $CSV_str = $code . ";" . $name . ";" . "Недопустимые символы\"";
            for ($i = 0; $i < count($symbol); $i++) {
                $CSV_str .= " " . $symbol[$i];
            }
            $CSV_str .= " \" в поле названия\r\n";
        } else {
            $CSV_str = $code . ";" . $name . ";" . "Недопустимый символ\"" . $symbol[0] . "\" в поле названия\r\n";
        }
        # Конвертация из utf-8 в cp1251, для корректного отображения киррилицы
        $CSV_str = iconv("UTF-8", "cp1251",  $CSV_str);
        # Запись в файл
        fwrite($fp, $CSV_str);
        //echo "Ошибка добавления";
        //echo "<br>";
    }
}
#закрытие файла
fclose($fp);
