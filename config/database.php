<?php

$host = "localhost";
$dbname = "optical_store";
$user = "postgres";
$password = "reena";

$conn = pg_connect(
    "host=$host dbname=$dbname user=$user password=$password"
);

if (!$conn) {
    die("Database Connection Failed");
}
?>