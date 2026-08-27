<?php
$mysqli = new mysqli("localhost", "root", "root", "SQL入門");

if ($mysqli->connect_error) {
    die("DB接続失敗: " . $mysqli->connect_error);
}
?>