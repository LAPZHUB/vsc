<?php
$host = 'localhost';
$db = 'sag';
$user = 'LAPZ_1';
$pass = '+-19851808Ap.';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>