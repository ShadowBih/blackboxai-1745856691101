<?php
require 'config.php';

$message = '';

if (!isset($_GET['id'])) {
    die("ID produk tidak ditemukan.");
}

$id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $stock = (int)$_POST['stock'];
    $price = (float)$_POST['price'];

    if ($name && $stock >= 0 && $price >= 0) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, stock = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssidi", $name, $description, $stock, $price, $id);
        if ($stmt->execute()) {
            $message = "Produk berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui produk.";
        }
        $stmt->close();
    } else {
        $message = "Data produk tidak valid.";
    }
}

// Fetch product data
$stmt = $conn->prepare("SELECT name, description, stock, price FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $description, $stock, $price);
if (!$stmt->fetch()) {
    die("Produk tidak ditemukan.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Produk</title>
</head>
<body>
    <h2>Edit Produk</h2>
    <p><a href="admin_products.php">Kembali ke Kelola Produk</a></p>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="edit_product.php?id=<?php echo $id; ?>">
        <label>Nama Produk:</label><br />
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required /><br />
        <label>Deskripsi:</label><br />
        <textarea name="description"><?php echo htmlspecialchars($description); ?></textarea><br />
        <label>Stok:</label><br />
        <input type="number" name="stock" min="0" value="<?php echo (int)$stock; ?>" required /><br />
        <label>Harga:</label><br />
        <input type="number" step="0.01" name="price" min="0" value="<?php echo (float)$price; ?>" required /><br /><br />
        <input type="submit" value="Simpan Perubahan" />
    </form>
</body>
</html>
