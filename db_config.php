<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'tilit_db');
define('DB_USER', 'postgres');
define('DB_PASSWORD', 'salasana');

function get_db_connection()
{
    $conn_string = sprintf(
        "host=%s port=%s dbname=%s user=%s password=%s",
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_USER,
        DB_PASSWORD
    );
    $conn = pg_connect($conn_string);
    if (!$conn) {
        die('Tietokantayhteyden avaaminen epäonnistui: ' . pg_last_error());
    }
    return $conn;
}

