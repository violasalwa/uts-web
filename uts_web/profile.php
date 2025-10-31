<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('index.php');
}

// Update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_email = trim($_POST['email']);
    $current_password = $_POST['current_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($current_password, $user['password'])) {
        setFlash("Password saat ini salah.", "error");
    } else {
        // Cek apakah email baru sudah dipakai orang lain
        if ($new_email !== $user['email']) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$new_email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                setFlash("Email baru sudah digunakan.", "error");
            } else {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$new_email, $_SESSION['user_id']]);
                $_SESSION['email'] = $new_email;
                setFlash("Email berhasil diperbarui.", "success");
            }
        } else {
            setFlash("Email tidak berubah.", "info");
        }
    }
}

// Ubah password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!password_verify($current_password, $user['password'])) {
        setFlash("Password saat ini salah.", "error");
    } elseif ($new_password !== $confirm_password) {
        setFlash("Password baru tidak cocok.", "error");
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        setFlash("Password berhasil diubah.", "success");
    }
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
    <title>Profil Saya</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 500px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        .flash { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; }
        .flash.error { background: #f8d7da; color: #721c24; }
        .section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profil Saya</h2>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <a href="dashboard.php">&larr; Kembali ke Dashboard</a>

        <div class="section">
            <h3>Ubah Email</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Email Saat Ini</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Email Baru</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" required>
                </div>
                <button type="submit" name="update_profile">Perbarui Email</button>
            </form>
        </div>

        <div class="section">
            <h3>Ubah Password</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" name="current_password" required>
                </div>
                <div clas   s="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password">Ganti Password</button>
            </form>
        </div>
    </div>
</body>
</html>