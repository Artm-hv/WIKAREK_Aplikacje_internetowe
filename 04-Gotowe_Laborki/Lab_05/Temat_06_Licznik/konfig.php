<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'student');
define('DB_NAME', 'test');
define('TBL_PREFIX', 'T_3ID11B_01_');
define('SESSION_LIFETIME', 30);
define('ONLINE_WINDOW_MIN', 5);
define('DEBOUNCE_WINDOW_MIN', 15);

function get_db_connection() {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        $conn = @new mysqli(DB_HOST, DB_USER, '', DB_NAME);
    }
    if ($conn->connect_error) {
        die('Błąd połączenia z bazą danych: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
