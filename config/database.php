<?php

$conn = pg_connect(
    "host=localhost
     port=5432
     dbname=optical_store
     user=postgres
     password=your_password"
);

if (!$conn) {
    die("Database connection failed.");
}
