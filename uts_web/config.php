<?php
// config.php

session_start();

$host = 'localhost';
$dbname = 'uts_web';
$username = 'root'; // ganti jika pakai user lain
$password = '';     // ganti jika ada password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Fungsi bantu: redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi bantu: flash message
function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}