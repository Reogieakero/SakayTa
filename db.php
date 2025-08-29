<?php
// db.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db() : mysqli {
    $conn = new mysqli("localhost", "root", "", "sakay_ta");
    $conn->set_charset("utf8mb4");
    return $conn;
}
