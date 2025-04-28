<?php
session_start();
require 'config.php';

// For simplicity, no admin authentication implemented here
// In production, add proper admin login and access control

$sql = "SELECT t.transaction_number, t.transaction_date, p.stock, p.name AS product_name, t.quantity, t.total_price, t.buyer_contact
        FROM transactions t
        JOIN products p ON t.product_id = p.id
        ORDER BY t.transaction_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Halaman Admin - Transaksi</title>
</head>
<body>
    <h2>Halaman Admin - Daftar Transaksi</h2>
    <p><a href="admin_products.php">Kelola Produk</a> | <a href="admin_users.php">Kelola User</a> | <a href="admin_transactions.php">Kelola Transaksi</a></p>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal Transaksi</th>
                    <th>Stok Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Harga Total</th>
                    <th>Kontak Pembeli</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['transaction_number']); ?></td>
                    <td><?php echo htmlspecialchars($row['transaction_date']); ?></td>
                    <td><?php echo (int)$row['stock']; ?></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo (int)$row['quantity']; ?></td>
                    <td>Rp <?php echo number_format($row['total_price'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($row['buyer_contact']); ?></td>
                    <td>
                        <a href="edit_transaction.php?id=<?php echo $row['transaction_number']; ?>">Edit</a> |
                        <a href="delete_transaction.php?id=<?php echo $row['transaction_number']; ?>" onclick="return confirm('Yakin ingin menghapus transaksi ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada transaksi ditemukan.</p>
    <?php endif; ?>
</body>
</html>
