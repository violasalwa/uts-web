<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("UPDATE users SET activation_token = ?, activation_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);

        // Simulasi: tampilkan link reset
        $reset_link = "http://localhost/uts-web/reset-password.php?token=" . urlencode($token);
        setFlash("Link reset password telah dikirim ke email Anda.<br><a href='$reset_link'>Klik di sini untuk reset password</a>", "success");
    } else {
        setFlash("Email tidak ditemukan.", "error");
    }
}

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 400px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        input[type="email"] { width: 100%; padding: 8px; }
        button { width: 100%; padding: 10px; background: #ffc107; color: black; border: none; cursor: pointer; }
        .flash { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; }
        .flash.error { background: #f8d7da; color: #721c24; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>

        <p><a href="index.php">Kembali ke Login</a></p>
    </div>
</body>
</html>