<?php
require_once 'config.php';

if (!isset($_GET['token'])) {
    setFlash("Token tidak valid.", "error");
    redirect('index.php');
}

$token = $_GET['token'];

$stmt = $pdo->prepare("SELECT id FROM users WHERE activation_token = ? AND activation_expiry > NOW()");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    setFlash("Token tidak valid atau sudah kadaluarsa.", "error");
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        setFlash("Password baru tidak cocok.", "error");
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, activation_token = NULL, activation_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        setFlash("Password berhasil diubah. Silakan login.", "success");
        redirect('index.php');
    }
}

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 400px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        input[type="password"] { width: 100%; padding: 8px; }
        button { width: 100%; padding: 10px; background: #28a745; color: white; border: none; cursor: pointer; }
        .flash { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; }
        .flash.error { background: #f8d7da; color: #721c24; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required>
            </div>
            <button type="submit">Simpan Password Baru</button>
        </form>

        <p><a href="index.php">Kembali ke Login</a></p>
    </div>
</body>
</html>