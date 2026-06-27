<?php

$conn = pg_connect(
    "host=localhost
     port=5432
     dbname=optical_store
     user=postgres
     password=reena"
);

if (!$conn) {
    die("Database connection failed.");
}