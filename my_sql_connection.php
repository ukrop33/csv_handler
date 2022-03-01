<?php

$user = 'root';
$password = '';
$host = 'localhost:3307';

$db = 'catalog';

$dsn = 'mysql:host=' . $host . ';dbname=' . $db;
$pdo = new PDO($dsn, $user, $password);
