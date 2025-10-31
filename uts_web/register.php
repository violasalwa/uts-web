<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        setFlash("Password tidak cocok.", "error");
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            setFlash("Email sudah terdaftar.", "error");
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            $stmt = $pdo->prepare("INSERT INTO users (email, password, activation_token) VALUES (?, ?, ?)");
            $stmt->execute([$email, $hashed_password, $token]);

            // Simulasi: tampilkan link aktivasi
            $activation_link = "http://localhost/uts-web/activate.php?token=" . urlencode($token);
            setFlash("Registrasi berhasil! Silakan aktivasi akun Anda.<br><a href='$activation_link'>Klik di sini untuk aktivasi</a>", "success");
        }
    }
}

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 400px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .flash { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; }
        .flash.error { background: #f8d7da; color: #721c24; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Daftar Admin Gudang</h2>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="index.php">Login di sini</a></p>
    </div>
</body>
</html>