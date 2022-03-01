<?php

$code = $_POST["code"];
$name = $_POST["name"];
settype($code, "integer");
var_dump($code === 0);
if ($code === "" || $code === 0) {
    echo "Не корректный и/или не введен код";
} elseif ($name === "" || !preg_match('/^[a-zа-яё0-9.-]*$/iu', $name)) {
    echo "Не корректное и/или не введено имя";
} else {
    #Приведение поля код в целочисленый формат
    settype($code, "integer");
    require_once("my_sql_connection.php");

    $sql = "SELECT code FROM list WHERE code = :code";
    $query = $pdo->prepare($sql);
    $query->execute(['code' => $code]);
    $data = $query->fetchObject();

    if (isset($data->code) && $data->code === $code) {
        $sql = "UPDATE `list` SET `name` = :name WHERE `code` = :code";
        $query = $pdo->prepare($sql);
        $query->execute(['code' => $code, 'name' => $name]);
        echo "Запись обновлена";
    } else {
        echo "Записи с таким кодом не существует";
    }
}
