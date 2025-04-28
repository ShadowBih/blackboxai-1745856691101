<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $transaction_number = trim($_POST['transaction_number']);
    $transaction_date = $_POST['transaction_date'];
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $buyer_contact = trim($_POST['buyer_contact']);
    $user_id = $_SESSION['user_id'];

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

            // Insert transaction
            $stmt = $conn->prepare("INSERT INTO transactions (transaction_number, transaction_date, product_id, quantity, total_price, buyer_contact, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiiisi", $transaction_number, $transaction_date, $product_id, $quantity, $total_price, $buyer_contact, $user_id);
            if ($stmt->execute()) {
                // Update product stock
                $new_stock = $stock - $quantity;
                $stmt->close();
                $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE id = ?");
                $stmt->bind_param("ii", $new_stock, $product_id);
                $stmt->execute();
                $message = "Transaksi berhasil. Silakan lakukan pembayaran via transfer bank.";
            } else {
                $message = "Terjadi kesalahan saat menyimpan transaksi.";
            }
        }
    } else {
        $message = "Produk tidak ditemukan.";
    }
    $stmt->close();
}

// Get products for selection
$products_result = $conn->query("SELECT id, name FROM products");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Pembayaran Transfer Bank</title>
</head>
<body>
    <h2>Pembayaran Transfer Bank</h2>
    <p>Silakan transfer ke rekening berikut:</p>
    <p>Bank ABC - No. Rekening: 1234567890 a.n. PT Aluminium</p>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="post" action="payment.php">
        <label>Nomor Transaksi:</label><br />
        <input type="text" name="transaction_number" required /><br />
        <label>Tanggal Transaksi:</label><br />
        <input type="date" name="transaction_date" required /><br />
        <label>Produk:</label><br />
        <select name="product_id" required>
            <option value="">--Pilih Produk--</option>
            <?php while ($row = $products_result->fetch_assoc()): ?>
                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
            <?php endwhile; ?>
        </select><br />
        <label>Jumlah:</label><br />
        <input type="number" name="quantity" min="1" required /><br />
        <label>Kontak Pembeli:</label><br />
        <input type="text" name="buyer_contact" required /><br />
        <label>Pengiriman (Jasa Kurir Kami):</label><br />
        <select name="shipping_option" required>
            <option value="kurir_kami">Kurir Kami</option>
        </select><br /><br />
        <input type="submit" value="Kirim" />
    </form>
</body>
</html>
