<?php
require_once 'config.php';

if (!isset($_GET['token'])) {
    setFlash("Token aktivasi tidak valid.", "error");
    redirect('index.php');
}

$token = $_GET['token'];

$stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = ?");
$stmt->execute([$token]);

if ($stmt->rowCount() > 0) {
    setFlash("Akun Anda telah diaktifkan. Silakan login.", "success");
} else {
    setFlash("Token aktivasi tidak valid atau sudah digunakan.", "error");
}

redirect('index.php');