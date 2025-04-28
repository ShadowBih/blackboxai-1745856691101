<?php
require 'config.php';

$message = '';

if (!isset($_GET['id'])) {
    die("ID user tidak ditemukan.");
}

$id = (int)$_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($name && $phone && $address && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($password) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, email = ?, password_hash = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $phone, $address, $email, $password_hash, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name = ?, phone = ?, address = ?, email = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $phone, $address, $email, $id);
        }
        if ($stmt->execute()) {
            $message = "User berhasil diperbarui.";
        } else {
            $message = "Gagal memperbarui user.";
        }
        $stmt->close();
    } else {
        $message = "Data user tidak valid.";
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT name, phone, address, email FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $phone, $address, $email);
if (!$stmt->fetch()) {
    die("User tidak ditemukan.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Edit User</title>
</head>
<body>
    <h2>Edit User</h2>
    <p><a href="admin_users.php">Kembali ke Kelola User</a></p>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="post" action="edit_user.php?id=<?php echo $id; ?>">
        <label>Nama:</label><br />
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required /><br />
        <label>Nomor Telepon:</label><br />
        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required /><br />
        <label>Alamat:</label><br />
        <textarea name="address" required><?php echo htmlspecialchars($address); ?></textarea><br />
        <label>Email:</label><br />
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required /><br />
        <label>Password (kosongkan jika tidak ingin mengubah):</label><br />
        <input type="password" name="password" /><br /><br />
        <input type="submit" value="Simpan Perubahan" />
    </form>
</body>
</html>
