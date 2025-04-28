<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT id, name, description, stock, price FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Daftar Produk Aluminium</title>
</head>
<body>
    <h2>Daftar Produk Aluminium</h2>
    <p>Selamat datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! <a href="logout.php">Logout</a></p>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Deskripsi</th>
                    <th>Stok</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo (int)$row['stock']; ?></td>
                    <td>Rp <?php echo number_format($row['price'], 2, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada produk tersedia.</p>
    <?php endif; ?>
</body>
</html>
