<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    redirect('index.php');
}

// Tambah produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    if ($name && $price >= 0 && $stock >= 0) {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $stock, $_SESSION['user_id']]);
        setFlash("Produk berhasil ditambahkan.", "success");
    } else {
        setFlash("Data produk tidak valid.", "error");
    }
}

// Hapus produk
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND created_by = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    setFlash("Produk berhasil dihapus.", "success");
}

// Ambil semua produk milik user
$stmt = $pdo->prepare("SELECT * FROM products WHERE created_by = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll();

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .container { max-width: 800px; margin: auto; }
        .form-group { margin-bottom: 15px; }
        input, textarea { width: 100%; padding: 8px; }
        button { padding: 8px 15px; background: #007bff; color: white; border: none; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .actions a { margin-right: 10px; color: #007bff; text-decoration: none; }
        .flash { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .flash.success { background: #d4edda; color: #155724; }
        .flash.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kelola Produk</h2>

        <?php if ($flash): ?>
            <div class="flash <?= $flash['type'] ?>"><?= htmlspecialchars($flash['message']) ?></div>
        <?php endif; ?>

        <a href="dashboard.php">&larr; Kembali ke Dashboard</a>

        <h3>Tambah Produk Baru</h3>
        <form method="POST">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"></textarea>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" step="0.01" name="price" min="0" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stock" min="0" required>
            </div>
            <button type="submit" name="add_product">Tambah Produk</button>
        </form>

        <h3>Daftar Produk Anda</h3>
        <?php if (count($products) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']) ?></td>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td><?= htmlspecialchars($p['description']) ?></td>
                            <td>Rp <?= number_format($p['price'], 2) ?></td>
                            <td><?= htmlspecialchars($p['stock']) ?></td>
                            <td class="actions">
                                <!-- Edit & Delete bisa ditambahkan nanti -->
                                <a href="#" onclick="return confirm('Yakin hapus?')" 
                                   title="Hapus" 
                                   style="color:red;">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Belum ada produk. Tambahkan produk pertama Anda!</p>
        <?php endif; ?>
    </div>
</body>
</html>