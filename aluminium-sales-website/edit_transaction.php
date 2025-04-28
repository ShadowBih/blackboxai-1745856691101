<?php
require 'config.php';

$message = '';

if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan.");
}

$id = $_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_number = trim($_POST['transaction_number']);
    $transaction_date = $_POST['transaction_date'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $buyer_contact = trim($_POST['buyer_contact']);

    // Get product price and stock
    $stmt = $conn->prepare("SELECT price, stock FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($price, $stock);
    if ($stmt->fetch()) {
        if ($quantity > $stock) {
            $message = "Jumlah melebihi stok yang tersedia.";
        } else {
            $total_price = $price * $quantity;
            $stmt->close();

            // Update transaction
            $stmt = $conn->prepare("UPDATE transactions SET transaction_number = ?, transaction_date = ?, product_id = ?, quantity = ?, total_price = ?, buyer_contact = ? WHERE transaction_number = ?");
            $stmt->bind_param("ssiiiss", $transaction_number, $transaction_date, $product_id, $quantity, $total_price, $buyer_contact, $id);
            if ($stmt->execute()) {
                $message = "Transaksi berhasil diperbarui.";
            } else {
                $message = "Gagal memperbarui transaksi.";
            }
        }
    } else {
        $message = "Produk tidak ditemukan.";
    }
    $stmt->close();
}

// Fetch transaction data
$stmt = $conn->prepare("SELECT transaction_number, transaction_date, product_id, quantity, buyer_contact FROM transactions WHERE transaction_number = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->bind_result($transaction_number, $transaction_date, $product_id, $quantity, $buyer_contact);
if (!$stmt->fetch()) {
    die("Transaksi tidak ditemukan.");
}
$stmt->close();

// Fetch products for selection
$products_result = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit Transaksi</title>
</head>
<body>
    <h2>Edit Transaksi</h2>
    <p><a href="admin_transactions.php">Kembali ke Kelola Transaksi</a></p>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="edit_transaction.php?id=<?php echo htmlspecialchars($id); ?>">
        <label>Nomor Transaksi:</label><br />
        <input type="text" name="transaction_number" value="<?php echo htmlspecialchars($transaction_number); ?>" required /><br />
        <label>Tanggal Transaksi:</label><br />
        <input type="date" name="transaction_date" value="<?php echo htmlspecialchars($transaction_date); ?>" required /><br />
        <label>Produk:</label><br />
        <select name="product_id" required>
            <?php while ($row = $products_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>" <?php if ($row['id'] == $product_id) echo 'selected'; ?>><?php echo htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select><br />
        <label>Jumlah:</label><br />
        <input type="number" name="quantity" min="1" value="<?php echo (int)$quantity; ?>" required /><br />
        <label>Kontak Pembeli:</label><br />
        <input type="text" name="buyer_contact" value="<?php echo htmlspecialchars($buyer_contact); ?>" required /><br /><br />
        <input type="submit" value="Simpan Perubahan" />
    </form>
</body>
</html>
