<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; text-decoration: none; color: #007bff; }
        .flash { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; }
        .flash.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Halo, <?= htmlspecialchars($user['email']) ?>!</h2>
        <p>Selamat datang di Dashboard Admin Gudang.</p>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <div class="nav">
            <a href="products.php">Kelola Produk</a>
            <a href="profile.php">Profil Saya</a>
            <a href="logout.php">Logout</a>
        </div>

        <h3>Apa yang bisa Anda lakukan:</h3>
        <ul>
            <li>Tambah, edit, hapus data produk</li>
            <li>Ubah password dan informasi profil</li>
        </ul>
    </div>
</body>
</html>