<?php
ob_start();
require_once("my_sql_connection.php");

if (isset($_FILES)) {
    #var_dump($_FILES);

    $time = time();
    #Сохранение загружемого файла

    $uploadfile = $time . "_" . basename($_FILES['csv-file']['name']);
    $arr = explode(".", $_FILES['csv-file']['name']);
    $extension = $arr[1];
    $file_name = $arr[0];
    if ($extension === "csv") {
        move_uploaded_file($_FILES['csv-file']['tmp_name'], $uploadfile);
    } else {
        exit("Неподходящий формат файла: $extension[1]");
    }
}

$file = fopen($uploadfile, "r");
$data = fgetcsv($file, null, ",");

#создаем файл csv
$report_file_name = "report_" . $file_name . ".csv";
$new_file = fopen($report_file_name, "w");

#задаем имена столбцов, и столбец ошибок
$title = $data[0] . ";Error\r\n";
//Тест $title = "ID;code;error\r\n";
# Конвертация из utf-8 в cp1251, для корректного отображения киррилицы
$CSV_str = iconv("UTF-8", "cp1251",  $title);
fwrite($new_file, $CSV_str);

# Перебор каждой строки в csv-файле и формирование нового
while (($data = fgetcsv($file, null, ",")) !== FALSE) {
    $item = explode(";", $data[0]);
    var_dump($item);
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
        fwrite($new_file, $CSV_str);

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
            $CSV_str = $code . ";" . $name . ";" . "Недопустимый символ\"";
            $CSV_str .= $symbol[0] . "\" в поле названия\r\n";
        }
        # Конвертация из utf-8 в cp1251, для корректного отображения киррилицы
        $CSV_str = iconv("UTF-8", "cp1251",  $CSV_str);
        # Запись в файл
        fwrite($new_file, $CSV_str);
        //echo "Ошибка добавления";
        //echo "<br>";
    }
}
#закрытие файлов
fclose($file);
fclose($new_file);


if (file_exists($report_file_name)) {
    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
    // если этого не сделать файл будет читаться в память полностью!
    if (ob_get_level()) {
        ob_end_clean();
    }
    // заставляем браузер показать окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($report_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($report_file_name));
    // читаем файл и отправляем его пользователю
    readfile($report_file_name);
    ob_end_flush();
    exit(0);
}
