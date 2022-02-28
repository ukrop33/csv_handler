<?php

if ($_POST["code"]) {
    require_once("my_sql_connection.php");
    #var_dump($_POST);
    $code = $_POST["code"];
    $name = $_POST["name"];
    #var_dump($_POST);
    if (preg_match('/^[a-zа-яё0-9.-]*$/iu', $name)) {
        $sql = "SELECT code FROM list WHERE code = :code";
        $query = $pdo->prepare($sql);
        $query->execute(['code' => $code]);
        $data = $query->fetchObject();
        if ($data->code === $code) {
            $t = date("Y-m-d H:i:s");
            $sql = "UPDATE `list` SET `name` = :name, `updated_at` = :t WHERE `code` = :code";
            $query = $pdo->prepare($sql);
            $query->execute(['code' => $code, 'name' => $name, 't' => $t]);
        } else {
            echo "Записи с таким кодом не существует";
        }
    } else {
        echo "Некорректное имя";
    }
} else {
    echo "Введите данные";
}
echo "Данные обновлены";
