<?php
require 'config.php';

$message = '';

// Handle delete product
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = "Produk berhasil dihapus.";
    } else {
        $message = "Gagal menghapus produk.";
    }
    $stmt->close();
}

// Handle add product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $stock = (int)$_POST['stock'];
    $price = (float)$_POST['price'];

    if ($name && $stock >= 0 && $price >= 0) {
        $stmt = $conn->prepare("INSERT INTO products (name, description, stock, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $name, $description, $stock, $price);
        if ($stmt->execute()) {
            $message = "Produk berhasil ditambahkan.";
        } else {
            $message = "Gagal menambahkan produk.";
        }
        $stmt->close();
    } else {
        $message = "Data produk tidak valid.";
    }
}

// Fetch products
$result = $conn->query("SELECT * FROM products");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Kelola Produk</title>
</head>
<body>
    <h2>Kelola Produk</h2>
    <p><a href="admin.php">Kembali ke Halaman Admin</a></p>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <h3>Tambah Produk Baru</h3>
    <form method="post" action="admin_products.php">
        <input type="hidden" name="add_product" value="1" />
        <label>Nama Produk:</label><br />
        <input type="text" name="name" required /><br />
        <label>Deskripsi:</label><br />
        <textarea name="description"></textarea><br />
        <label>Stok:</label><br />
        <input type="number" name="stock" min="0" required /><br />
        <label>Harga:</label><br />
        <input type="number" step="0.01" name="price" min="0" required /><br /><br />
        <input type="submit" value="Tambah Produk" />
    </form>

    <h3>Daftar Produk</h3>
    <?php if ($result->num_rows > 0): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Deskripsi</th>
                    <th>Stok</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo (int)$row['stock']; ?></td>
                    <td>Rp <?php echo number_format($row['price'], 2, ',', '.'); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="admin_products.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus produk ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada produk ditemukan.</p>
    <?php endif; ?>
</body>
</html>
